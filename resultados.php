<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");


// Recebe a pesquisa do usuário
$pesquisa = $_GET['pesquisa'] ?? '';
$pesquisa = trim($pesquisa); // remove espaços extras
$pesquisa = strip_tags($pesquisa); // remove HTML
$pesquisa = mb_substr($pesquisa, 0, 100); // limita tamanho

// Verifica se a pesquisa não está vazia
if (empty($pesquisa)) {
  header("Location: " . BASE_URL);
  exit;
}


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
$title = "Resultados - Blog Pablo Sato"; // título da aba
include('assets/views/head.php');
?>


<body class="bg-light text-dark">

  <!-- Header e Navbar -->
  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <input type="hidden" id="pesquisaDigitado" value="<?= htmlspecialchars($pesquisa, ENT_QUOTES, 'UTF-8') ?>">
    <div class="row">

      <!-- Resultados -->
      <div class="col-md-9">
        <h3 class="pb-4 pt-4 mb-4 border-bottom">
          Resultados da Pesquisa
        </h3>

        <div id="lista-postagens"></div>

        <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
          <button id="btn-carregar-mais" type="button" class="btn btn-outline-dark theme-outline-button">Conferir Mais</button>
        </div>

      </div><!-- col-md-9 -->
      <!-- FINAL - Resultados -->

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

  <!-- Resultados -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // pega a pesquisa da URL
      const urlParams = new URLSearchParams(window.location.search);
      const pesquisa = urlParams.get('pesquisa') || '';

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
              const resp = await fetch(`api/posts-resultados.php?page=${currentPage}&per_page=${perPage}&pesquisa=${encodeURIComponent(pesquisa)}`);
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