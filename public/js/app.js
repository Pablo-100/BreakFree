/**
 * BreakFree - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initRangeSliders();
    initAutoFade();
    initConfirmDialogs();
});

/* ─── Sidebar mobile toggle ─── */
function initSidebar() {
    const toggle  = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay?.classList.toggle('active');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
}

/* ─── Range sliders with value display ─── */
function initRangeSliders() {
    document.querySelectorAll('input[type="range"]').forEach(range => {
        const valueDisplay = document.getElementById(range.id + '_value');
        if (valueDisplay) {
            valueDisplay.textContent = range.value;
            range.addEventListener('input', () => {
                valueDisplay.textContent = range.value;
            });
        }
    });
}

/* ─── Auto-fade flash messages ─── */
function initAutoFade() {
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

/* ─── Confirm dialogs for delete actions ─── */
function initConfirmDialogs() {
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm(btn.dataset.confirm || 'Êtes-vous sûr ?')) {
                e.preventDefault();
            }
        });
    });
}

/* ─── Chart.js utils ─── */
const ChartColors = {
    accent:   'rgb(99, 102, 241)',
    accentBg: 'rgba(99, 102, 241, 0.15)',
    success:  'rgb(34, 197, 94)',
    successBg:'rgba(34, 197, 94, 0.15)',
    warning:  'rgb(245, 158, 11)',
    warningBg:'rgba(245, 158, 11, 0.15)',
    danger:   'rgb(239, 68, 68)',
    dangerBg: 'rgba(239, 68, 68, 0.15)',
    text:     '#94a3b8',
    grid:     'rgba(148, 163, 184, 0.1)',
};

const ChartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: ChartColors.text, font: { size: 12 } }
        }
    },
    scales: {
        x: {
            ticks: { color: ChartColors.text, font: { size: 11 } },
            grid:  { color: ChartColors.grid }
        },
        y: {
            ticks: { color: ChartColors.text, font: { size: 11 } },
            grid:  { color: ChartColors.grid },
            beginAtZero: true
        }
    }
};

/**
 * Fetch data from API and create a line chart
 */
async function createLineChart(canvasId, apiUrl, label, color, bgColor) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    try {
        const response = await fetch(apiUrl);
        const data = await response.json();

        if (!data.labels || data.labels.length === 0) {
            canvas.parentElement.innerHTML = '<div class="empty-state"><p>Pas encore de données</p></div>';
            return;
        }

        // Format dates for display
        const labels = data.labels.map(d => {
            const date = new Date(d);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
        });

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data.values,
                    borderColor: color,
                    backgroundColor: bgColor,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                }]
            },
            options: {
                ...ChartDefaults,
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    } catch (err) {
        console.error('Chart error:', err);
    }
}

/**
 * Create admin overview chart with dual axes
 */
async function createAdminChart(canvasId, apiUrl) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    try {
        const response = await fetch(apiUrl);
        const data = await response.json();

        if (!data.labels || data.labels.length === 0) {
            canvas.parentElement.innerHTML = '<div class="empty-state"><p>Pas encore de données</p></div>';
            return;
        }

        const labels = data.labels.map(d => {
            const date = new Date(d);
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
        });

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Consommation moy.',
                        data: data.consumption,
                        borderColor: ChartColors.accent,
                        backgroundColor: ChartColors.accentBg,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Craving moyen',
                        data: data.cravings,
                        borderColor: ChartColors.warning,
                        backgroundColor: ChartColors.warningBg,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                ...ChartDefaults,
                scales: {
                    ...ChartDefaults.scales,
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        ticks: { color: ChartColors.text },
                        grid: { drawOnChartArea: false },
                        beginAtZero: true,
                        max: 10,
                    }
                }
            }
        });
    } catch (err) {
        console.error('Admin chart error:', err);
    }
}
