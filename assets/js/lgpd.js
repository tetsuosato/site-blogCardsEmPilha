(function () {
    var banner  = document.getElementById('banner-lgpd');
    var aceitar = document.getElementById('btn-lgpd-aceitar');
    var recusar = document.getElementById('btn-lgpd-recusar');

    if (!banner) return;

    if (!localStorage.getItem('lgpd_aceite')) {
        banner.style.display = 'block';
    }

    if (aceitar) {
        aceitar.addEventListener('click', function () {
            localStorage.setItem('lgpd_aceite', 'aceito');
            banner.style.display = 'none';
        });
    }

    if (recusar) {
        recusar.addEventListener('click', function () {
            localStorage.setItem('lgpd_aceite', 'recusado');
            banner.style.display = 'none';
        });
    }
})();
