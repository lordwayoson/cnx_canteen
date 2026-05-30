(function () {
  // Resolve the auth API base as robustly as possible across Apache/XAMPP setups
  const apiBase = (() => {
    const candidates = [];
    if (window.AUTH_API_BASE) {
      candidates.push(window.AUTH_API_BASE.replace(/\/$/, ''));
    }
    const bodyAbs = document.body?.dataset?.authBaseAbs;
    if (bodyAbs) {
      candidates.push(bodyAbs.replace(/\/$/, ''));
    }
    const bodyBase = document.body?.dataset?.authBase;
    if (bodyBase) {
      candidates.push(bodyBase.replace(/\/$/, ''));
    }
    if (window.CANTEEN_BACKEND_API_URL) {
      candidates.push(`${window.CANTEEN_BACKEND_API_URL.replace(/\/$/, '')}/auth`);
    }
    if (window.CANTEEN_BACKEND_API_BASE) {
      candidates.push(`${window.location.origin}${window.CANTEEN_BACKEND_API_BASE.replace(/\/$/, '')}/auth`);
    }
    if (window.CANTEEN_BACKEND_BASE) {
      candidates.push(`${window.location.origin}${window.CANTEEN_BACKEND_BASE.replace(/\/$/, '')}/api/auth`);
    }
    // Common defaults for local installs
    candidates.push(`${window.location.origin}/canteen-system/backend/api/auth`);
    candidates.push(`${window.location.origin}/backend/api/auth`);
    candidates.push('/backend/api/auth');
    const winner = candidates.find((c) => typeof c === 'string' && c.length > 0);
    return winner || '/backend/api/auth';
  })();

  function showTableMessage(message) {
    const tbody = document.querySelector('#user-table tbody');
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${message}</td></tr>`;
    }
  }

  function showAlert(elementId, message, variant = 'success') {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.className = `alert alert-${variant}`;
    el.textContent = message;
    el.classList.remove('d-none');
  }

  async function request(path, options = {}) {
    const url = `${apiBase}/${path}`;
    const isGet = !options.method || options.method.toUpperCase() === 'GET';
    const fetchOptions = {
      credentials: 'include',
      ...(isGet ? {} : { headers: { 'Content-Type': 'application/json' } }),
      ...options
    };
    const response = await fetch(url, fetchOptions);
    let data;
    try {
      data = await response.json();
    } catch (err) {
      throw new Error('Invalid server response');
    }
    if (!response.ok || data.success === false) {
      throw new Error(data.error || 'Request failed');
    }
    return data;
  }

  let currentEditUserId = null;
  let currentDeleteUserId = null;

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
  //function disablePasswordFields() {
    // Password fields are always enabled; keep placeholder for legacy calls.
  //}
=======
  function disablePasswordFields() {
    // Password fields are always enabled; keep placeholder for legacy calls.
  }
>>>>>>> theirs
=======
  function disablePasswordFields() {
    // Password fields are always enabled; keep placeholder for legacy calls.
  }
>>>>>>> theirs
=======
  function disablePasswordFields() {
    // Password fields are always enabled; keep placeholder for legacy calls.
  }
>>>>>>> theirs

  function renderUsers(users) {
    const tbody = document.querySelector('#user-table tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (!users.length) {
      showTableMessage('No users found');
      return;
    }
    users.forEach((user) => {
      const created = user.created_at || '';
      const updated = user.updated_at || '';
      const roleLabel = (user.role || '').toLowerCase() === 'admin' ? 'Admin' : 'Kitchen';
      const roleBadge = roleLabel === 'Admin' ? 'bg-primary' : 'bg-secondary';
      const tr = document.createElement('tr');
      tr.dataset.userId = user.id ?? '';
      tr.innerHTML = `
        <td class="text-muted fw-semibold">${user.id ?? ''}</td>
        <td>${user.username || ''}</td>
        <td><span class="badge ${roleBadge}">${roleLabel}</span></td>
        <td>${created}</td>
        <td>${updated}</td>
        <td class="text-end">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary btn-edit-user" data-bs-toggle="modal" data-bs-target="#editUserModal" data-action="edit" data-id="${user.id}" data-user-id="${user.id}" data-username="${user.username}" data-role="${roleLabel.toLowerCase()}">Edit</button>
            <button type="button" class="btn btn-outline-danger btn-delete-user" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-action="delete" data-id="${user.id}" data-user-id="${user.id}" data-username="${user.username}">Delete</button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  async function loadUserList() {
    const table = document.getElementById('user-table');
    if (!table) return;
    const tbody = table.querySelector('tbody');
    const original = tbody ? tbody.innerHTML : '';
    const loader = document.getElementById('user-table-loader');
    if (loader) loader.classList.remove('d-none');
    try {
      const { data } = await request('listUsers.php').catch(async (err) => {
        console.warn('listUsers.php failed, trying getUsers.php fallback', err);
        const legacy = await request('getUsers.php');
        return { data: legacy.data };
      });
      renderUsers(data || []);
    } catch (error) {
      // Preserve any server-rendered rows so the page still shows data even if the
      // AJAX call fails (e.g., due to path issues in nested deployments).
      if (tbody && original.trim()) {
        tbody.innerHTML = original;
      } else {
        showTableMessage(error.message || 'Failed to load users');
      }
      showAlert('user-alert', error.message || 'Failed to load users', 'danger');
    } finally {
      if (loader) loader.classList.add('d-none');
    }
  }

  async function submitCreateUserForm(event) {
    event.preventDefault();
    const form = event.target;
    const payload = Object.fromEntries(new FormData(form).entries());
    payload.role = (payload.role || 'kitchen').toLowerCase();
    try {
      await request('register.php', { method: 'POST', body: JSON.stringify(payload) });
      showAlert('create-alert', 'User created successfully', 'success');
      form.reset();
      // Redirect to the list so actions are immediately available even if JS is limited.
      setTimeout(() => {
        window.location.href = 'index.php?created=1';
      }, 500);
    } catch (error) {
      showAlert('create-alert', error.message || 'Failed to create user', 'danger');
    }
  }

  async function submitEditUserForm(event) {
    event?.preventDefault?.();
    const form = event?.target instanceof HTMLFormElement ? event.target : document.getElementById('edit-user-form');
    if (!form) return;

    const payload = Object.fromEntries(new FormData(form).entries());
    payload.id = Number(payload.id || currentEditUserId || 0);
    payload.role = (payload.role || '').toLowerCase();

    if (!payload.id) {
      alert('No user selected to update');
      return;
    }

    const pwdValue = (payload.password || '').trim();
    const pwd2Value = (payload.confirm_password || '').trim();

    if (pwdValue === '' && pwd2Value === '') {
      delete payload.password;
      delete payload.confirm_password;
    } else {
      if (pwdValue === '' || pwd2Value === '') {
        alert('Both password fields are required to change the password');
        return;
      }
      if (pwdValue.length < 8) {
        alert('Password must be at least 8 characters');
        return;
      }
      if (pwdValue !== pwd2Value) {
        alert('Passwords do not match');
        return;
      }
      payload.password = pwdValue;
      payload.confirm_password = pwd2Value;
    }

    try {
      await request('updateUser.php', { method: 'POST', body: JSON.stringify(payload) });
      hideModalById('editUserModal');
      form.reset();
      loadUserList();
    } catch (error) {
      alert(error.message);
    }
  }

  async function deleteUser(id) {
    try {
      await request('deleteUser.php', { method: 'POST', body: JSON.stringify({ id }) });
      hideModalById('deleteUserModal');
      loadUserList();
    } catch (error) {
      alert(error.message);
    }
  }

  function showModalById(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    if (window.bootstrap?.Modal) {
      new bootstrap.Modal(modalEl).show();
    } else {
      // Bootstrap fallback: simple class toggle
      modalEl.classList.add('show');
      modalEl.style.display = 'block';
    }
  }

  function hideModalById(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    if (window.bootstrap?.Modal) {
      const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      inst.hide();
    } else {
      modalEl.classList.remove('show');
      modalEl.style.display = 'none';
    }
  }

  function extractUserIdFromButton(btn) {
    if (!btn) return 0;
    // Prefer explicit data-user-id on the button.
    const direct = Number(btn.dataset.userId || btn.dataset.id || 0);
    if (direct) return direct;
    // Otherwise walk up to the nearest row.
    const row = btn.closest('tr');
    if (row?.dataset?.userId) return Number(row.dataset.userId);
    // Fallback: pull from the first cell text.
    const firstCell = row?.querySelector('td');
    if (firstCell) {
      const numeric = parseInt((firstCell.textContent || '').replace(/[^0-9]/g, ''), 10);
      return Number.isNaN(numeric) ? 0 : numeric;
    }
    return 0;
  }

  async function hydrateEditUser(id) {
    try {
      const { data } = await request(`getUser.php?id=${id}`);
      const display = document.getElementById('edit-username-display');
      const roleSelect = document.getElementById('edit-role');
      const idField = document.getElementById('edit-id');
      if (idField) idField.value = String(data.id || id);
      if (display) display.textContent = data.username || '';
      const normalizedRole = (data.role || data.user_role || 'kitchen').toLowerCase();
      if (roleSelect) roleSelect.value = normalizedRole;
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
      //disablePasswordFields();
=======
      disablePasswordFields();
>>>>>>> theirs
=======
      disablePasswordFields();
>>>>>>> theirs
=======
      disablePasswordFields();
>>>>>>> theirs
    } catch (error) {
      alert(error.message || 'Failed to load user');
    }
  }

  function populateEditFromButton(btn) {
    const id = extractUserIdFromButton(btn);
    if (!id) return;
    currentEditUserId = id;
    const idField = document.getElementById('edit-id');
    if (idField) idField.value = String(id);
    const display = document.getElementById('edit-username-display');
    if (display) display.textContent = btn.dataset.username || '';
    const roleSelect = document.getElementById('edit-role');
    if (roleSelect) roleSelect.value = (btn.dataset.role || 'kitchen').toLowerCase();
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    //disablePasswordFields();
=======
    disablePasswordFields();
>>>>>>> theirs
=======
    disablePasswordFields();
>>>>>>> theirs
=======
    disablePasswordFields();
>>>>>>> theirs
    hydrateEditUser(id);
  }

  function populateDeleteFromButton(btn) {
    const id = extractUserIdFromButton(btn);
    if (!id) return;
    currentDeleteUserId = id;
    const username = btn.dataset.username || '';
    const idField = document.getElementById('delete-user-id');
    const nameSpan = document.getElementById('delete-username');
    const idDisplay = document.getElementById('delete-user-id-display');
    if (idField) idField.value = String(id);
    if (nameSpan) nameSpan.textContent = username;
    if (idDisplay) idDisplay.textContent = String(id);
  }

  function bindTableActions() {
    document.addEventListener('click', (event) => {
      const editBtn = event.target instanceof HTMLElement ? event.target.closest('.btn-edit-user') : null;
      const deleteBtn = event.target instanceof HTMLElement ? event.target.closest('.btn-delete-user') : null;

      if (editBtn) {
        populateEditFromButton(editBtn);
        if (!window.bootstrap?.Modal) {
          showModalById('editUserModal');
        }
        return;
      }

      if (deleteBtn) {
        populateDeleteFromButton(deleteBtn);
        if (!window.bootstrap?.Modal) {
          showModalById('deleteUserModal');
        }
      }
    });
  }

  function bindModalPrefill() {
    const editModal = document.getElementById('editUserModal');
    if (editModal && window.bootstrap?.Modal) {
      editModal.addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        if (btn) {
          populateEditFromButton(btn);
        }
      });
      editModal.addEventListener('hidden.bs.modal', () => {
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
       // disablePasswordFields();
=======
        disablePasswordFields();
>>>>>>> theirs
=======
        disablePasswordFields();
>>>>>>> theirs
=======
        disablePasswordFields();
>>>>>>> theirs
      });
    }

    const deleteModal = document.getElementById('deleteUserModal');
    if (deleteModal && window.bootstrap?.Modal) {
      deleteModal.addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        if (btn) {
          populateDeleteFromButton(btn);
        }
      });
    }
  }

  function bindDeleteModal() {
    document.addEventListener('click', (event) => {
      const confirmBtn = event.target instanceof HTMLElement ? event.target.closest('#confirm-delete-btn') : null;
      if (!confirmBtn) return;
      const id = Number(document.getElementById('delete-user-id')?.value || currentDeleteUserId || 0);
      if (id) {
        deleteUser(id);
      } else {
        alert('No user selected to delete');
      }
    });
  }

  function bindConfirmEditButton() {
    document.addEventListener('click', (e) => {
      const btn = e.target instanceof HTMLElement ? e.target.closest('#confirm-edit-btn') : null;
      if (!btn) return;
      e.preventDefault();
      const form = document.getElementById('edit-user-form');
      if (!form) return;
      submitEditUserForm({ target: form, preventDefault: () => {} });
    });
  }

