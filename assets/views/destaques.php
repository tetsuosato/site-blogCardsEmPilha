<!-- DESTAQUES -->
<div class="container mt-4 mb-2">
    <h3 class="pb-2 mb-3 border-bottom fw-bold">Destaques</h3>
    <div class="row g-3">
        <?php
        $todos = array_merge(
            isset($destaques['destaque1']) && is_array($destaques['destaque1']) ? $destaques['destaque1'] : [],
            isset($destaques['destaque2']) && is_array($destaques['destaque2']) ? $destaques['destaque2'] : []
        );
        foreach ($todos as $post):
            $urlPost = htmlspecialchars(BASE_URL . '/postagem/' . $post['categoriaSlug'] . '/' . $post['slug'] . '/' . $post['id']);
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
                        <i class="bi bi-calendar3"></i> <?= htmlspecialchars($post['data']) ?>
                    </p>
                    <p class="card-text flex-grow-1"><?= htmlspecialchars($post['resumo']) ?></p>
                    <a href="<?= $urlPost ?>" class="card-read-more stretched-link mt-auto">Leia mais <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($todos)): ?>
            <p class="text-muted">Nenhum destaque encontrado.</p>
        <?php endif; ?>
    </div>
</div>
<!-- FINAL - DESTAQUES -->
