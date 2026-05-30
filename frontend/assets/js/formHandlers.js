document.addEventListener('DOMContentLoaded', () => {
  const ingressButton = document.getElementById('ingress-sync');
  const previewButton = document.getElementById('preview-btn');
  const importButton = document.getElementById('import-btn');
  const previewTableBody = document.querySelector('#preview-table tbody');

  if (ingressButton) {
    ingressButton.addEventListener('click', async () => {
      ingressButton.disabled = true;
      ingressButton.innerText = 'Syncing...';
      try {
        const response = await fetch('../backend/api/ingress/importUsers.php', {
          method: 'POST',
          credentials: 'include'
        });
        const data = await parseJsonResponse(response);
        if (!response.ok) {
          const message = data.error || data.message || 'Ingress sync failed';
          const detail = data.details ? `\nDetails: ${data.details}` : '';
          throw new Error(message + detail);
        }
        alert(data.message + '\nImported: ' + data.count);
      } catch (error) {
        console.error(error);
        alert('Failed to sync ingress users. ' + (error.message || ''));
      } finally {
        ingressButton.disabled = false;
        ingressButton.innerText = 'Ingress Sync';
      }
    });
  }

  if (previewButton) {
    previewButton.addEventListener('click', async () => {
      previewButton.disabled = true;
      previewButton.innerText = 'Loading...';
      try {
        const response = await fetch('../backend/api/sheets/preview.php', {
          credentials: 'include'
        });
        const payload = await parseJsonResponse(response);
        if (!response.ok) {
          const message = payload.error || 'Preview failed';
          const detail = payload.details ? `\nDetails: ${payload.details}` : '';
          throw new Error(message + detail);
        }
        renderPreview(payload.rows || []);
      } catch (error) {
        console.error(error);
        alert('Failed to load preview. ' + (error.message || ''));
      } finally {
        previewButton.disabled = false;
        previewButton.innerText = 'Preview New Rows';
      }
    });
  }

  if (importButton) {
    importButton.addEventListener('click', async () => {
      if (!confirm('Import all new rows?')) return;
      importButton.disabled = true;
      importButton.innerText = 'Importing...';
      try {
        const response = await fetch('../backend/api/sheets/import.php', {
          method: 'POST',
          credentials: 'include'
        });
        const payload = await parseJsonResponse(response);
        if (!response.ok) {
          const message = payload.error || payload.message || 'Import failed';
          const detail = payload.details ? `\nDetails: ${payload.details}` : '';
          throw new Error(message + detail);
        }
        const skipped = payload.summary?.skippedMissingStaff?.length || 0;
        const summaryLines = [`Inserted: ${payload.summary?.inserted || 0}`];
        if (skipped > 0) {
          summaryLines.push(`Skipped (missing staff): ${skipped}`);
        }
        alert(`${payload.message}\n${summaryLines.join('\n')}`);
        renderPreview([]);
      } catch (error) {
        console.error(error);
        alert('Failed to import rows. ' + (error?.message || ''));
      } finally {
        importButton.disabled = false;
        importButton.innerText = 'Confirm Import';
      }
    });
  }

  async function parseJsonResponse(response) {
    const contentType = response.headers.get('content-type') || '';
    const text = await response.text();
    if (contentType.includes('application/json')) {
      return JSON.parse(text || '{}');
    }
    try {
      return JSON.parse(text || '{}');
    } catch (error) {
      throw new Error(text.slice(0, 200) || 'Unexpected non-JSON response');
    }
  }

  function renderPreview(rows) {
    if (!previewTableBody) return;
    previewTableBody.innerHTML = '';
    if (!rows.length) {
      previewTableBody.innerHTML = '<tr><td colspan="7" class="text-center">No new rows</td></tr>';
      return;
    }
    rows.forEach((row) => {
      const tr = document.createElement('tr');
      const statusLabel = row.has_user === false
        ? '<span class="badge bg-warning text-dark">Missing staff record</span>'
        : '<span class="badge bg-success">Ready</span>';
      tr.innerHTML = `
        <td>${row.sheet_row_id}</td>
        <td>${row.staff_id}</td>
        <td>${row.project}</td>
        <td>${row.shift_type}</td>
        <td>${row.week_start_date}</td>
        <td>${row.diet_notes || ''}</td>
        <td>${statusLabel}</td>`;
      previewTableBody.appendChild(tr);
    });
    if (window.jQuery && jQuery.fn.DataTable) {
      const existingTable = jQuery.fn.dataTable.isDataTable('#preview-table')
        ? jQuery('#preview-table').DataTable()
        : null;
      if (existingTable) {
        existingTable.clear();
        existingTable.destroy();
      }
      jQuery('#preview-table').DataTable({
        destroy: true,
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']]
      });
    } else if (window.DataTable) {
      return new window.DataTable('#preview-table', {
        destroy: true
      });
    }
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
