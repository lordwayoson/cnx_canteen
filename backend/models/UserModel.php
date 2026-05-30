<?php
declare(strict_types=1);

namespace Canteen\Models;

use PDO;
use PDOException;

final class UserModel
{
    private bool $hasCreatedAt;
    private bool $hasUpdatedAt;
    private string $roleColumn = 'role';

    public function __construct(private PDO $pdo)
    {
        // Some MySQL users have limited INFORMATION_SCHEMA visibility; treat missing
        // metadata lookups as “column absent” instead of throwing and breaking the
        // entire user management UI.
        $this->hasCreatedAt = $this->columnExists('admin_user', 'created_at');
        $this->hasUpdatedAt = $this->columnExists('admin_user', 'updated_at');

        // Resolve the role column using safe detection with a legacy fallback.
        if (!$this->columnExists('admin_user', $this->roleColumn) && $this->columnExists('admin_user', 'user_role')) {
            $this->roleColumn = 'user_role';
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            $stmt = $this->pdo->prepare('SHOW COLUMNS FROM ' . $table . ' LIKE :column');
            $stmt->execute(['column' => $column]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException) {
            // If metadata access is restricted, fall back to “false” so we avoid fatals.
            return false;
        }
    }

    private function normalizeRole(string $role): string
    {
        $role = strtolower($role);
        return in_array($role, ['admin', 'kitchen'], true) ? $role : 'kitchen';
    }

    public function findAdminByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare(sprintf('SELECT id, username, password_hash, LOWER(%s) AS role FROM admin_user WHERE username = :username LIMIT 1', $this->roleColumn));
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findAdminById(int $id): ?array
    {
        $columns = ['id', 'username', sprintf('LOWER(%s) AS role', $this->roleColumn)];
        if ($this->hasCreatedAt) {
            $columns[] = 'created_at';
        }
        if ($this->hasUpdatedAt) {
            $columns[] = 'updated_at';
        }
        $sql = sprintf('SELECT %s FROM admin_user WHERE id = :id LIMIT 1', implode(', ', $columns));
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }

        // Expose both role keys to simplify API consumers regardless of column name.
        if (!isset($user['user_role'])) {
            $user['user_role'] = $user['role'] ?? null;
        }

        return $user;
    }

    public function createAdminUser(string $username, string $passwordHash, string $role): int
    {
        $normalizedRole = $this->normalizeRole($role);
        $stmt = $this->pdo->prepare(sprintf('INSERT INTO admin_user (username, password_hash, %s) VALUES (:username, :password_hash, :role)', $this->roleColumn));
        $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash,
            'role' => $normalizedRole,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateAdminRole(int $id, string $role): bool
    {
        $stmt = $this->pdo->prepare(sprintf('UPDATE admin_user SET %s = :role WHERE id = :id', $this->roleColumn));
        return $stmt->execute([
            'role' => $this->normalizeRole($role),
            'id' => $id,
        ]);
    }

    public function updateAdminPassword(int $id, string $hash): bool
    {
        $stmt = $this->pdo->prepare('UPDATE admin_user SET password_hash = :hash WHERE id = :id');
        return $stmt->execute([
            'hash' => $hash,
            'id' => $id,
        ]);
    }

    public function deleteAdminUser(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM admin_user WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM admin_user WHERE username = :username';
        $params = ['username' => $username];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :exclude';
            $params['exclude'] = $excludeId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function allAdminUsers(): array
    {
        $columns = ['id', 'username', sprintf('LOWER(%s) AS role', $this->roleColumn)];
        if ($this->hasCreatedAt) {
            $columns[] = 'created_at';
        }
        if ($this->hasUpdatedAt) {
            $columns[] = 'updated_at';
        }
        $sql = sprintf('SELECT %s FROM admin_user ORDER BY id DESC', implode(', ', $columns));
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function upsertUser(array $payload): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO user (userid, username, name, lastname, email, project, cardnumber)
            VALUES (:userid, :username, :name, :lastname, :email, :project, :cardnumber)
            ON DUPLICATE KEY UPDATE username = VALUES(username), name = VALUES(name), lastname = VALUES(lastname), email = VALUES(email), project = VALUES(project), cardnumber = VALUES(cardnumber)');
        $stmt->execute([
            'userid' => (int) $payload['userid'],
            'username' => $payload['username'],
            'name' => $payload['name'],
            'lastname' => $payload['lastname'],
            'email' => $payload['email'] ?? null,
            'project' => $payload['project'] ?? null,
            'cardnumber' => $payload['cardnumber'] ?? null,
        ]);
    }

    public function findByCardOrUserId(?string $cardnumber, ?int $userid): ?array
    {
        if ($userid !== null) {
            $stmt = $this->pdo->prepare('SELECT * FROM user WHERE userid = :userid LIMIT 1');
            $stmt->execute(['userid' => $userid]);
        } elseif ($cardnumber !== null) {
            $stmt = $this->pdo->prepare('SELECT * FROM user WHERE cardnumber = :cardnumber LIMIT 1');
            $stmt->execute(['cardnumber' => $cardnumber]);
        } else {
            return null;
        }
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM user ORDER BY lastname, name');
        return $stmt->fetchAll();
    }
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
