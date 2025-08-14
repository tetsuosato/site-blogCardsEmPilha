<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");

$pesquisa = NULL; // Padrão de pesquisa


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
$title = "Blog Pablo Sato"; // título da aba
include('assets/views/head.php');
?>

<body class="bg-light text-dark">

  <!-- Header e Navbar -->
  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <!-- DESTAQUES -->
  <?php include __DIR__ . '/assets/views/destaques.php'; ?>
  <!-- FINAL - DESTAQUES -->

  <div class="container">
    <div class="row">

      <!-- Coluna Principal -->
      <div class="col-md-9">
        <h3 class="pb-4 pt-4 mb-4 border-bottom">
          Últimas Postagens
        </h3>

        <div id="lista-postagens"></div>

        <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <button id="btn-carregar-mais" type="button" class="btn btn-outline-dark theme-outline-button">Conferir Mais</button>
        </div>


      </div><!-- col-md-9 -->
      <!-- FINAL - Coluna Principal -->

      <!-- Coluna Sobre -->
      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>
      <!-- FINAL - Coluna Sobre -->

    </div><!-- row -->
  </div>

  <!-- Footer -->
  <?php include __DIR__ . '/assets/views/footer.php'; ?>

  <!-- POSTS -->
  <script src="<?= BASE_URL ?>/assets/js/postsindex.js?v=<?=time()?>"></script>

</body>

</html>