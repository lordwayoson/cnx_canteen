function escapeReportCell(value) { const span = document.createElement('span'); span.textContent = value == null || value === '' ? '—' : String(value); return span.innerHTML; }
let reportRequest = 0;
async function loadSummary() {
  const request = ++reportRequest;
  const requestKey = new URLSearchParams(new FormData(document.getElementById('report-filter'))).toString();
  const startDate = document.getElementById('start_date').value;
  const endDate = document.getElementById('end_date').value;
  const shiftType = document.getElementById('shift_type').value;
  const reportType = document.getElementById('report_type')?.value || '';
  const params = buildReportParams({ startDate, endDate, shiftType, reportType });

  const query = params.toString();
  const apiUrl = buildSummaryUrl(`/reports/summary.php${query ? `?${query}` : ''}`);
  const response = await fetch(apiUrl, { credentials: 'include' });
  let payload;
  try {
    payload = await response.json();
  } catch (parseError) {
    throw new Error('Unable to load report. Please try again.');
  }
  if (!response.ok) {
    const detail = payload?.error || payload?.detail || 'Request failed';
    throw new Error(detail);
  }
  if (request !== reportRequest || requestKey !== new URLSearchParams(new FormData(document.getElementById('report-filter'))).toString()) return;
  window.reportSnapshot = { key: requestKey, context: payload.context };
  renderSummary(payload.data || { totals: [], topMeals: [], staff: [], servedMeals: [], selectedMeals: [] }, reportType);
}

function buildSummaryUrl(pathname) {
  if (window.CANTEEN_SUMMARY_API) {
    const queryIndex = pathname.indexOf('?');
    const query = queryIndex >= 0 ? pathname.slice(queryIndex) : '';
    return `${window.CANTEEN_SUMMARY_API}${query}`;
  }
  const origin = window.location.origin;
  const apiBaseAbsolute = window.CANTEEN_BACKEND_API_URL || '';
  const apiBaseRelative = window.CANTEEN_BACKEND_API_BASE || window.CANTEEN_BACKEND_BASE || '';
  const base = apiBaseAbsolute || `${origin}${apiBaseRelative}/api`;
  const normalizedPath = pathname.startsWith('/api') ? pathname.replace('/api', '') : pathname;
  return `${base}${normalizedPath}`;
}

