<?php
require_once __DIR__ . '/../assets/views/auth-guard.php';

$paginaAtual  = 'home';
$tituloPagina = 'Dashboard';

$pdo = Database::get();

$totalPosts      = (int) $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$totalPublicados = (int) $pdo->query("SELECT COUNT(*) FROM posts WHERE `data` <= NOW()")->fetchColumn();
$totalAgendados  = $totalPosts - $totalPublicados;
$totalUsuarios   = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCategorias = (int) $pdo->query("SELECT COUNT(*) FROM categoria")->fetchColumn();

$porTipo = $pdo->query("
    SELECT t.Nome AS tipo, COUNT(p.id) AS total
    FROM tipo t
    LEFT JOIN posts p ON p.tipo = t.id
    GROUP BY t.id, t.Nome
    ORDER BY total DESC
")->fetchAll();

$topCategorias = $pdo->query("
    SELECT c.Nome AS categoria, COUNT(p.id) AS total
    FROM categoria c
    LEFT JOIN posts p ON p.categoria = c.id
    GROUP BY c.id, c.Nome
    HAVING total > 0
    ORDER BY total DESC, c.Nome ASC
    LIMIT 8
")->fetchAll();

$ultimosPosts = $pdo->query("
    SELECT p.id, p.titulo, p.`data`, p.imagem, p.slug,
           u.nickname AS autor, t.Nome AS tipo, c.Nome AS categoria
    FROM posts p
    LEFT JOIN users u ON u.id = p.autor
    LEFT JOIN tipo t ON t.id = p.tipo
    LEFT JOIN categoria c ON c.id = p.categoria
    ORDER BY p.`data` DESC
    LIMIT 8
")->fetchAll();

$maiorCategoria = $topCategorias ? (int) $topCategorias[0]['total'] : 0;

include __DIR__ . '/../assets/views/dash-topo.php';
?>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="bo-stat">
            <div class="bo-stat-icone bg-vermelho"><i class="bi bi-collection-play-fill"></i></div>
            <div>
                <div class="bo-stat-valor"><?= $totalPosts ?></div>
                <div class="bo-stat-rotulo">Postagens</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="bo-stat">
            <div class="bo-stat-icone bg-verde"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="bo-stat-valor"><?= $totalPublicados ?></div>
                <div class="bo-stat-rotulo">Publicadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="bo-stat">
            <div class="bo-stat-icone bg-laranja"><i class="bi bi-clock-fill"></i></div>
            <div>
                <div class="bo-stat-valor"><?= $totalAgendados ?></div>
                <div class="bo-stat-rotulo">Agendadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="bo-stat">
            <div class="bo-stat-icone bg-azul"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="bo-stat-valor"><?= $totalUsuarios ?></div>
                <div class="bo-stat-rotulo">Usuários</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="bo-painel h-100">
            <div class="bo-painel-cab">
                <h2 class="bo-painel-titulo">Postagens por tipo</h2>
            </div>
            <div class="bo-painel-corpo">
                <?php foreach ($porTipo as $linha): ?>
                    <?php $pct = $totalPosts > 0 ? round(($linha['total'] / $totalPosts) * 100) : 0; ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?= e($linha['tipo']) ?></span>
                            <span class="text-muted"><?= (int) $linha['total'] ?> (<?= $pct ?>%)</span>
                        </div>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-danger" style="width: <?= $pct ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Categorias cadastradas</span>
                    <strong><?= $totalCategorias ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="bo-painel h-100">
            <div class="bo-painel-cab">
                <h2 class="bo-painel-titulo">Categorias mais usadas</h2>
            </div>
            <div class="bo-painel-corpo">
                <?php if (!$topCategorias): ?>
                    <p class="text-muted mb-0">Nenhuma postagem categorizada ainda.</p>
                <?php else: ?>
                    <?php foreach ($topCategorias as $linha): ?>
                        <?php $pct = $maiorCategoria > 0 ? round(($linha['total'] / $maiorCategoria) * 100) : 0; ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span><?= e($linha['categoria']) ?></span>
                                <span class="text-muted"><?= (int) $linha['total'] ?></span>
                            </div>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="bo-painel">
    <div class="bo-painel-cab">
        <h2 class="bo-painel-titulo">Últimas postagens</h2>
        <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-sm btn-outline-secondary ms-auto">
            <i class="bi bi-box-arrow-up-right"></i> Ver no site
        </a>
    </div>
    <div class="table-responsive">
        <table class="table bo-tabela align-middle">
            <thead>
                <tr>
                    <th style="width:80px">Capa</th>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Autor</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosPosts as $post): ?>
                    <?php $agendado = strtotime($post['data']) > time(); ?>
                    <tr>
                        <td>
                            <img src="<?= BASE_URL ?>/images/img-youtube/<?= e($post['imagem']) ?>"
                                 alt="" class="bo-thumb" loading="lazy">
                        </td>
                        <td>
                            <?= e($post['titulo']) ?>
                            <?php if ($agendado): ?>
                                <span class="badge bg-warning text-dark ms-1">agendada</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?= e($post['categoria']) ?></span></td>
                        <td class="text-muted small"><?= e($post['tipo']) ?></td>
                        <td class="text-muted small"><?= e($post['autor']) ?></td>
                        <td class="text-muted small text-nowrap"><?= date('d/m/Y H:i', strtotime($post['data'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../assets/views/dash-rodape.php'; ?>
