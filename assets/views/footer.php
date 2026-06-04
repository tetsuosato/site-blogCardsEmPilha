<!-- Banner LGPD / Cookies -->
<div id="banner-lgpd" class="position-fixed bottom-0 start-0 end-0 bg-dark text-light p-3 shadow-lg" style="z-index:1055; display:none;">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <p class="mb-0 small">
            <i class="bi bi-shield-lock me-1"></i>
            Usamos cookies para melhorar sua experiência e analisar o tráfego do site, em conformidade com a
            <strong>LGPD (Lei nº 13.709/2018)</strong>. Ao continuar navegando, você concorda com nossa
            <a href="<?= BASE_URL ?>/politica-privacidade" class="text-warning text-decoration-underline">Política de Privacidade</a>.
        </p>
        <div class="d-flex gap-2 flex-shrink-0">
            <button id="btn-lgpd-recusar" class="btn btn-outline-light btn-sm">Recusar</button>
            <button id="btn-lgpd-aceitar" class="btn btn-warning btn-sm text-dark fw-bold">Aceitar</button>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/lgpd.js?v=<?=time()?>"></script>

<!-- Botões Fixos: Voltar ao Topo + Trocar Tema -->
<div class="position-fixed bottom-0 end-0 mb-3 me-3 d-flex flex-column align-items-center gap-2">
    <div id="btnTopoBtn" title="Voltar ao topo" style="cursor:pointer; display:none;">
        <i class="bi bi-arrow-up-circle-fill fs-2"></i>
    </div>
    <div id="toggleThemeBtn" title="Trocar Tema">
        <i id="toggleThemeIcon" class="bi bi-moon-fill fs-2"></i><br>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/topo.js?v=<?=time()?>"></script>

<footer class="container-fluid text-light rodape">

    <!-- Redes sociais -->
    <div class="text-center pt-4 pb-3">
        <a href="https://www.youtube.com/@pablosato"       class="redesocial" target="_blank" aria-label="Youtube"   title="Canal no Youtube">   <i class="bi bi-youtube   fs-5"></i></a>
        <a href="https://www.instagram.com/pablo_sato/"    class="redesocial" target="_blank" aria-label="Instagram" title="Siga no Instagram">   <i class="bi bi-instagram fs-5"></i></a>
        <a href="https://github.com/tetsuosato"            class="redesocial" target="_blank" aria-label="GitHub"    title="Perfil no GitHub">    <i class="bi bi-github    fs-5"></i></a>
        <a href="https://x.com/pablo_sato"                 class="redesocial" target="_blank" aria-label="X-Twitter" title="Siga no X-Twitter">   <i class="bi bi-twitter-x fs-5"></i></a>
    </div>

    <!-- Links -->
    <div class="text-center pb-2">
        <ul class="nav justify-content-center footer-links">
            <li><a href="<?= BASE_URL ?>" class="nav-link">Home</a></li>
            <li><a href="<?= BASE_URL ?>/politica-privacidade" class="nav-link">Política de Privacidade</a></li>
        </ul>
    </div>

    <!-- Copyright -->
    <div class="text-center pb-4 footer-copy">
        Blog Pablo Sato &copy; <?= date('Y') ?> &nbsp;·&nbsp;
        Site feito por <a href="https://www.instagram.com/pablo_sato/" target="_blank" class="text-warning text-decoration-none fw-semibold">pablo_sato</a>
    </div>

</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="<?= BASE_URL ?>/assets/bootstrap/5.3.8/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- Scripts -->
<script src="<?= BASE_URL ?>/assets/js/scripts.js?v=<?=time()?>"></script>
<script src="<?= BASE_URL ?>/assets/js/sidebar.js?v=<?=time()?>"></script>