<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
  //function bindPasswordToggle() {
    // No-op: passwords are always enabled; kept for backward compatibility.
  //}
=======
  function bindPasswordToggle() {
    // No-op: passwords are always enabled; kept for backward compatibility.
  }
>>>>>>> theirs
=======
  function bindPasswordToggle() {
    // No-op: passwords are always enabled; kept for backward compatibility.
  }
>>>>>>> theirs
=======
  function bindPasswordToggle() {
    // No-op: passwords are always enabled; kept for backward compatibility.
  }
>>>>>>> theirs

  async function hydratePasswordPage() {
    const form = document.getElementById('change-password-form');
    if (!form) return;
    const id = Number(form.dataset.userId || 0);
    if (!id) return;
    document.getElementById('password-id').value = id;
    try {
      const { data } = await request(`getUser.php?id=${id}`);
      const badge = document.getElementById('password-username');
      if (badge) badge.textContent = data.username || '';
    } catch (error) {
      alert(error.message);
    }
  }

  async function hydrateEditPage() {
    const form = document.getElementById('edit-user-form');
    if (!form || form.dataset.userId === undefined) return;
    const id = Number(form.dataset.userId || 0);
    if (!id) return;
    try {
      const { data } = await request(`getUser.php?id=${id}`);
      document.getElementById('edit-id').value = String(data.id);
      const display = document.getElementById('edit-username-display');
      if (display) display.textContent = data.username || '';
      document.getElementById('edit-role').value = (data.role || data.user_role || 'kitchen');
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
      //disablePasswordFields();
=======
      disablePasswordFields();
>>>>>>> theirs
=======
      disablePasswordFields();
>>>>>>> theirs
=======
      disablePasswordFields();
>>>>>>> theirs
    } catch (error) {
      alert(error.message);
    }
  }

  async function submitChangePasswordForm(event) {
    event.preventDefault();
    const form = event.target;
    const payload = Object.fromEntries(new FormData(form).entries());
    payload.id = Number(payload.id);
    try {
      await request('changePassword.php', { method: 'POST', body: JSON.stringify(payload) });
      alert('Password updated');
      form.reset();
    } catch (error) {
      alert(error.message);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const createForm = document.getElementById('create-user-form');
    if (createForm) createForm.addEventListener('submit', submitCreateUserForm);

    const editForm = document.getElementById('edit-user-form');
    if (editForm) editForm.addEventListener('submit', submitEditUserForm);
    hydrateEditPage();

    const changePassForm = document.getElementById('change-password-form');
    if (changePassForm) {
      changePassForm.addEventListener('submit', submitChangePasswordForm);
      hydratePasswordPage();
    }

    bindModalPrefill();
    bindTableActions();
    bindDeleteModal();
    bindConfirmEditButton();
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    //bindPasswordToggle();
    loadUserList();
  });
})();
=======
=======
>>>>>>> theirs
=======
>>>>>>> theirs
    bindPasswordToggle();
    loadUserList();
  });
})();
<<<<<<< ours
<<<<<<< ours
>>>>>>> theirs
=======
>>>>>>> theirs
=======
>>>>>>> theirs
