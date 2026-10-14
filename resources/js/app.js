import Chart from 'chart.js/auto';

window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    // Mobile sidebar
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('backdrop');
    const toggle = () => {
        sidebar?.classList.toggle('-translate-x-full');
        sidebar?.classList.toggle('rtl:translate-x-full');
        backdrop?.classList.toggle('hidden');
    };
    document.getElementById('menu-toggle')?.addEventListener('click', toggle);
    backdrop?.addEventListener('click', toggle);

    // Dynamic order item rows
    const items = document.getElementById('order-items');
    if (items) {
        const template = document.getElementById('item-template');
        let index = items.querySelectorAll('[data-item]').length;
        const recalc = () => {
            let total = 0;
            items.querySelectorAll('[data-item]').forEach((row) => {
                const price = Number(row.querySelector('select').selectedOptions[0]?.dataset.price || 0);
                const qty = Number(row.querySelector('input').value || 0);
                total += price * qty;
            });
            document.getElementById('order-total').textContent = total.toLocaleString(document.documentElement.lang === 'fa' ? 'fa-IR' : 'en-US');
        };
        document.getElementById('add-item')?.addEventListener('click', () => {
            items.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index++));
            recalc();
        });
        items.addEventListener('click', (e) => {
            if (e.target.closest('[data-remove]') && items.querySelectorAll('[data-item]').length > 1) {
                e.target.closest('[data-item]').remove();
                recalc();
            }
        });
        items.addEventListener('input', recalc);
        items.addEventListener('change', recalc);
        recalc();
    }

    // Dashboard charts
    const data = window.dashboardChart;
    if (data) {
        const font = { family: 'Vazirmatn' };
        Chart.defaults.font.family = font.family;
        Chart.defaults.color = '#64748b';
        const rtl = document.documentElement.dir === 'rtl';
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    { type: 'line', label: data.ordersLabel, data: data.orders, yAxisID: 'y1', borderColor: '#f59e0b', backgroundColor: '#f59e0b', tension: 0.35, pointRadius: 4 },
                    { label: data.revenueLabel, data: data.revenue, backgroundColor: '#6366f1', borderRadius: 8, maxBarThickness: 38 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', rtl } },
                scales: {
                    x: { reverse: rtl, grid: { display: false } },
                    y: { position: rtl ? 'right' : 'left', ticks: { callback: (v) => (v / 1e6).toLocaleString() + 'M' }, grid: { color: '#f1f5f9' } },
                    y1: { position: rtl ? 'left' : 'right', grid: { display: false } },
                },
            },
        });
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: { labels: data.statusLabels, datasets: [{ data: data.statusData, backgroundColor: ['#f59e0b', '#0ea5e9', '#6366f1', '#10b981', '#f43f5e'], borderWidth: 0 }] },
            options: { maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', rtl, labels: { boxWidth: 10, padding: 14 } } } },
        });
    }
});
