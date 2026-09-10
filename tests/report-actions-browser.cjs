const { chromium } = require(process.env.REPORT_PLAYWRIGHT_MODULE || 'playwright');
const fs = require('node:fs');
const assert = require('node:assert/strict');
(async () => {
  const auth = JSON.parse(fs.readFileSync('storage/reports-qa/apache-session.json', 'utf8'));
  const base = process.env.REPORT_TEST_BASE || 'http://localhost/cnx_canteen';
  const browser = await chromium.launch({headless:true, executablePath:process.env.REPORT_BROWSER_EXECUTABLE || undefined});
  try {
    for (const scenario of [
      {path:'/frontend/reports/index.php'},
      {path:'/reports'},
      {path:'/reports/'},
      {path:'/frontend/reports/'},
      {path:'/frontend/reports/index.php', blockCharts:true},
      {path:'/frontend/reports/index.php', noActions:true},
      {path:'/frontend/reports/index.php', noJS:true},
    ].filter(s => process.env.REPORT_ACTION_CASES !== 'fallback' || s.noActions || s.noJS)) {
      const context = await browser.newContext({javaScriptEnabled:!scenario.noJS});
      await context.addCookies([{name:auth.name,value:auth.id,url:base}]);
      if(scenario.blockCharts) await context.route('**/charts.js*', route => route.abort());
      if(scenario.noActions) await context.route('**/report-actions.js*', route => route.abort());
      const page = await context.newPage();
      await page.goto(base+scenario.path);
      await page.locator('#start_date').fill('2026-09-01');
      await page.locator('#end_date').fill('2026-09-10');
      await page.locator('#report_type').selectOption('meals_served');
      const actionSrc = await page.locator('script[src*="report-actions.js"]').getAttribute('src');
      assert.match(actionSrc,/\/cnx_canteen\/frontend\/assets\/js\/report-actions\.js\?v=\d+/);
      const popupEvent = context.waitForEvent('page');
      await page.locator('#print-button').click();
      const preview = await popupEvent;
      await preview.waitForURL('**/frontend/reports/print.php?**');
      await preview.locator('.report-document').waitFor();
      assert.match(await preview.locator('.period').innerText(),/01 September 2026 - 10 September 2026/);
      assert.equal(await preview.locator('nav,.sidebar').count(),0);
      // Native form downloads can belong to their target page rather than opener.
      async function downloadFrom(target) {
        let cleanup;
        const downloaded = new Promise((resolve,reject) => {
          const onDownload = file => { cleanup(); resolve(file); };
          const watch = p => p.on('download',onDownload);
          const timer = setTimeout(()=>{cleanup();reject(new Error('PDF download did not start'));},30000);
          cleanup = () => { clearTimeout(timer); context.off('page',watch); context.pages().forEach(p=>p.off('download',onDownload)); };
          context.pages().forEach(watch);
          context.on('page',watch);
        });
        try {
          const [file] = await Promise.all([downloaded, target.locator('#pdf-button').click()]);
          assert.match(file.suggestedFilename(),/^Concentrix_Canteen_.*\.pdf$/);
          await file.saveAs('storage/reports-qa/buttons-export.pdf');
          assert.equal(fs.readFileSync('storage/reports-qa/buttons-export.pdf').subarray(0,5).toString(),'%PDF-');
        } finally { cleanup(); }
      }
      await downloadFrom(page);
      await downloadFrom(preview);
      console.log('PASS',JSON.stringify(scenario));
      await context.close();
    }
  } finally { await browser.close(); }
})().catch(e=>{console.error(e);process.exitCode=1;});
