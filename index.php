<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");

// slugify(), usado para montar o endereço de cada post na seção do topo
require_once __DIR__ . '/api/functions/functions.php';

// As 3 mais recentes vêm da mesma API da lista "Mais Postagens", que as pula
// (pular=3 no postsindex.js). Mesma fonte e mesma ordem: nenhum post repete
// nem fica de fora entre as duas seções.
$ultimasJson = @file_get_contents(BASE_URL . "/api/posts-index.php?page=1&per_page=3");
$ultimas     = $ultimasJson ? json_decode($ultimasJson, true) : null;

// Na home o bloco "Recentes" da lateral repetiria as 3 postagens do topo.
$ocultarRecentes = true;
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Blog Pablo Sato";
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <!-- ÚLTIMAS POSTAGENS -->
  <?php include __DIR__ . '/assets/views/ultimas-postagens.php'; ?>

  <div class="container">
    <div class="row">

      <div class="col-md-9">
        <!-- Continua a partir da 4ª postagem; some se não houver nenhuma -->
        <section id="secao-mais-postagens">
          <h3 class="pb-4 pt-4 mb-4 border-bottom">Mais Postagens</h3>

          <div id="lista-postagens" class="row g-3"></div>

          <div class="d-grid mt-2 mb-4">
            <button id="btn-carregar-mais" type="button" class="btn btn-carregar-mais">
              Carregar mais posts <i class="bi bi-arrow-down-circle"></i>
            </button>
          </div>
        </section>

      </div><!-- col-md-9 -->

      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>

    </div><!-- row -->
  </div>

  <?php include __DIR__ . '/assets/views/footer.php'; ?>

  <script src="<?= BASE_URL ?>/assets/js/postsindex.js?v=<?=time()?>"></script>

</body>
</html>
