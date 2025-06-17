<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Blog Pablo Sato - Postagem</title>
  <link rel="icon" href="assets/image/favicon.png">

  <!-- Required meta tags -->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Pablo Sato">
  <meta name="keywords" content="Blog Pablo Sato, elaborar as keywords">
  <meta name="robots" content="index, follow">

  <!-- Open Graph Tags -->
  <meta name="Blog do Pablo Sato - Postagem - elborar">
  <meta property="og:title" content="Blog Pablo Sato - Postagem">
  <meta property="og:description" content="Blog Pablo Sato - Postagem">
  <meta property="og:image" content="https://ENDERECOSITE/assets/image/logo_menu.png">
  <meta property="og:url" content="https://ENDERECOSITE">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Blog Pablo Sato">

  <!-- Twitter Card Tags -->
  <meta name="twitter:card" content="summary">
  <meta name="twitter:site" content="@BlogPabloSato">
  <meta name="twitter:title" content="Blog Pablo Sato">
  <meta name="twitter:description" content="Blog Pablo Sato DESCRICAO">
  <meta name="twitter:image" content="https://ENDERECOSITEassets/image/logo_menu.png">
  <meta name="twitter:url" content="https://ENDERECOSITE">

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="assets/css/styles.css?v=1.0">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
  <!-- fontawesome -->
  <script src="https://kit.fontawesome.com/ab3f175b89.js" crossorigin="anonymous"></script>

</head>

<body class="bg-light text-dark">

  <!-- Header e Navbar -->
  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">
      <!-- POSTAGEM -->
      <div class="col-md-9">
        <div id="postagem"></div>
      </div><!-- col-md-9 -->
      <!-- POSTAGEM -->

      <div class="col-md-3 g-0 mb-4 mt-2 border rounded shadow colunafixaindexcol">
        <div class="position-sticky" style="top: 3rem;">
          <div class="p-4 mb-3 rounded">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Sobre</h4>
            <p class="mb-0"><em>Aqui descreve do que se trata o blog</em></p>
          </div>

          <div class="p-4">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Veja mais</h4>
            <ol class="list-unstyled mb-0">
              <li><a href="pagina_construcao.html" target="_blank" class="theme-link">Tags de postagens 1</a></li>
              <li><a href="pagina_construcao.html" target="_blank" class="theme-link">Postagens antigas 1</a></li>
              <li><a href="pagina_construcao.html" target="_blank" class="theme-link">Postagens antigas 2</a></li>
            </ol>
          </div>

          <div class="p-4">
            <h4 class="pb-2 pt-2 mb-2 border-bottom">Confira Também</h4>
            <ol class="list-unstyled">
              <li><a href="pagina_construcao.html" target="_blank" class="theme-link">Outras Recomendações 1</a></li>
              <li><a href="pagina_construcao.html" target="_blank" class="theme-link">Outras Recomendações 2</a></li>
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

  <!-- Footer -->
  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!-- Scripts -->
<script src="assets/js/scripts.js?v=1.0"></script>

<script>
function getURLParams() {
  const params = new URLSearchParams(window.location.search);
  return {
    categoria: params.get('categoria'),
    slug: params.get('slug'),
    id: params.get('id')
  };
}

document.addEventListener('DOMContentLoaded', function() {
  const { categoria, slug, id } = getURLParams();

  const postagemDiv = document.getElementById('postagem');

  if (!categoria || !slug || !id) {
    postagemDiv.innerHTML = '<p>Postagem não encontrada ou parâmetros inválidos.</p>';
    return;
  }

  fetch(`get-post.php?categoria=${categoria}&slug=${slug}&id=${id}`)
    .then(response => response.json())
    .then(post => {
      let conteudoHTML = `
        <h1 class="pb-3 pt-4 mb-2 border-bottom">${post.titulo}</h1>
        <p class="meta-info mb-1">
          <strong>Por:</strong> ${post.autor} |
          <strong>Publicado em:</strong> ${formatarData(post.data)} |
          <strong>Categoria:</strong> ${post.categoria}
        </p>
        <img src="${post.imagem}" alt="${post.titulo}" class="img-fluid rounded mb-4 mt-2 shadow d-block mx-auto w-75">
        <article class="mb-5">
      `;

      post.conteudo.forEach(paragrafo => {
        conteudoHTML += `<p>${paragrafo}</p>`;
      });

      conteudoHTML += `</article>`;

      // Adicionar as tags
      let tagsHTML = '';
      post.tags.forEach(tag => {
        tagsHTML += `<a href="pagina_construcao.html" class="badge bg-secondary text-decoration-none me-2 mb-2">${tag}</a>`;
      });

      conteudoHTML += `
        <section class="mt-4 mb-5">
          <h5>Tags:</h5>
          <div>${tagsHTML}</div>
        </section>
      `;

      postagemDiv.innerHTML = conteudoHTML;
    })
    .catch(error => {
      postagemDiv.innerHTML = '<p>Erro ao carregar a postagem.</p>';
      console.error(error);
    });

  function formatarData(data) {
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }
});
</script>

</html>
