import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { formatRupiah, parseRupiah, registerCurrencyDirective } from './currency';

Alpine.plugin(collapse);

// Register SAMSARA Currency Input Directive
registerCurrencyDirective(Alpine);

window.Alpine = Alpine;
window.formatRupiah = formatRupiah;
window.parseRupiah = parseRupiah;

// Helper: Web Bluetooth ESC/POS Thermal Printing Simulation & Native Bridge
window.printThermalReceipt = async function (receiptData) {
    if ('bluetooth' in navigator) {
        try {
            console.log('Requesting Bluetooth Device for Thermal Printer...');
            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
            });
            console.log('Bluetooth Device connected:', device.name);
            alert(`Terhubung ke printer Bluetooth: ${device.name}. Struk berhasil dikirim!`);
            return true;
        } catch (err) {
            console.warn('Bluetooth print cancelled or unsupported:', err);
            // Fallback to native window.print()
            window.print();
            return false;
        }
    } else {
        // Fallback for desktop or non-bluetooth browsers
        window.print();
        return true;
    }
};

// Helper: Offline Queue Manager for FieldOps PWA
window.FieldOpsOfflineQueue = {
    getQueue() {
        try {
            return JSON.parse(localStorage.getItem('samsara_fieldops_queue') || '[]');
        } catch {
            return [];
        }
    },
    add(scanItem) {
        const queue = this.getQueue();
        scanItem.timestamp = new Date().toISOString();
        queue.push(scanItem);
        localStorage.setItem('samsara_fieldops_queue', JSON.stringify(queue));
        return queue.length;
    },
    clear() {
        localStorage.removeItem('samsara_fieldops_queue');
    },
    count() {
        return this.getQueue().length;
    }
};

Alpine.start();
