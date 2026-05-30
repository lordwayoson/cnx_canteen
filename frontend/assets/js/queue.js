document.addEventListener('DOMContentLoaded', () => {
  const queueList = document.getElementById('queue-list');
  const queueItems = document.getElementById('queue-items');
  const searchForm = document.getElementById('queue-search-form');
  const searchInput = document.getElementById('queue-search-input');
  const resetBtn = document.getElementById('queue-reset');
  const searchPanel = document.getElementById('queue-search-panel');
  const searchResultsBody = document.getElementById('queue-search-results');
  const searchSummary = document.getElementById('queue-search-summary');
  const searchClose = document.getElementById('queue-search-close');
  const queueInterval = parseInt(document.body.dataset.queuePoll || '2', 10) * 1000;
  const MAX_ROWS = 10;

  let lastId = 0;
  const seenIds = new Set();
  const seenSignatures = new Set();
  const rowMap = new Map();

  // Enable fallback skin only when Bootstrap variables are missing.
  const bsBg = getComputedStyle(document.body).getPropertyValue('--bs-body-bg');
  if (!bsBg || bsBg.trim() === '') {
    document.body.classList.add('no-bootstrap');
  }

  function apiPath(path) {
    return path.startsWith('http') ? path : `../backend/api${path}`;
  }

  async function safeJson(response) {
    try {
      return await response.json();
    } catch (error) {
      const text = await response.text().catch(() => '');
      throw new Error(`Invalid JSON response${text ? `: ${text}` : ''}`);
    }
  }

  async function fetchQueue(initial = false) {
    try {
      if (initial) {
        lastId = 0;
        seenIds.clear();
      }
      const params = new URLSearchParams();
      if (!initial && lastId) {
        params.set('after_id', String(lastId));
      }
      const url = apiPath(`/queue/getNewQueue.php${params.toString() ? `?${params.toString()}` : ''}`);
      const response = await fetch(url, { credentials: 'include' });
      const data = await safeJson(response);
      if (!response.ok || data.success === false) {
        const message = data?.error || 'Unable to fetch queue';
        throw new Error(message);
      }
      const items = data.queue || [];
      const appendOnly = !initial && params.has('after_id');
      renderQueue(items, data.date, appendOnly);
    } catch (error) {
      console.error(error);
      if (queueList) {
        queueList.innerHTML = `<div class="alert alert-danger mb-0">Failed to load queue. ${error.message || ''}</div>`;
      }
    }
  }

  function renderQueue(items, dateLabel, appendOnly = false) {
    if (!queueList || !queueItems) return;

    const dateTarget = document.getElementById('queue-date');
    if (dateTarget) {
      dateTarget.textContent = dateLabel ? `Today: ${dateLabel}` : 'Today';
    }

    if (!appendOnly) {
      queueItems.innerHTML = '';
      seenIds.clear();
      seenSignatures.clear();
      rowMap.clear();
    }

    const fragment = document.createDocumentFragment();
    items.forEach((item) => {
      const numericId = Number(item.id || 0);
      if (seenIds.has(numericId)) {
        return;
      }
      const signature = `${item.staff_id || 'special'}|${item.served_at || ''}|${item.meal_label || ''}`;
      if (seenSignatures.has(signature)) {
        return;
      }
      seenIds.add(numericId);
      seenSignatures.add(signature);
      lastId = Math.max(lastId, numericId);
      const row = renderQueueItem(item);
      rowMap.set(numericId, row);
      fragment.appendChild(row);
    });
    queueItems.appendChild(fragment);

    trimQueueRows();

    if (!queueItems.children.length) {
      queueItems.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No meals served yet for today.</td></tr>';
    }
  }

<<<<<<< ours
<<<<<<< ours
  /*function renderQueueItem(item) {
=======
  function renderQueueItem(item) {
>>>>>>> theirs
=======
  function renderQueueItem(item) {
>>>>>>> theirs
    const projectName = (item.project || '').toLowerCase();
    const isSpecial = projectName === 'reserved' || projectName === 'temporal';
    const fullName = isSpecial ? (item.project || 'Reserved/Temporal') : ([item.name, item.lastname].filter(Boolean).join(' ') || 'Unknown');
    const mealLabel = isSpecial ? '—' : (item.meal_label ? `${item.meal_label}` : 'N/A');
    const servedAt = item.served_at ? new Date(item.served_at).toLocaleString() : '';
    const receiptClass = item.receipt_status && item.receipt_status.toLowerCase().includes('not')
      ? 'bg-warning text-dark'
      : 'bg-success';
    const diet = isSpecial ? '—' : (item.diet_notes ? item.diet_notes : 'None');
    const project = item.meal_project || item.project || '—';
<<<<<<< ours
<<<<<<< ours
    //const shift = isSpecial ? '—' : (item.shift_type || '—');
=======
    const shift = isSpecial ? '—' : (item.shift_type || '—');
>>>>>>> theirs
=======
    const shift = isSpecial ? '—' : (item.shift_type || '—');
>>>>>>> theirs

    const tr = document.createElement('tr');
    tr.classList.add('queue-row');
    tr.innerHTML = `
      <td class="fw-semibold">${fullName}</td>
      <td>${isSpecial ? '—' : (item.staff_id || '—')}</td>
      <td>${project}</td>
<<<<<<< ours
<<<<<<< ours
      <td class="fw-semibold text-dark meal-cell">${mealLabel}</td>
=======
      <td class="fw-semibold text-primary meal-cell">${mealLabel}</td>
>>>>>>> theirs
=======
      <td class="fw-semibold text-primary meal-cell">${mealLabel}</td>
>>>>>>> theirs
      <td>${diet}</td>
      <td>${shift}</td>
      <td>${servedAt || '—'}</td>
      <td><span class="badge ${receiptClass}">${item.receipt_status || '—'}</span></td>
    `;
    return tr;
<<<<<<< ours
<<<<<<< ours
  }*/
=======
  }
>>>>>>> theirs
=======
  }
