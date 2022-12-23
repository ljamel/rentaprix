import {Controller} from '@hotwired/stimulus';
export default class extends Controller {

    connect() {
        this.element.addEventListener('chartjs:connect', this._onConnect);
    }


    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side effects
        this.element.removeEventListener('chartjs:connect', this._onConnect);
    }

    _onConnect(event) {
        event.detail.chart.options.plugins.tooltip.enabled = true;
        event.detail.chart.options.plugins.tooltip.callbacks = (context) => {
            let label = context.dataset.label || '';

            if (label) {
                label += ': ';
            }
            if (context.parsed.y !== null) {
                label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
            }
            return label;
        }

        event.detail.chart.options.plugins.legend.labels.generateLabels = (chart) => {
            const datasets = chart.data.datasets;
            return datasets[0].data.map((data, i) => ({
                text: `${chart.data.labels[i]} ${data} €`,
                fillStyle: datasets[0].backgroundColor[i],
            }))
        };
    }
}