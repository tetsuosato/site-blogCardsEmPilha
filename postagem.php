<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");

$pesquisa = NULL; // Padrão de pesquisa

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


// ==================== LEITURA DA API DE DESTAQUES ====================

// Faz a leitura da Postagem via API
$destaquesURL = BASE_URL."/api/destaques.php";
$destaquesJson = @file_get_contents($destaquesURL);

// Verifica status HTTP
$statusCodeDestaques = null;
if (isset($http_response_header) && preg_match('{HTTP/\S+ (\d{3})}', $http_response_header[0], $match)) {
  $statusCodeDestaques = (int)$match[1];
}

if (!$destaquesJson) {
  $destaques = "erro";
} else{
  $destaques = json_decode($destaquesJson, true);
}
// ==================== LEITURA DA API DE DESTAQUES ====================


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
                  <a href="<?= BASE_URL ?>/resultados?pesquisa=<?=htmlspecialchars($tag)?>" class="badge bg-secondary text-decoration-none me-2 mb-2">
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


      <!-- Coluna Sobre -->
      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>
      <!-- FINAL - Coluna Sobre -->
      
    </div><!-- row -->
  </div>


  <!-- DESTAQUES -->
  <?php include __DIR__ . '/assets/views/destaques.php'; ?>
  <!-- FINAL - DESTAQUES -->


  <!-- Footer -->
  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>