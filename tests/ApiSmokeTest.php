<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ApiSmokeTest extends TestCase
{
    public function testAuthEndpointExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../backend/api/auth/login.php');
    }

    public function testQueueEndpointExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../backend/api/queue/serve.php');
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
