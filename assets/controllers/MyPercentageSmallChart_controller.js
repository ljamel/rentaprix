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
        event.detail.chart.options.plugins.tooltip.callbacks.label = (ttItem) => `${ttItem.label}: ${ttItem.parsed}%`;
    }
}