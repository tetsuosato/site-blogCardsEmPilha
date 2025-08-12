<!-- DESTAQUES -->
<div class="container">
    <div class="row mb-2">
        
        <!-- Destaques 1 -->
        <div class="col-md-8">
            <h3 class="pb-2 pt-2 mb-2 border-bottom">Destaques 1</h3>

            <?php if (isset($destaques['destaque1']) && is_array($destaques['destaque1'])): ?>
                <?php foreach ($destaques['destaque1'] as $post): ?>
                <div class="card mb-3 shadow theme-card">
                    <div class="row g-0">
                    <div class="col-md-4">
                        <img src="<?= htmlspecialchars(BASE_URL . '/' . $post['imagem'] .'?t='.mt_rand(1, 1000)) ?>" class="img-fluid rounded p-2" alt="<?= htmlspecialchars($post['titulo']) ?>">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($post['titulo']) ?></h5>
                        <p>
                            <strong><?= htmlspecialchars($post['tipo']) ?></strong> - <strong class="text-secondary"><?= htmlspecialchars($post['categoria']) ?></strong>
                            <div class="mb-1 text-muted"><small><?= htmlspecialchars($post['autor']) ?></small> - <?= htmlspecialchars($post['data']) ?></div>
                        </p>
                        <p class="card-text"><?= htmlspecialchars($post['resumo']) ?></p>
                        <a href="<?= htmlspecialchars(BASE_URL . '/postagem/' . $post['categoriaSlug'] . '/' . $post['slug'] . '/' . $post['id']) ?>" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
                        </div>
                    </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Nenhum destaque encontrado.</p>
            <?php endif; ?>

        </div><!-- col-md-8 -->
        <!-- FINAL - Destaques 1 -->

        <!-- Destaques 2 -->
        <div class="col-sm-4 align-items-center justify-content-center">

            <h3 class="pb-2 pt-2 mb-2 border-bottom">Destaques 2</h3>

            <?php if (isset($destaques['destaque2']) && is_array($destaques['destaque2'])): ?>
                <?php foreach ($destaques['destaque2'] as $post): ?>
                <div class="card mb-3 shadow theme-card">
                    <img src="<?= htmlspecialchars(BASE_URL . '/' . $post['imagem'] .'?t='.mt_rand(1, 1000)) ?>" class="card-img-top rounded" alt="<?= htmlspecialchars($post['titulo']) ?>">
                    <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($post['titulo']) ?></h5>
                    <p>
                        <strong><?= htmlspecialchars($post['tipo']) ?></strong> - <strong class="text-secondary"><?= htmlspecialchars($post['categoria']) ?></strong>
                        <div class="mb-1 text-muted"><small><?= htmlspecialchars($post['autor']) ?></small> - <?= htmlspecialchars($post['data']) ?></div>
                    </p>
                    <p class="card-text"><?= htmlspecialchars($post['resumo']) ?></p>
                    <a href="<?= htmlspecialchars(BASE_URL . '/postagem/' . $post['categoriaSlug'] . '/' . $post['slug'] . '/' . $post['id']) ?>" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Nenhum destaque encontrado.</p>
            <?php endif; ?>

        </div><!-- col-sm-4 -->
        <!-- FINAL - Destaques 2 -->
        
    </div><!-- row mb-2 -->
</div><!-- container -->
<!-- FINAL - DESTAQUES -->