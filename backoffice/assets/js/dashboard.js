document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('bo-overlay');
    var toggle = document.getElementById('bo-toggle');

    function fecharMenu() {
        sidebar.classList.remove('aberta');
        overlay.classList.remove('ativo');
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('aberta');
            overlay.classList.toggle('ativo');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', fecharMenu);
    }

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            fecharMenu();
        }
    });

    var verSenha = document.getElementById('togglePassword');
    if (verSenha) {
        verSenha.addEventListener('click', function () {
            var campo = document.getElementById('password');
            campo.type = campo.type === 'password' ? 'text' : 'password';
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    }

    // ----- Confirmação via modal (substitui o confirm() do navegador) -----
    var elementoModal = document.getElementById('modalConfirmacao');
    if (!elementoModal) {
        return;
    }

    var modal      = new bootstrap.Modal(elementoModal);
    var elTitulo   = document.getElementById('modalConfirmacaoTitulo');
    var elMensagem = document.getElementById('modalConfirmacaoMensagem');
    var elOk       = document.getElementById('modalConfirmacaoOk');
    var origem     = null;

    function prepararModal(elemento) {
        elTitulo.textContent   = elemento.dataset.confirmarTitulo || 'Confirmação';
        elMensagem.textContent = elemento.dataset.confirmar;
        elOk.textContent       = elemento.dataset.confirmarOk || 'Confirmar';
        elOk.className         = 'btn btn-' + (elemento.dataset.confirmarTipo || 'danger');
        origem = elemento;
        modal.show();
    }

    // Forms: intercepta o submit.
    document.querySelectorAll('form[data-confirmar]').forEach(function (form) {
        form.addEventListener('submit', function (evento) {
            if (form.dataset.confirmado === 'sim') {
                return;
            }
            evento.preventDefault();
            prepararModal(form);
        });
    });

    // Links e botões: intercepta o clique.
    document.querySelectorAll('a[data-confirmar], button[data-confirmar]').forEach(function (elemento) {
        elemento.addEventListener('click', function (evento) {
            evento.preventDefault();
            prepararModal(elemento);
        });
    });

    elOk.addEventListener('click', function () {
        if (!origem) {
            return;
        }

        modal.hide();

        if (origem.tagName === 'FORM') {
            origem.dataset.confirmado = 'sim';
            origem.submit();
        } else if (origem.dataset.url) {
            window.location.href = origem.dataset.url;
        } else if (origem.href) {
            window.location.href = origem.href;
        }
    });

    elementoModal.addEventListener('hidden.bs.modal', function () {
        origem = null;
    });
});
