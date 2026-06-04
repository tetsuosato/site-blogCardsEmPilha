<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");

// Parâmetros da URL
$urlPath   = $_SERVER['QUERY_STRING'];
$partes    = explode('/', trim($urlPath, '/'));
$categoria = isset($partes[0]) ? $partes[0] : '';
$slug      = isset($partes[1]) ? $partes[1] : '';
$id        = isset($partes[2]) ? $partes[2] : '';

// Postagem
$postagemURL  = BASE_URL . "/api/post-unico.php?categoria=$categoria&slug=$slug&id=$id";
$postagemJson = @file_get_contents($postagemURL);
$postagem     = $postagemJson ? json_decode($postagemJson, true) : "erro";

// Relacionados
$tituloPost    = (is_array($postagem) && isset($postagem['titulo']))    ? $postagem['titulo']    : '';
$categoriaPost = (is_array($postagem) && isset($postagem['categoria'])) ? $postagem['categoria'] : $categoria;

$relacionadosURL  = BASE_URL . "/api/posts-relacionados.php"
    . "?id="        . urlencode($id)
    . "&titulo="    . urlencode($tituloPost)
    . "&categoria=" . urlencode($categoriaPost);
$relacionadosJson = @file_get_contents($relacionadosURL);
$relacionados     = $relacionadosJson ? json_decode($relacionadosJson, true) : [];
?>
<!DOCTYPE html>
<html lang="pt-br">

<?php
$title = "$slug - Blog Pablo Sato";
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">

      <?php if ($postagem != "erro"): ?>
        <div id="postagem" class="col-md-9 pt-4">

          <!-- Categoria + Tipo -->
          <div class="post-eyebrow">
            <a href="<?= BASE_URL ?>/resultados?pesquisa=<?= urlencode($postagem['categoria']) ?>" class="post-badge-categoria">
              <?= htmlspecialchars($postagem['categoria']) ?>
            </a>
            <span class="post-badge-tipo"><?= htmlspecialchars($postagem['tipo']) ?></span>
          </div>

          <!-- Título -->
          <h1 class="post-titulo"><?= htmlspecialchars($postagem['titulo']) ?></h1>

          <!-- Meta -->
          <div class="post-meta-bar">
            <span class="post-meta-chip">
              <i class="bi bi-person-fill"></i> <?= htmlspecialchars($postagem['autor']) ?>
            </span>
            <span class="post-meta-sep">·</span>
            <span class="post-meta-chip">
              <i class="bi bi-calendar3"></i> <?= htmlspecialchars($postagem['data']) ?>
            </span>
          </div>

          <!-- Imagem de capa -->
          <a href="<?= htmlspecialchars($postagem['urlimagem']) ?>" target="_blank" class="post-hero-link">
            <img
              src="<?= htmlspecialchars(BASE_URL . '/' . $postagem['imagem']) . '?t=' . time() ?>"
              alt="<?= htmlspecialchars($postagem['titulo']) ?>"
              class="post-hero-img"
            >
          </a>

          <!-- Conteúdo -->
          <article class="post-content mb-5">
            <?= $postagem['conteudo'] ?>
          </article>

          <!-- Tags -->
          <?php if (!empty($postagem['tags']) && is_array($postagem['tags'])): ?>
            <section class="post-tags-section mb-5">
              <span class="post-tags-label"><i class="bi bi-tags-fill"></i> Tags</span>
              <div class="post-tags-list">
                <?php foreach ($postagem['tags'] as $tag): ?>
                  <a href="<?= BASE_URL ?>/resultados?pesquisa=<?= urlencode($tag) ?>" class="post-tag">
                    <?= htmlspecialchars($tag) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </section>
          <?php endif; ?>

        </div><!-- col-md-9 -->
      <?php else: ?>
        <div class="col-md-9 mb-2 mt-4">
          <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Postagem não encontrada!</h4>
            <p>Desculpe, não conseguimos encontrar a postagem que você está procurando.</p>
            <hr>
            <p class="mb-0">Volte para a <a href="<?= BASE_URL ?>" class="alert-link">página inicial</a>.</p>
          </div>
        </div>
      <?php endif; ?>

      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>

    </div><!-- row -->
  </div>

  <?php include __DIR__ . '/assets/views/relacionados.php'; ?>

  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>
