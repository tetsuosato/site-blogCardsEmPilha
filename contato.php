<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Contato - Blog Pablo Sato";
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">

      <div class="col-md-9">

        <h1 class="post-titulo pt-4">Contato</h1>

        <p class="post-meta-bar mb-4">
          Fale comigo através de qualquer um dos canais abaixo.
        </p>

        <article class="post-content">

          <div class="contato-item mb-4">
            <div class="contato-icon"><i class="bi bi-envelope-fill"></i></div>
            <div>
              <h5 class="contato-titulo">E-mail</h5>
              <p class="contato-desc">Para parcerias, dúvidas ou sugestões:</p>
              <span class="contato-valor">contato@meusite.com</span>
            </div>
          </div>

          <hr class="my-4" style="border-color: rgba(0,0,0,0.08);">

          <h4 class="mb-3">Redes Sociais</h4>
          <p>Você também pode me encontrar e mandar mensagem diretamente pelas redes:</p>

        </article>

        <div class="row g-3 mb-5">
          <div class="col-12 col-md-6">
            <a href="https://www.youtube.com/@pablosato" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-youtube fs-2 text-danger"></i>
              <div>
                <div class="fw-bold">YouTube</div>
                <div class="text-muted small">youtube.com/@pablosato</div>
              </div>
            </a>
          </div>
          <div class="col-12 col-md-6">
            <a href="https://www.instagram.com/pablo_sato/" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-instagram fs-2" style="color:#E1306C;"></i>
              <div>
                <div class="fw-bold">Instagram</div>
                <div class="text-muted small">@pablo_sato</div>
              </div>
            </a>
          </div>
          <div class="col-12 col-md-6">
            <a href="https://github.com/tetsuosato" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-github fs-2"></i>
              <div>
                <div class="fw-bold">GitHub</div>
                <div class="text-muted small">github.com/tetsuosato</div>
              </div>
            </a>
          </div>
          <div class="col-12 col-md-6">
            <a href="https://x.com/pablo_sato" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-twitter-x fs-2"></i>
              <div>
                <div class="fw-bold">X (Twitter)</div>
                <div class="text-muted small">@pablo_sato</div>
              </div>
            </a>
          </div>
        </div>

      </div><!-- col-md-9 -->

      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>

    </div><!-- row -->
  </div>

  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>
