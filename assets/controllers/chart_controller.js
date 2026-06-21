import { Controller } from '@hotwired/stimulus';

/*
 * Контроллер для управления диаграммами Chart.js.
 */
export default class extends Controller {
    static targets = ['chart', 'legend'];
    static values = {
        type: String,
        data: Object,
        currency: { type: String, default: '₽' }
    };

    connect() {
        this.initChart();
    }

    disconnect() {
        if (this.chart) {
            this.chart.destroy();
        }
    }

    initChart() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js не загружен');
            return;
        }

        const chartData = this.dataValue;
        if (!chartData || !chartData.labels || !chartData.data) {
            console.error('Данные для диаграммы некорректны');
            return;
        }

        if (this.typeValue === 'pie') {
            this.createPieChart(chartData);
        } else if (this.typeValue === 'line') {
            this.createLineChart(chartData);
        }
    }

    createPieChart(data) {
        const ctx = this.chartTarget;
        if (!ctx) return;

        this.chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                let comment = context.label;

                                if (comment === 'Service') {
                                    comment = 'Service (все сервисы)';
                                } else if (comment.length > 20) {
                                    comment = comment.substring(0, 20) + '...';
                                }

                                return comment + ': ' + (value / 100).toFixed(2) + ' ' + this.currencyValue + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        this.renderCustomLegend(this.chart);
    }

    createLineChart(data) {
        const ctx = this.chartTarget;
        if (!ctx || data.labels.length === 0) {
            console.error('Canvas не найден или нет данных');
            return;
        }

        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    // label: 'Расходы',
                    data: data.data,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                return (context.parsed.y / 100).toFixed(2) + ' ' + this.currencyValue;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => {
                                return (value / 100).toFixed(0) + ' ' + this.currencyValue;
                            }
                        }
                    }
                }
            }
        });
    }

    renderCustomLegend(chart) {
        if (!this.hasLegendTarget) return;

        const container = this.legendTarget;
        const data = chart.data;
        const labels = data.labels;
        const dataset = data.datasets[0];
        const total = dataset.data.reduce((a, b) => a + b, 0);

        let html = '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-1 row-cols-lg-2 g-2" style="width:calc(100% - 5px)">';
        labels.forEach((label, i) => {
            const value = dataset.data[i];
            const percentage = ((value / total) * 100).toFixed(1);
            const color = dataset.backgroundColor[i];
            html += `
                <div class="col">
                    <div class="d-flex align-items-center item">
                        <span class="icon" style="background:${color};"></span>
                        <span title="${label}">${label}</span>
                        <span class="ms-auto text-muted">${(value / 100).toFixed(2)} ${this.currencyValue} (${percentage}%)</span>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }
}