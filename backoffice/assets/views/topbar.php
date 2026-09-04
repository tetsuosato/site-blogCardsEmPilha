<?php
$tituloPagina = isset($tituloPagina) ? $tituloPagina : 'Dashboard';
$nomeUsuario  = trim(($_SESSION['name'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''));
?>
<header class="bo-topbar">
    <button class="bo-toggle" id="bo-toggle" type="button" aria-label="Abrir menu">
        <i class="bi bi-list"></i>
    </button>

    <h1 class="bo-topbar-titulo"><?= e($tituloPagina) ?></h1>

    <div class="dropdown ms-auto">
        <button class="bo-usuario dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
            <span class="d-none d-sm-inline"><?= e($nomeUsuario) ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-item-text text-muted small"><?= e($_SESSION['email'] ?? '') ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= BASE_URL ?>/backoffice/main/usuarios">Usuários</a></li>
            <li>
                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/backoffice/functions/logout.php"
                   data-confirmar="Tem certeza que deseja sair do sistema?"
                   data-confirmar-titulo="Sair do sistema"
                   data-confirmar-ok="Sair">Sair</a>
            </li>
        </ul>
    </div>
</header>
