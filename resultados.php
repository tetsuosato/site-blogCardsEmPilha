<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");

$pesquisa = $_GET['pesquisa'] ?? '';
$pesquisa = trim($pesquisa);
$pesquisa = strip_tags($pesquisa);
$pesquisa = mb_substr($pesquisa, 0, 100);

if (empty($pesquisa)) {
    header("Location: " . BASE_URL);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<?php
$title = "Resultados - Blog Pablo Sato";
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">

      <div class="col-md-9">
        <h3 class="pb-4 pt-4 mb-4 border-bottom">Resultados da Pesquisa</h3>

        <div id="lista-postagens" class="row g-3"></div>

        <div class="d-grid mt-3 mb-4">
          <button id="btn-carregar-mais" type="button" class="btn btn-carregar-mais">
            Carregar mais posts <i class="bi bi-arrow-down-circle"></i>
          </button>
        </div>

      </div><!-- col-md-9 -->

      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>

    </div><!-- row -->
  </div>

  <?php include __DIR__ . '/assets/views/footer.php'; ?>

  <script src="<?= BASE_URL ?>/assets/js/resultados.js?v=<?=time()?>"></script>

</body>
