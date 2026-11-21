<?php
/**
 * ÚLTIMAS POSTAGENS — as 3 mais recentes, no topo da home.
 *
 * Espera $ultimas: a resposta de api/posts-index.php?page=1&per_page=3, lida
 * no index.php. A lista "Mais Postagens" logo abaixo pede a mesma API com
 * pular=3, então continua exatamente do 4º post em diante.
 *
 * Sem posts (ou com a API fora do ar) a seção inteira some, em vez de mostrar
 * um título vazio.
 */
$postsTopo = (is_array($ultimas) && isset($ultimas['posts']) && is_array($ultimas['posts']))
    ? $ultimas['posts']
    : [];
?>
<?php if (!empty($postsTopo)): ?>
<!-- ÚLTIMAS POSTAGENS -->
<div class="container mt-4 mb-2">
    <h3 class="pb-2 mb-3 border-bottom fw-bold">Últimas Postagens</h3>
    <div class="row g-3">
        <?php foreach ($postsTopo as $post):
            $urlPost  = htmlspecialchars(BASE_URL . '/postagem/' . slugify((string) $post['categoria']) . '/' . $post['slug'] . '/' . $post['id']);
            $dataPost = date('d/m/Y H:i', strtotime($post['data']));
        ?>
        <div class="col-md-4">
            <div class="card h-100 theme-card">
                <div class="card-img-wrapper">
                    <img src="<?= htmlspecialchars(BASE_URL . '/' . $post['imagem']) ?>"
                         class="card-img-top" alt="<?= htmlspecialchars($post['titulo']) ?>">
                    <span class="card-categoria-badge"><?= htmlspecialchars($post['categoria']) ?></span>
                    <h5 class="card-img-title"><?= htmlspecialchars($post['titulo']) ?></h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-meta">
                        <i class="bi bi-person-fill"></i> <?= htmlspecialchars($post['autor']) ?>
                        &nbsp;&middot;&nbsp;
                        <i class="bi bi-calendar3"></i> <?= htmlspecialchars($dataPost) ?>
                    </p>
                    <p class="card-text flex-grow-1"><?= htmlspecialchars($post['resumo']) ?></p>
                    <a href="<?= $urlPost ?>" class="card-read-more stretched-link mt-auto">Leia mais <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<!-- FINAL - ÚLTIMAS POSTAGENS -->
<?php endif; ?>
