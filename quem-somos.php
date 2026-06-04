<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Quem Somos - Blog Pablo Sato";
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">

      <div class="col-md-9">

        <h1 class="post-titulo pt-4">Quem Somos</h1>

        <div class="post-meta-bar mb-4">
          <span class="post-meta-chip"><i class="bi bi-person-fill"></i> Pablo Sato</span>
          <span class="post-meta-sep">·</span>
          <span class="post-meta-chip"><i class="bi bi-github"></i> tetsuosato</span>
        </div>

        <article class="post-content mb-5">

          <p>
            O <strong>Blog Pablo Sato</strong> é um espaço dedicado a compartilhar conhecimento sobre
            tecnologia, desenvolvimento de software e temas relacionados. O objetivo é simples: produzir
            conteúdo de qualidade para quem gosta de aprender e está em constante evolução.
          </p>

          <p>
            Aqui você encontra artigos, tutoriais e reflexões sobre programação, ferramentas e boas práticas
            do dia a dia de quem trabalha com tecnologia.
          </p>

          <h2>O que você encontra aqui</h2>

          <ul>
            <li><strong>Tutoriais práticos</strong> — passo a passo sobre linguagens, frameworks e ferramentas</li>
            <li><strong>Artigos técnicos</strong> — conceitos, boas práticas e aprofundamentos</li>
            <li><strong>Projetos e experimentos</strong> — o que está sendo desenvolvido e aprendido</li>
            <li><strong>Dicas e curiosidades</strong> — conteúdo rápido e direto ao ponto</li>
          </ul>

          <h2>Código aberto</h2>

          <p>
            Este blog é desenvolvido de forma pública. Você pode acompanhar o código-fonte, sugerir melhorias
            ou até usá-lo como base para o seu próprio projeto.
          </p>

          <div class="my-4">
            <a href="https://github.com/tetsuosato" target="_blank" rel="noopener"
               class="btn btn-dark fw-bold px-4 py-2">
              <i class="bi bi-github me-2"></i>Ver no GitHub
            </a>
          </div>

          <h2>Redes Sociais</h2>
          <p>Acompanhe e entre em contato pelas redes:</p>

        </article>

        <div class="row g-3 mb-5">
          <div class="col-6 col-md-4">
            <a href="https://www.youtube.com/@pablosato" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-youtube fs-3 text-danger"></i>
              <div>
                <div class="fw-bold small">YouTube</div>
                <div class="text-muted" style="font-size:0.75rem;">@pablosato</div>
              </div>
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="https://www.instagram.com/pablo_sato/" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-instagram fs-3" style="color:#E1306C;"></i>
              <div>
                <div class="fw-bold small">Instagram</div>
                <div class="text-muted" style="font-size:0.75rem;">@pablo_sato</div>
              </div>
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="https://github.com/tetsuosato" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-github fs-3"></i>
              <div>
                <div class="fw-bold small">GitHub</div>
                <div class="text-muted" style="font-size:0.75rem;">tetsuosato</div>
              </div>
            </a>
          </div>
          <div class="col-6 col-md-4">
            <a href="https://x.com/pablo_sato" target="_blank" rel="noopener"
               class="d-flex align-items-center gap-3 p-3 rounded-3 text-decoration-none quemsomos-social-card">
              <i class="bi bi-twitter-x fs-3"></i>
              <div>
                <div class="fw-bold small">X (Twitter)</div>
                <div class="text-muted" style="font-size:0.75rem;">@pablo_sato</div>
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
