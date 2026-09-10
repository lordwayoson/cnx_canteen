const { chromium } = require(process.env.REPORT_PLAYWRIGHT_MODULE || 'playwright');
const fs = require('node:fs');
const assert = require('node:assert/strict');
(async () => {
  const browser = await chromium.launch({headless:true, executablePath:process.env.REPORT_BROWSER_EXECUTABLE || undefined});
  const base = 'http://127.0.0.1:8091';
  const ids = JSON.parse(fs.readFileSync('storage/reports-qa/sessions.json','utf8'));
  const context = await browser.newContext({viewport:{width:1440,height:1000}});
  const errors = [];
  const page = await context.newPage();
  page.on('pageerror', e => errors.push(e.message));
  for (const path of ['/frontend/reports/print.php','/frontend/reports/pdf.php','/backend/api/reports/summary.php']) {
    assert.equal((await context.request.get(base+path)).status(),401);
  }
  await context.addCookies([{name:ids.cookieName,value:ids.kitchen,url:base}]);
  for (const path of ['/frontend/reports/index.php','/frontend/reports/print.php','/frontend/reports/pdf.php','/backend/api/reports/summary.php']) {
    assert.equal((await context.request.get(base+path)).status(),403);
  }
  await context.addCookies([{name:ids.cookieName,value:ids.admin,url:base}]);
  assert.equal((await context.request.get(base+'/backend/api/reports/summary.php?start_date=2026-02-30')).status(),422);
  assert.equal((await context.request.get(base+'/frontend/reports/pdf.php?shift_type=bad')).status(),422);
  // Block all external origins: report assets must work entirely offline.
  await context.route('**/*',route => route.request().url().startsWith(base) ? route.continue() : route.abort());
  await page.goto(base+'/frontend/reports/index.php?start_date=2026-09-01&end_date=2026-09-10&report_type=meals_served');
  await page.waitForFunction(()=>window.reportSnapshot?.context);
  await page.screenshot({path:'storage/reports-qa/dashboard.png',fullPage:true});
  const previewEvent = context.waitForEvent('page');
  await page.getByRole('button',{name:'Print Report',exact:true}).click();
  const preview = await previewEvent;
  await preview.waitForURL('**/print.php?context=*');
  await preview.waitForLoadState('networkidle');
  assert.equal(await preview.locator('nav,aside,.sidebar,.navbar,#report-filter').count(),0);
  assert.match(await preview.locator('.metadata').innerText(),/qa.admin/);
  await preview.screenshot({path:'storage/reports-qa/preview.png',fullPage:true});
  await preview.emulateMedia({media:'print'});
  assert.equal(await preview.locator('.print-toolbar').isVisible(),false);
  await preview.pdf({path:'storage/reports-qa/browser-print.pdf',preferCSSPageSize:true,printBackground:true});
  await preview.emulateMedia({media:'screen'});
  const downloadEvent = preview.waitForEvent('download');
  await preview.getByRole('button',{name:'Export PDF',exact:true}).click();
  const download = await downloadEvent;
  assert.match(download.suggestedFilename(),/^Concentrix_Canteen_.*\.pdf$/);
  await download.saveAs('storage/reports-qa/http-export.pdf');
  assert.equal(fs.readFileSync('storage/reports-qa/http-export.pdf').subarray(0,5).toString(),'%PDF-');
  await page.setViewportSize({width:390,height:844});
  await page.screenshot({path:'storage/reports-qa/mobile.png',fullPage:true});
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true);
  assert.deepEqual(errors,[]);
  await preview.goto(base+'/frontend/reports/print.php?context='+ids.fixture);
  assert.equal(await preview.locator('.report-table').first().locator('tbody tr').count(),181);
  assert.equal(await preview.locator('.identity img').count(),1);
  await preview.pdf({path:'storage/reports-qa/browser-large.pdf',preferCSSPageSize:true,printBackground:true});
  assert.equal((await context.request.get(base+'/frontend/dashboard.php')).status(),200);
  assert.equal((await context.request.get(base+'/frontend/queue.php')).status(),200);
  assert.equal((await context.request.post(base+'/backend/api/auth/login.php',{data:{username:'',password:''}})).status(),422);
  await context.request.get(base+'/frontend/logout.php');
  assert.equal((await context.request.get(base+'/backend/api/reports/summary.php')).status(),401);
  await browser.close();
  console.log('Browser/HTTP tests passed: anonymous 401, kitchen 403, malformed filters 422, offline assets, preview, print CSS, PDF download, mobile layout, no JavaScript errors.');
})().catch(e=>{console.error(e);process.exit(1);});
