// Barra "Compartilhar" da postagem (assets/views/compartilhar.php).
// Os botões das redes são links comuns; aqui ficam só o "Copiar link" e o
// menu de compartilhamento do próprio aparelho.
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.post-compartilhar').forEach(function (bloco) {
        var url    = bloco.dataset.url;
        var titulo = bloco.dataset.titulo;
        var aviso  = bloco.querySelector('.post-compartilhar-aviso');
        var timer  = null;

        function avisar(texto, erro) {
            if (!aviso) return;
            aviso.textContent = texto;
            aviso.classList.toggle('is-erro', !!erro);
            clearTimeout(timer);
            timer = setTimeout(function () { aviso.textContent = ''; }, 2500);
        }

        // --- Copiar link ---
        var copiar = bloco.querySelector('[data-compartilhar="copiar"]');
        if (copiar) {
            copiar.addEventListener('click', function () {
                copiarTexto(url).then(
                    function () { avisar('Link copiado!'); },
                    function () { avisar('Não foi possível copiar. Copie o endereço da barra do navegador.', true); }
                );
            });
        }

        // --- Menu do aparelho ---
        // Só em tela de toque: no computador o menu do sistema mais confunde
        // do que ajuda, e as redes já estão nos botões ao lado.
        var nativo = bloco.querySelector('[data-compartilhar="nativo"]');
        var toque  = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;

        if (nativo && navigator.share && toque) {
            nativo.hidden = false;
            nativo.addEventListener('click', function () {
                navigator.share({ title: titulo, url: url }).catch(function () {
                    // Cancelado pelo usuário: nada a fazer.
                });
            });
        }
    });

    /**
     * Copia para a área de transferência. A API moderna só existe em página
     * segura (https ou localhost) e alguns navegadores embutidos (o de apps,
     * por exemplo) a recusam; nesses casos tenta o método antigo.
     */
    function copiarTexto(texto) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(texto).catch(function () {
                return copiarMetodoAntigo(texto);
            });
        }

        return copiarMetodoAntigo(texto);
    }

    function copiarMetodoAntigo(texto) {
        return new Promise(function (resolve, reject) {
            var campo = document.createElement('textarea');
            campo.value = texto;
            campo.setAttribute('readonly', '');
            campo.style.position = 'fixed';
            campo.style.opacity = '0';
            document.body.appendChild(campo);
            campo.select();

            var ok = false;
            try { ok = document.execCommand('copy'); } catch (e) { ok = false; }

            document.body.removeChild(campo);
            ok ? resolve() : reject();
        });
    }
});
