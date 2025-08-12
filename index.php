<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");



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
      <div class="col-md-9">
        <h3 class="pb-4 pt-4 mb-4 border-bottom">
          Últimas Postagens
        </h3>

        <div id="lista-postagens"></div>

        <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <button id="btn-carregar-mais" type="button" class="btn btn-outline-dark theme-outline-button">Conferir Mais</button>
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
      const container = document.getElementById('lista-postagens');
      const btnMais = document.getElementById('btn-carregar-mais');
      let currentPage = 1;
      const perPage = 10;
      let loading = false;
      let hasMore = true;

      function slugify(str) {
        return str
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)+/g, '');
      }

      function formatarData(data) {
        const d = new Date(data);
        return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {
          hour: '2-digit',
          minute: '2-digit'
        });
      }

      function montarPostHTML(post) {
        const url = `postagem/${slugify(post.categoria)}/${post.slug}/${post.id}`;

        // criar container do post
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
          <hr class="theme-hr">
          <div class="row flex-xd-column g-0 overflow-hidden flex-md-row mb-4 h-md-300 position-relative shadow-lg postagemindex">
            <div class="postagemindextitulo">
              <a href="${url}" class="text-reset text-decoration-none">
                <strong>${post.titulo}</strong>
              </a>
            </div>
            <div class="col-md-6 postagemindexcolimg">
              <a href="${url}" class="linkimagem">
                <img src="${post.imagem}?t=${new Date().getTime()}" class="img-fluid rounded" alt="${post.titulo}">
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
        return wrapper;
      }

      async function carregarPosts() {
        if (loading || !hasMore) return;
        loading = true;
        btnMais.disabled = true;
        btnMais.textContent = 'Carregando...';

        try {
          const resp = await fetch(`api/posts-index.php?page=${currentPage}&per_page=${perPage}/`);
          if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
          const data = await resp.json();

          if (!Array.isArray(data.posts)) {
            console.error('Resposta inesperada', data);
            return;
          }

          data.posts.forEach(post => {
            const postEl = montarPostHTML(post);
            container.appendChild(postEl);
          });

          hasMore = data.pagination.has_more;
          if (!hasMore) {
            btnMais.textContent = 'Não há mais posts';
            btnMais.disabled = true;
          } else {
            currentPage += 1;
            btnMais.textContent = 'Conferir Mais';
            btnMais.disabled = false;
          }
        } catch (err) {
          console.error('Erro ao carregar os posts:', err);
          btnMais.textContent = 'Tentar novamente';
          btnMais.disabled = false;
        } finally {
          loading = false;
        }
      }

      // evento do botão
      btnMais.addEventListener('click', carregarPosts);

      // carrega a primeira página automaticamente
      carregarPosts();
    });
  </script>

</body>

</html>