>>>>>>> theirs

  function trimQueueRows() {
    if (!queueItems) return;
    while (queueItems.children.length > MAX_ROWS) {
      queueItems.removeChild(queueItems.firstElementChild);
    }
  }

  async function pollLoop(initial = false) {
    await fetchQueue(initial);
  }

  pollLoop(true);
  setInterval(() => { pollLoop(false); }, queueInterval);

  async function handleSearch(staffId) {
    if (!staffId) {
      if (searchSummary) searchSummary.textContent = 'Enter a Staff ID to search.';
      if (searchResultsBody) searchResultsBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Awaiting Staff ID...</td></tr>';
      return;
    }
    const params = new URLSearchParams({ staff_id: staffId });
    const url = apiPath(`/queue/getNewQueue.php?${params.toString()}`);
    try {
      const response = await fetch(url, { credentials: 'include' });
      const data = await safeJson(response);
      if (!response.ok || data.success === false) {
        throw new Error(data?.error || 'Unable to search queue');
      }
      const items = data.queue || [];
      if (searchSummary) {
        searchSummary.textContent = items.length ? `Found ${items.length} entr${items.length === 1 ? 'y' : 'ies'} for ${staffId}` : `No entries for ${staffId} today.`;
      }
      if (searchResultsBody) {
        if (!items.length) {
          searchResultsBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No queue entries for this Staff ID today.</td></tr>';
        } else {
          searchResultsBody.innerHTML = '';
          const fragment = document.createDocumentFragment();
          items.forEach((item) => {
            fragment.appendChild(renderQueueItem(item));
          });
          searchResultsBody.appendChild(fragment);
        }
      }
      if (searchPanel) {
        searchPanel.classList.add('show');
      }
    } catch (error) {
      if (searchSummary) searchSummary.textContent = `Search failed: ${error.message}`;
      if (searchResultsBody) {
        searchResultsBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Unable to load search results.</td></tr>';
      }
      if (searchPanel) {
        searchPanel.classList.add('show');
      }
    }
  }

  if (searchForm && searchInput) {
    searchForm.addEventListener('submit', (event) => {
      event.preventDefault();
      handleSearch(searchInput.value.trim());
    });
  }

  if (resetBtn && searchInput) {
    resetBtn.addEventListener('click', () => {
      searchInput.value = '';
      if (searchSummary) searchSummary.textContent = 'Enter a Staff ID to search.';
      if (searchResultsBody) searchResultsBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Awaiting Staff ID...</td></tr>';
    });
  }

  if (searchClose && searchPanel) {
    searchClose.addEventListener('click', () => {
      searchPanel.classList.remove('show');
    });
  }
<<<<<<< ours
<<<<<<< ours
});
=======
});
>>>>>>> theirs
=======
});
>>>>>>> theirs
