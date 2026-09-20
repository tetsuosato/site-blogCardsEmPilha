<?php
// $paginaAtual é definido por cada página antes do include.
$paginaAtual = isset($paginaAtual) ? $paginaAtual : '';
$menu = BASE_URL . '/backoffice/main';

$itens = [
    ['slug' => 'home',      'url' => $menu . '/',                  'icone' => 'bi-speedometer2',   'texto' => 'Dashboard',        'obra' => false],
    ['slug' => 'postagens', 'url' => $menu . '/postagens',         'icone' => 'bi-collection',     'texto' => 'Postagens',        'obra' => false],
    ['slug' => 'video',     'url' => $menu . '/postagem-video',    'icone' => 'bi-youtube',        'texto' => 'Postar Vídeo',     'obra' => false],
    ['slug' => 'artigo',    'url' => $menu . '/postagem-artigo',   'icone' => 'bi-file-earmark-text', 'texto' => 'Postar Artigo', 'obra' => true],
    ['slug' => 'usuarios',  'url' => $menu . '/usuarios',          'icone' => 'bi-people-fill',    'texto' => 'Usuários',         'obra' => false],
    ['slug' => 'grupos',    'url' => $menu . '/grupos',            'icone' => 'bi-shield-lock',    'texto' => 'Grupos e Permissões', 'obra' => true],
];
?>
<nav id="sidebar" class="bo-sidebar">
    <a href="<?= $menu ?>/" class="bo-sidebar-brand">
        <img src="<?= BASE_URL ?>/backoffice/assets/image/logo.png" alt="Blog Pablo Sato">
        <span>Backoffice</span>
    </a>

    <ul class="bo-nav">
        <?php foreach ($itens as $item): ?>
            <li>
                <a href="<?= $item['url'] ?>" class="bo-nav-link<?= $paginaAtual === $item['slug'] ? ' active' : '' ?>">
                    <i class="bi <?= $item['icone'] ?>"></i>
                    <span><?= $item['texto'] ?></span>
                    <?php if ($item['obra']): ?>
                        <i class="bi bi-cone-striped bo-nav-obra" title="Em construção"></i>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="bo-sidebar-rodape">
        <a href="<?= BASE_URL ?>" target="_blank" class="bo-nav-link">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Ver o site</span>
        </a>
        <a href="<?= BASE_URL ?>/backoffice/functions/logout.php" class="bo-nav-link bo-nav-sair"
           data-confirmar="Tem certeza que deseja sair do sistema?"
           data-confirmar-titulo="Sair do sistema"
           data-confirmar-ok="Sair">
            <i class="bi bi-power"></i>
            <span>Sair</span>
        </a>
    </div>
</nav>
<div id="bo-overlay" class="bo-overlay"></div>