function renderSummary(data, reportType = '') {
  const totalsTableBody = document.querySelector('#totals-table tbody');
  const mealsTableBody = document.querySelector('#meals-table tbody');
  const staffTableBody = document.querySelector('#staff-table tbody');
  const servedMealsTableBody = document.querySelector('#served-meals-table tbody');
  const selectedMealsTableBody = document.querySelector('#selected-meals-table tbody');
  if (totalsTableBody) totalsTableBody.innerHTML = '';
  if (mealsTableBody) mealsTableBody.innerHTML = '';
  if (staffTableBody) staffTableBody.innerHTML = '';
  if (servedMealsTableBody) servedMealsTableBody.innerHTML = '';
  if (selectedMealsTableBody) selectedMealsTableBody.innerHTML = '';

  toggleReportSections(reportType);

  (data.totals || []).forEach((row) => {
    if (!totalsTableBody) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeReportCell(row.date)}</td><td>${escapeReportCell(row.total)}</td>`;
    totalsTableBody.appendChild(tr);
  });
  if (totalsTableBody && !totalsTableBody.children.length) {
    totalsTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No data</td></tr>';
  }

  (data.topMeals || []).forEach((row) => {
    if (!mealsTableBody) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeReportCell(row.meal_label)}</td><td>${escapeReportCell(row.count)}</td>`;
    mealsTableBody.appendChild(tr);
  });
  if (mealsTableBody && !mealsTableBody.children.length) {
    mealsTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No data</td></tr>';
  }

  (data.staff || []).forEach((row) => {
    if (!staffTableBody) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${escapeReportCell(row.name)} ${escapeReportCell(row.lastname)}</td><td>${escapeReportCell(row.count)}</td>`;
    staffTableBody.appendChild(tr);
  });
  if (staffTableBody && !staffTableBody.children.length) {
    staffTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No data</td></tr>';
  }

  // Default view (no report type) keeps charts visible.
  if (!reportType) {
    const servedChartData = buildStackedChartData(data.servedMeals || []);
    renderStackedChart('servedMealsChart', servedChartData, 'Meals Served per Meal Type per Day', 'served');

    const selectedChartData = buildStackedChartData(data.selectedMeals || []);
    renderStackedChart('selectedMealsChart', selectedChartData, 'Selected Meals per Meal Type per Day', 'selected');
    return;
  }

  if (reportType === 'meals_served' && servedMealsTableBody) {
    if (!data.servedMeals || !data.servedMeals.length) {
      servedMealsTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No data</td></tr>';
    } else {
      data.servedMeals.forEach((row) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${escapeReportCell(row.date)}</td><td>${escapeReportCell(row.meal_label)}</td><td>${escapeReportCell(row.count)}</td>`;
        servedMealsTableBody.appendChild(tr);
      });
    }
  }

  if (reportType === 'selected_meals' && selectedMealsTableBody) {
    if (!data.selectedMeals || !data.selectedMeals.length) {
      selectedMealsTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No data</td></tr>';
    } else {
      data.selectedMeals.forEach((row) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${escapeReportCell(row.date)}</td><td>${escapeReportCell(row.meal_label)}</td><td>${escapeReportCell(row.count)}</td>`;
        selectedMealsTableBody.appendChild(tr);
      });
    }
  }
}

function toggleReportSections(reportType) {
  const servedChartCard = document.getElementById('served-chart-card');
  const selectedChartCard = document.getElementById('selected-chart-card');
  const servedTableCard = document.getElementById('served-table-card');
  const selectedTableCard = document.getElementById('selected-table-card');
  const dailyTotalsCard = document.getElementById('daily-totals-card');
  const topMealsCard = document.getElementById('top-meals-table-card');
  const topStaffCard = document.getElementById('top-staff-card');

  if (!reportType) {
    if (servedChartCard) servedChartCard.style.display = '';
    if (selectedChartCard) selectedChartCard.style.display = '';
    if (servedTableCard) servedTableCard.style.display = 'none';
    if (selectedTableCard) selectedTableCard.style.display = 'none';
    if (dailyTotalsCard) dailyTotalsCard.style.display = '';
    if (topMealsCard) topMealsCard.style.display = '';
    if (topStaffCard) topStaffCard.style.display = '';
    return;
  }

  const visibilityMap = {
    meals_served: {
      servedTableCard: true,
      selectedTableCard: false,
      dailyTotalsCard: false,
      topMealsCard: false,
      topStaffCard: false,
      servedChartCard: false,
      selectedChartCard: false,
    },
    selected_meals: {
      servedTableCard: false,
      selectedTableCard: true,
      dailyTotalsCard: false,
      topMealsCard: false,
      topStaffCard: false,
      servedChartCard: false,
      selectedChartCard: false,
    },
    daily_totals: {
      servedTableCard: false,
      selectedTableCard: false,
      dailyTotalsCard: true,
      topMealsCard: false,
      topStaffCard: false,
      servedChartCard: false,
      selectedChartCard: false,
    },
    top_meals: {
      servedTableCard: false,
      selectedTableCard: false,
      dailyTotalsCard: false,
      topMealsCard: true,
      topStaffCard: false,
      servedChartCard: false,
      selectedChartCard: false,
    },
  };

  const visibility = visibilityMap[reportType];
  if (!visibility) {
    // Unrecognized report type: fall back to default combined view.
    if (servedChartCard) servedChartCard.style.display = '';
    if (selectedChartCard) selectedChartCard.style.display = '';
    if (servedTableCard) servedTableCard.style.display = 'none';
    if (selectedTableCard) selectedTableCard.style.display = 'none';
    if (dailyTotalsCard) dailyTotalsCard.style.display = '';
    if (topMealsCard) topMealsCard.style.display = '';
    if (topStaffCard) topStaffCard.style.display = '';
    return;
  }

  if (servedChartCard) servedChartCard.style.display = visibility.servedChartCard ? '' : 'none';
  if (selectedChartCard) selectedChartCard.style.display = visibility.selectedChartCard ? '' : 'none';
  if (servedTableCard) servedTableCard.style.display = visibility.servedTableCard ? '' : 'none';
  if (selectedTableCard) selectedTableCard.style.display = visibility.selectedTableCard ? '' : 'none';
  if (dailyTotalsCard) dailyTotalsCard.style.display = visibility.dailyTotalsCard ? '' : 'none';
  if (topMealsCard) topMealsCard.style.display = visibility.topMealsCard ? '' : 'none';
  if (topStaffCard) topStaffCard.style.display = visibility.topStaffCard ? '' : 'none';
}

const colorPalette = ['#0d6efd', '#6c757d', '#198754', '#dc3545', '#ffc107', '#fd7e14', '#20c997', '#6610f2', '#0dcaf0', '#adb5bd'];
const colorMap = new Map();

function buildReportParams({ startDate = '', endDate = '', shiftType = '', reportType = '' } = {}) {
  const params = new URLSearchParams();
  if (startDate) params.append('start_date', startDate);
  if (endDate) params.append('end_date', endDate);
  if (shiftType) params.append('shift_type', shiftType);
  if (reportType) params.append('report_type', reportType);
  return params;
}


function getMealColor(mealLabel) {
  if (colorMap.has(mealLabel)) return colorMap.get(mealLabel);
  const color = colorPalette[colorMap.size % colorPalette.length];
  colorMap.set(mealLabel, color);
  return color;
}

function buildStackedChartData(entries) {
  const dateSet = new Set();
  const mealSet = new Set();
  const lookup = new Map();

  (entries || []).forEach((entry) => {
    const entryDate = entry?.date || entry?.selected_date;
    if (!entry || !entryDate || !entry.meal_label) return;
    dateSet.add(entryDate);
    mealSet.add(entry.meal_label);
    lookup.set(`${entryDate}|${entry.meal_label}`, Number(entry.count) || 0);
  });

  const labels = Array.from(dateSet).sort();
  const meals = Array.from(mealSet);
  const datasets = meals.map((meal) => ({
    label: meal,
    data: labels.map((date) => lookup.get(`${date}|${meal}`) || 0),
    backgroundColor: getMealColor(meal)
  }));

  return { labels, datasets };
}

const chartInstances = {};
function renderStackedChart(canvasId, chartData, title, tooltipSuffix) {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return;

  if (chartInstances[canvasId]) {
    chartInstances[canvasId].destroy();
  }

  chartInstances[canvasId] = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false
      },
      plugins: {
        title: {
          display: true,
          text: title
        },
        tooltip: {
          callbacks: {
            label: (context) => {
              const meal = context.dataset?.label || 'Meal';
              const value = context.parsed?.y ?? 0;
              const suffix = tooltipSuffix === 'selected' ? 'selected' : 'served';
              return `${meal}: ${value} ${suffix}`;
            }
          }
        }
      },
      scales: {
        x: {
          stacked: false
        },
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const filterForm = document.getElementById('report-filter');


  if (filterForm) {
    const initial = new URLSearchParams(location.search);
    ['start_date', 'end_date', 'shift_type', 'report_type'].forEach(id => { if (initial.has(id)) document.getElementById(id).value = initial.get(id); });
    filterForm.addEventListener('submit', async (event) => {
      if (['print-button', 'pdf-button'].includes(event.submitter?.id)) return;
      event.preventDefault();
      try {
        await loadSummary();
      } catch (error) {
        console.error(error);
        alert(`Failed to load summary: ${error.message}`);
      }
    });

    loadSummary().catch((error) => { console.error(error); const status = document.getElementById('report-status'); if (status) status.textContent = error.message; });
  }
});
