// resources/js/app.js
import './bootstrap';
import Dropzone from "dropzone";

// Configuração global do Dropzone
window.Dropzone = Dropzone;
Dropzone.autoDiscover = false;

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initApp();
});

function initApp() {
    initAnimations();
    initInteractiveElements();
    initCharts();
    initFormValidations();
    initNotifications();
    initThemeManager();
    initCurrencyFormatting();
    initDateHandlers();
    initLoadingStates();
    initFileUpload(); // Adicionar esta função
}

// Função para inicializar upload de arquivos
function initFileUpload() {
    const dropzoneElement = document.getElementById('billet-dropzone');

    if (!dropzoneElement) return;

    new Dropzone('#billet-dropzone', {
        url: dropzoneElement.dataset.url || '/billet/upload',
        paramName: 'billet',
        maxFiles: 1,
        maxFilesize: 5,
        acceptedFiles: '.pdf,.jpg,.jpeg,.png,.webp',
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        init: function() {
            this.on('sending', function(file, xhr, formData) {
                document.getElementById('loading-overlay')?.classList.remove('hidden');
            });

            this.on('success', function(file, response) {
                document.getElementById('loading-overlay')?.classList.add('hidden');

                if (response.success) {
                    fillFormFields(response.data);
                    showNotification('Boleto processado com sucesso!', 'success');
                } else {
                    showNotification(response.message || 'Erro ao processar boleto', 'error');
                }

                setTimeout(() => this.removeFile(file), 2000);
            });

            this.on('error', function(file, error) {
                document.getElementById('loading-overlay')?.classList.add('hidden');
                showNotification('Erro ao processar arquivo', 'error');
                setTimeout(() => this.removeFile(file), 2000);
            });
        }
    });
}

// Função para preencher os campos do formulário
window.fillFormFields = function(data) {
    if (data.title && document.getElementById('title-input')) {
        document.getElementById('title-input').value = data.title;
    }
    if (data.amount && document.getElementById('amount-input')) {
        document.getElementById('amount-input').value = data.amount;
    }
    if (data.due_date && document.getElementById('due-date-input')) {
        document.getElementById('due-date-input').value = data.due_date;
    }
    if (data.barcode && document.getElementById('barcode-input')) {
        document.getElementById('barcode-input').value = data.barcode;
    }
}

// Função de notificação (caso não exista)
window.showNotification = window.showNotification || function(message, type = 'info') {
    alert(`${type.toUpperCase()}: ${message}`);
};
