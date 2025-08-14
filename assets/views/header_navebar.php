<!-- Header com ícones de redes sociais -->
<header class="bg-dark text-light text-center py-3 bg-dark">
    <a href="https://www.youtube.com/@pablosato"    class="text-light mx-2 redesocial" target="_blank" aria-label="Youtube"   title="Inscreva-se em nosso canal no Youtube"><i class="bi bi-youtube   fs-4"></i></a>
    <a href="https://www.instagram.com/pablo_sato"  class="text-light mx-2 redesocial" target="_blank" aria-label="Instagram" title="Siga-nos no Instagram">                <i class="bi bi-instagram fs-4"></i></a>
    <a href="https://github.com/tetsuosato"         class="text-light mx-2 redesocial" target="_blank" aria-label="Facebook"  title="Curti a nossa Fanpage">                <i class="bi bi-facebook  fs-4"></i></a>
    <a href="https://github.com/tetsuosato"         class="text-light mx-2 redesocial" target="_blank" aria-label="TikTok"    title="Siga-nos no TikTok">                   <i class="bi bi-tiktok    fs-4"></i></a>
    <a href="https://github.com/tetsuosato"         class="text-light mx-2 redesocial" target="_blank" aria-label="X-Twitter" title="Siga-nos no X-Twitter">                <i class="bi bi-twitter-x fs-4"></i></a>
</header>

<!-- Navbar -->
<nav class="navbar sticky-top fixed-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="<?=BASE_URL?>">
                <span class="img-fluid img-logo">Blog Pablo Sato</span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <form class="d-flex form-inline" action="<?=BASE_URL?>/resultados.php" method="get" role="search">
                <input name="pesquisa" class="form-control form-control-sm" type="search" placeholder="Pesquisar" value="" aria-label="Search">
                <button class="btn btn-outline-secondary btn-sm" type="submit"> <i class="bi bi-search"></i></button>
            </form>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-auto">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="<?=BASE_URL?>"><label class="navlabel">Home</label></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <label class="navlabel">Opções</label>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?=BASE_URL?>/pagina_construcao.php" target="_blank">Opções 1</a></li>
                        <li><a class="dropdown-item" href="<?=BASE_URL?>/pagina_construcao.php" target="_blank">Opções 2</a></li>
                        <!-- DEMAIS -->
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?=BASE_URL?>/pagina_construcao.php">Opções 3</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?=BASE_URL?>/pagina_construcao.php"><label class="navlabel">Quem Somos</label></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?=BASE_URL?>/pagina_construcao.php"><label class="navlabel">Contato</label></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <label class="navlabel">Recomendações</label>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                        <li><a class="dropdown-item" href="<?=BASE_URL?>/pagina_construcao.php">Recomendações 1</a></li>
                        <li><a class="dropdown-item" href="<?=BASE_URL?>/pagina_construcao.php">Recomendações 2</a></li>
                        <li><a class="dropdown-item" href="<?=BASE_URL?>/pagina_construcao.php">Recomendações 3</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>