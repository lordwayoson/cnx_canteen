(() => {
  const form = document.getElementById('report-filter');
  const pdfButton = document.getElementById('pdf-button');
  const printButton = document.getElementById('print-button');
  const status = document.getElementById('report-status');
  let busy = false;
  const endpoint = (button, fallback) => button?.getAttribute('formaction') || button?.dataset.url || fallback;
  async function exportReport(kind, event) {
    event.preventDefault();
    if (busy || (form && !form.reportValidity())) return;
    const printUrl = endpoint(printButton, 'print.php');
    const pdfUrl = endpoint(pdfButton, 'pdf.php');
    const preview = kind === 'print' ? window.open('', '_blank') : null;
    if (kind === 'print' && !preview) {
      // Still provide the preview when the browser blocks a new tab.
      window.location.assign(`${printUrl}?${new URLSearchParams(new FormData(form))}`);
      return;
    }
    busy = true;
    [printButton, pdfButton].forEach(button => { if (button) { button.disabled = true; button.setAttribute('aria-busy', 'true'); } });
    status.textContent = 'Preparing report…';
    try {
      if (preview) { preview.document.title = 'Preparing report'; preview.document.body.textContent = 'Preparing report preview…'; }
      let context = pdfButton?.dataset.context;
      if (form) {
        const key = new URLSearchParams(new FormData(form)).toString();
        context = window.reportSnapshot?.key === key ? window.reportSnapshot.context : null;
        if (!context) {
          const response = await fetch(`${form.dataset.summaryUrl}?${key}`, { credentials: 'same-origin', cache: 'no-store' });
          const payload = await response.json().catch(() => null);
          if (!response.ok || !payload?.context) throw new Error(payload?.error || 'Unable to prepare report. Please sign in again or retry.');
          context = payload.context;
          window.reportSnapshot = { key, context };
        }
      }
      if (!context) throw new Error('Please apply the report filters and try again.');
      if (kind === 'print') {
        preview.location.replace(`${printUrl}?context=${encodeURIComponent(context)}`);
        status.textContent = 'Report preview opened.';
      } else {
        const response = await fetch(`${pdfUrl}?context=${encodeURIComponent(context)}`, { credentials: 'same-origin', cache: 'no-store' });
        if (!response.ok || !response.headers.get('Content-Type')?.includes('application/pdf')) {
          throw new Error(response.status === 401 || response.status === 403 ? 'Your session cannot access reports. Please sign in as an administrator.' : 'Unable to export PDF. Please apply the filters again and retry.');
        }
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = response.headers.get('Content-Disposition')?.match(/filename="([^"]+)"/)?.[1] || 'Concentrix_Canteen_Report.pdf';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(() => URL.revokeObjectURL(url), 60000);
        status.textContent = 'PDF downloaded.';
      }
    } catch (error) {
      if (preview) preview.close();
      status.textContent = error.message || 'Unable to generate report. Please try again.';
    } finally {
      busy = false;
      [printButton, pdfButton].forEach(button => { if (button) { button.disabled = false; button.removeAttribute('aria-busy'); } });
    }
  }
  printButton?.addEventListener('click', event => exportReport('print', event));
  pdfButton?.addEventListener('click', event => exportReport('pdf', event));
  document.getElementById('print-document')?.addEventListener('click', () => window.print());
  const backLink = document.getElementById('back-to-reports');
  backLink?.addEventListener('click', event => {
    try {
      // Keep the original report tab and its current filters intact.
      if (window.opener && !window.opener.closed &&
          window.opener.location.origin === window.location.origin &&
          window.opener.document.getElementById('report-filter')) {
        event.preventDefault();
        window.opener.focus();
        window.close();
        // Some browsers refuse to close tabs they did not open through script.
        setTimeout(() => window.location.assign(backLink.href), 150);
      }
    } catch (_) {
      // A direct preview or unavailable opener uses the normal same-tab link.
    }
  });
})();

