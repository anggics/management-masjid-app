import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Daftarkan Service Worker (PWA)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((err) => {
            console.warn('Service worker gagal didaftarkan:', err);
        });
    });
}

// Helper: salin teks ke clipboard (nomor rekening)
window.copyToClipboard = async function (text, el) {
    try {
        await navigator.clipboard.writeText(text);
        if (el) {
            const original = el.innerText;
            el.innerText = 'Tersalin!';
            setTimeout(() => (el.innerText = original), 1500);
        }
    } catch (e) {
        console.warn('Gagal menyalin', e);
    }
};
