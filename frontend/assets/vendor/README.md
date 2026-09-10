Reports use locally packaged assets and require no CDN access:

- bootstrap.min.css: Bootstrap 5.3.3, MIT
  https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
- bootstrap.bundle.min.js: Bootstrap 5.3.3, MIT
  https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js
- chart.umd.min.js: Chart.js 4.4.3, MIT
  https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js

Original license banners are retained. These versions match the application's existing CDN versions. Other application pages retain their existing asset loading. Reports do not initialize DataTables and therefore do not load its unused assets.
