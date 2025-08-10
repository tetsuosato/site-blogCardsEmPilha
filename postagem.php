<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");


// ==================== RECEBE E RETORNA A POSTAGEM ====================
// Recebe os parametros da URL
$urlPath = $_SERVER['QUERY_STRING'];
$partes = explode('/', trim($urlPath, '/'));
$categoria = isset($partes[0]) ? $partes[0] : '';
$slug = isset($partes[1]) ? $partes[1] : '';
$id = isset($partes[2]) ? $partes[2] : '';

// Faz a leitura da Postagem via API
$postagemURL = BASE_URL."/api/post-unico.php?categoria=$categoria&slug=$slug&id=$id";
$postagemJson = @file_get_contents($postagemURL);

// Verifica status HTTP
$statusCode = null;
if (isset($http_response_header) && preg_match('{HTTP/\S+ (\d{3})}', $http_response_header[0], $match)) {
  $statusCode = (int)$match[1];
}

if (!$postagemJson) {
  $postagem = "erro";
} else{
  $postagem = json_decode($postagemJson, true);
}
// ==================== FINAL - RECEBE E RETORNA A POSTAGEM ====================

?>
<!DOCTYPE html>
<html lang="pt-br">

<?php
$title = "$slug - Blog Pablo Sato"; // título da aba
include('assets/views/head.php');
?>


<body class="bg-light text-dark">

  <!-- Header e Navbar -->
  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">
      <!-- POSTAGEM -->
      <?php if ($postagem != "erro"): ?>
        <div id="postagem" class="col-md-9">
          <h1 class="pb-3 pt-4 mb-2 border-bottom"><?= htmlspecialchars($postagem['titulo']) ?></h1>

          <p class="meta-info mb-1">
            <strong>Por:</strong> <?= htmlspecialchars($postagem['autor']) ?> |
            <strong>Publicado em:</strong> <?= htmlspecialchars($postagem['data']) ?> |
            <strong><?= htmlspecialchars($postagem['tipo']) ?></strong> - <?= htmlspecialchars($postagem['categoria']) ?>
          </p>

          <a href="<?= htmlspecialchars($postagem['urlimagem']) ?>" target="_blank">
            <img 
              src="<?= htmlspecialchars(BASE_URL . '/' . $postagem['imagem']) . '?t=' . time() ?>" 
              alt="<?= htmlspecialchars($postagem['titulo']) ?>" 
              class="img-fluid rounded mb-4 mt-2 shadow d-block mx-auto w-75"
            >
          </a>

          <article class="col-md-12 mb-5">
            <?= $postagem['conteudo'] ?>
          </article>

          <!-- Tags da Postagem -->
          <?php if (!empty($postagem['tags']) && is_array($postagem['tags'])): ?>
            <section class="mt-4 mb-5">
              <h5>Tags:</h5>
              <div>
                <?php foreach ($postagem['tags'] as $tag): ?>
                  <a href="pagina_construcao.html" class="badge bg-secondary text-decoration-none me-2 mb-2">
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
            <p>Desculpe, não conseguimos encontrar a postagem que você está procurando. Ela pode ter sido removida ou o link pode estar incorreto.</p>
            <hr> 
            <p class="mb-0">Por favor, verifique o link ou volte para a <a href="<?= BASE_URL ?>" class="alert-link">página inicial</a>.</p>
          </div>
        </div>
      <?php endif; ?>
      <!-- FIM - POSTAGEM -->


      <div class="col-md-3 g-0 mb-4 mt-2 border rounded shadow colunafixaindexcol">
        <div class="position-sticky" style="top: 3rem;">
          <div class="p-4 mb-3 rounded">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Sobre</h4>
            <p class="mb-0"><em>Aqui descreve do que se trata o blog</em></p>
          </div>

          <div class="p-4">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Veja mais</h4>
            <ol class="list-unstyled mb-0">
              <li><a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="theme-link">Tags de postagens 1</a></li>
              <li><a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="theme-link">Postagens antigas 1</a></li>
              <li><a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="theme-link">Postagens antigas 2</a></li>
            </ol>
          </div>

          <div class="p-4">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Confira Também</h4>
            <ol class="list-unstyled">
              <li><a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="theme-link">Outras Recomendações 1</a></li>
              <li><a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="theme-link">Outras Recomendações 2</a></li>
            </ol>
          </div>
        </div>
      </div>
      
    </div><!-- row -->
  </div>

  <!-- DESTAQUES -->
  <div class="container">
    <div class="row mb-2">
      <!-- Destaques 1 -->
      <div class="col-md-8">
        <h3 class="pb-2 pt-2 mb-2 border-bottom">Destaques 1</h3>
        <!-- 1º Destaque 1 -->
        <div class="card mb-3 shadow theme-card">
          <div class="row g-0">
            <div class="col-md-4">
              <img src="<?=BASE_URL?>/imagesposts\postagem10.webp" class="img-fluid rounded p-2" alt="Destaque 1">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">Título para colocar aqui</h5>
                <p>
                  <strong>Artigo</strong> - <strong class="text-secondary">Dicas</strong>
                  <div class="mb-1 text-muted"><small>Pablo Sato</small> - 13/06/2025 17:30</div>
                </p>
                <p class="card-text">Texto Resumo da postagem. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar. </p>
                <a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
              </div>
            </div>
          </div>
        </div>

        <!-- 2º Destaque 1 -->
        <div class="card mb-3 shadow theme-card">
          <div class="row g-0">
            <div class="col-md-4">
              <img src="<?=BASE_URL?>/imagesposts\postagem8.webp" class="img-fluid rounded p-2" alt="Destaque 1">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">Título da postagem</h5>
                <p>
                  <strong>Artigo</strong> - <strong class="text-secondary">Dicas</strong>
                  <div class="mb-1 text-muted"><small>Pablo Sato</small> - 13/06/2025 17:30</div>
                </p>
                <p class="card-text">Texto Resumo da postagem. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar.</p>
                <a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
              </div>
            </div>
          </div>
        </div>

      </div><!-- col-md-6 -->
      <!-- FINAL - Destaques 1 -->

      <!-- Destaques 2 -->
      <div class="col-sm-4 align-items-center justify-content-center">

        <h3 class="pb-2 pt-2 mb-2 border-bottom">Destaques 2</h3>

        <div class="card shadow theme-card">
          <img src="<?=BASE_URL?>/imagesposts/postagem9.webp" class="card-img-top rounded" alt="Destaque 2">
          <div class="card-body">
            <h5 class="card-title">TITULO DO DESTAQUE 2</h5>
            <p>
              <strong>Artigo</strong> - <strong class="text-secondary">Dicas</strong>
              <div class="mb-1 text-muted"><small>Pablo Sato</small> - 13/06/2025 17:30</div>
            </p>
            <p class="card-text">Texto Resumo da postagem. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar.</p>
            <a href="<?= BASE_URL ?>/pagina_construcao.php" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
          </div>
        </div>

      </div><!-- col-md-6 -->
      <!-- FINAL - Destaques 2 -->
    </div><!-- row mb-2 -->
  </div><!-- container -->
  <!-- FINAL - DESTAQUES -->

  <!-- Footer -->
  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>