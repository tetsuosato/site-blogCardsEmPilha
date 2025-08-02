<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
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
  <div class="container">
    <div class="row mb-2">
      <!-- Destaques 1 -->
      <div class="col-md-8">
        <h3 class="pb-2 pt-2 mb-2 border-bottom">Destaques 1</h3>
        <!-- 1º Destaque 1 -->
        <div class="card mb-3 shadow theme-card">
          <div class="row g-0">
            <div class="col-md-4">
              <img src="imagesposts\postagem10.webp" class="img-fluid rounded p-2" alt="Destaque 1">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">Título para colocar aqui</h5>
                <p>
                  <strong>Artigo</strong> - <strong class="text-secondary">Dicas</strong>
                  <div class="mb-1 text-muted"><small>Pablo Sato</small> - 13/06/2025 17:30</div>
                </p>
                <p class="card-text">Texto Resumo da postagem. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar. </p>
                <a href="postagem.html" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
              </div>
            </div>
          </div>
        </div>

        <!-- 2º Destaque 1 -->
        <div class="card mb-3 shadow theme-card">
          <div class="row g-0">
            <div class="col-md-4">
              <img src="imagesposts\postagem8.webp" class="img-fluid rounded p-2" alt="Destaque 1">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">Título da postagem</h5>
                <p>
                  <strong>Artigo</strong> - <strong class="text-secondary">Dicas</strong>
                  <div class="mb-1 text-muted"><small>Pablo Sato</small> - 13/06/2025 17:30</div>
                </p>
                <p class="card-text">Texto Resumo da postagem. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar.</p>
                <a href="postagem.html" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
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
          <img src="imagesposts/postagem9.webp" class="card-img-top rounded" alt="Destaque 2">
          <div class="card-body">
            <h5 class="card-title">TITULO DO DESTAQUE 2</h5>
            <p>
              <strong>Artigo</strong> - <strong class="text-secondary">Dicas</strong>
              <div class="mb-1 text-muted"><small>Pablo Sato</small> - 13/06/2025 17:30</div>
            </p>
            <p class="card-text">Texto Resumo da postagem. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar.</p>
            <a href="postagem.html" target="_blank" class="btn btn-dark theme-button">Leia mais</a>
          </div>
        </div>

      </div><!-- col-md-6 -->
      <!-- FINAL - Destaques 2 -->
    </div><!-- row mb-2 -->
  </div><!-- container -->
  <!-- FINAL - DESTAQUES -->

  <div class="container">
    <div class="row">
      <div class="col-md-9">
        <h3 class="pb-4 pt-4 mb-4 border-bottom">
          Últimas Postagens
        </h3>

        <div id="lista-postagens"></div>

        <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <a href="pagina_construcao.html" type="button" target="_blank" class="btn btn-outline-dark theme-outline-button">Conferir Mais</a>
        </div>

      </div><!-- col-md-8 -->

      <div class="col-md-3 g-0 mb-4 mt-2 border rounded shadow colunafixaindexcol">
        <div class="position-sticky" style="top: 3rem;">
          <div class="p-4 mb-3 rounded">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Sobre</h4>
            <p class="mb-0"><em>Aqui descreve do que se trata o blog</em></p>
          </div>

          <div class="p-4">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Veja mais</h4>
            <ol class="list-unstyled mb-0">
              <li><a href="<?= BASE_URL ?>/pagina_construcao.html" target="_blank" class="theme-link">Tags de postagens 1</a></li>
              <li><a href="<?= BASE_URL ?>/pagina_construcao.html" target="_blank" class="theme-link">Postagens antigas 1</a></li>
              <li><a href="<?= BASE_URL ?>/pagina_construcao.html" target="_blank" class="theme-link">Postagens antigas 2</a></li>
            </ol>
          </div>

          <div class="p-4">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Confira Também</h4>
            <ol class="list-unstyled">
              <li><a href="<?= BASE_URL ?>/pagina_construcao.html" target="_blank" class="theme-link">Outras Recomendações 1</a></li>
              <li><a href="<?= BASE_URL ?>/pagina_construcao.html" target="_blank" class="theme-link">Outras Recomendações 2</a></li>
            </ol>
          </div>
        </div>
      </div>

    </div><!-- row -->
  </div>

  <!-- Footer -->
  <?php include __DIR__ . '/assets/views/footer.php'; ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      fetch('posts-json.php')
        .then(response => response.json())
        .then(posts => {
          const container = document.getElementById('lista-postagens');
          container.innerHTML = '';

          posts.forEach(post => {
            // const url = `postagem.php?categoria=${slugify(post.categoria)}&slug=${post.slug}&id=${post.id}`;
            const url = `postagem/${slugify(post.categoria)}/${post.slug}/${post.id}`;

            const postHTML = `
              <hr class="theme-hr">
              <div class="row flex-xd-column g-0 overflow-hidden flex-md-row mb-4 h-md-300 position-relative shadow-lg postagemindex">
                <div class="postagemindextitulo">
                  <a href="${url}" class="text-reset text-decoration-none">
                    <strong>${post.titulo}</strong>
                  </a>
                </div>
                <div class="col-md-6 postagemindexcolimg">
                  <a href="${url}" class="linkimagem">
                    <img src="${post.imagem}" class="img-fluid rounded" alt="Thumbnail">
                  </a>
                </div>
                <div class="col-md-6 postagemindexcol">
                  <p>
                    <strong>${post.tipo}</strong> - <strong class="text-secondary">${post.categoria}</strong>
                    <div class="mb-1 text-muted"><small>${post.autor}</small> - ${formatarData(post.data)}</div>
                  </p>
                  <p>${post.resumo}</p>
                  <a href="${url}" class="btn btn-dark mt-2 theme-button">Continue lendo</a>
                </div>
              </div>
            `;
            container.innerHTML += postHTML;
          });
        })
        .catch(error => console.error('Erro ao carregar os posts:', error));

      function formatarData(data) {
        const d = new Date(data);
        return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {
          hour: '2-digit',
          minute: '2-digit'
        });
      }

      function slugify(str) {
        return str
          .normalize('NFD')                    
          .replace(/[\u0300-\u036f]/g, '')    
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')        
          .replace(/(^-|-$)+/g, '');          
      }
    });
  </script>

</body>

</html>