<!-- RELACIONADOS -->
<div class="container relacionados-section">
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="relacionados-titulo"><span>Você também pode gostar</span></h3>
            <div class="row">
                <?php foreach (array_slice($relacionados ?? [], 0, 3) as $post): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 theme-card">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars(BASE_URL . '/' . $post['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['titulo']) ?>">
                            <span class="card-categoria-badge"><?= htmlspecialchars($post['categoria']) ?></span>
                            <h5 class="card-img-title"><?= htmlspecialchars($post['titulo']) ?></h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="card-meta">
                                <i class="bi bi-person-fill"></i><?= htmlspecialchars($post['autor']) ?>
                                <span>·</span>
                                <i class="bi bi-calendar3"></i><?= htmlspecialchars($post['data']) ?>
                            </p>
                            <p class="card-text flex-grow-1"><?= htmlspecialchars($post['resumo']) ?></p>
                            <a href="<?= htmlspecialchars(BASE_URL . '/postagem/' . $post['categoriaSlug'] . '/' . $post['slug'] . '/' . $post['id']) ?>" class="card-read-more stretched-link mt-auto">
                                Leia mais <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- FINAL - RELACIONADOS -->
