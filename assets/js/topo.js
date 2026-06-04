document.addEventListener('DOMContentLoaded', function () {
    var btnTopo  = document.getElementById('btnTopoBtn');
    var themeBtn = document.getElementById('toggleThemeBtn');
    var footer   = document.querySelector('footer');

    if (!btnTopo || !themeBtn) return;

    function corTema() {
        return document.body.classList.contains('bg-dark') ? '#ffc107' : '#212529';
    }

    function atualizarBotoes() {
        var scrollY = window.scrollY || window.pageYOffset;

        // Mostrar/ocultar botão de topo
        btnTopo.style.display = scrollY > 300 ? 'block' : 'none';

        // Cor dos botões conforme posição e tema
        var cor = corTema();
        if (footer) {
            var sobreFooter = footer.getBoundingClientRect().top < window.innerHeight;
            var temaClaro   = !document.body.classList.contains('bg-dark');
            if (sobreFooter && temaClaro) cor = '#ffc107';
        }
        btnTopo.style.color  = cor;
        themeBtn.style.color = cor;
    }

    // Voltar ao topo ao clicar
    btnTopo.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', atualizarBotoes);
    atualizarBotoes();
});
