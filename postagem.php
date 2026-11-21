<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");

// Parâmetros da URL (/postagem/categoria/slug/id)
//
// O .htaccess repassa "categoria/slug/id" e, depois de um &, os parâmetros
// que vieram no link compartilhado (?fbclid=, ?utm_source=...). Só a parte
// antes do & interessa aqui.
$urlPath   = strtok($_SERVER['QUERY_STRING'] ?? '', '&');
$partes    = explode('/', trim((string) $urlPath, '/'));
$categoria = isset($partes[0]) ? $partes[0] : '';
$slug      = isset($partes[1]) ? $partes[1] : '';
$id        = isset($partes[2]) ? $partes[2] : '';

// Cada parte só pode ter o que a regra do .htaccess aceita. Acessando
// postagem.php direto dava para mandar qualquer texto, que ia parar no título
// da página e dentro do endereço da chamada à API.
$urlValida = preg_match('/^[a-zA-Z0-9_-]+$/', $categoria)
          && preg_match('/^[a-zA-Z0-9_-]+$/', $slug)
          && preg_match('/^[0-9]+$/', $id);

if (!$urlValida) {
    $categoria = $slug = $id = '';
    http_response_code(404);
}

// Postagem
$postagemURL  = BASE_URL . "/api/post-unico.php"
    . "?categoria=" . urlencode($categoria)
    . "&slug="      . urlencode($slug)
    . "&id="        . urlencode($id);
$postagemJson = $urlValida ? @file_get_contents($postagemURL) : false;
$postagem     = $postagemJson ? json_decode($postagemJson, true) : "erro";

// Relacionados
$tituloPost    = (is_array($postagem) && isset($postagem['titulo']))    ? $postagem['titulo']    : '';
$categoriaPost = (is_array($postagem) && isset($postagem['categoria'])) ? $postagem['categoria'] : $categoria;

$relacionadosURL  = BASE_URL . "/api/posts-relacionados.php"
    . "?id="        . urlencode($id)
    . "&titulo="    . urlencode($tituloPost)
    . "&categoria=" . urlencode($categoriaPost);
$relacionadosJson = $urlValida ? @file_get_contents($relacionadosURL) : false;
$relacionados     = $relacionadosJson ? json_decode($relacionadosJson, true) : [];

// Prévia do link e compartilhamento
// Alimenta as metatags do head.php (o que aparece ao colar o link no WhatsApp,
// Facebook...) e a barra "Compartilhar" (assets/views/compartilhar.php).
require_once __DIR__ . '/api/functions/functions.php';

$postEncontrado = is_array($postagem) && isset($postagem['titulo']);

if ($postEncontrado) {
    // Endereço oficial do post, com a categoria real — a da URL acessada pode
    // ser qualquer texto, já que o post é localizado pelo slug e pelo id.
    $urlPost = BASE_URL . '/postagem/' . slugify((string) $postagem['categoria']) . '/' . $slug . '/' . $id;

    $ogTitulo    = $postagem['titulo'];
    $ogDescricao = gerar_resumo((string) $postagem['conteudo']);
    $ogUrl       = $urlPost;
    $ogTipo      = 'article';

    // Capa: o WebP do site vai em JPG (api/capa-previa), formato que todas as
    // redes exibem. O ?v= muda quando a capa muda, e as redes buscam a imagem
    // de novo em vez de reaproveitar a antiga que guardaram.
    $capa        = parse_url((string) $postagem['imagem']);
    $caminhoCapa = $capa['path'] ?? '';
    parse_str($capa['query'] ?? '', $versaoCapa);

    if (preg_match('~^images/((?:img-youtube|img-post)/[^/]+\.webp)$~', $caminhoCapa, $achado)) {
        $ogImagem = BASE_URL . '/api/capa-previa?img=' . rawurlencode($achado[1])
                  . (isset($versaoCapa['v']) ? '&v=' . (int) $versaoCapa['v'] : '');
    } elseif (preg_match('~^images/[A-Za-z0-9/._-]+\.(jpe?g|png)$~i', $caminhoCapa)) {
        $ogImagem = BASE_URL . '/' . $caminhoCapa;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<?php
// título da aba
$title = $postEncontrado ? $postagem['titulo'] . ' - Blog Pablo Sato' : 'Postagem não encontrada - Blog Pablo Sato';
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">

      <?php if ($postagem != "erro"): ?>
        <div id="postagem" class="col-md-9 pt-4">

          <!-- Categoria + Tipo -->
          <div class="post-eyebrow">
            <a href="<?= BASE_URL ?>/resultados?pesquisa=<?= urlencode($postagem['categoria']) ?>" class="post-badge-categoria">
              <?= htmlspecialchars($postagem['categoria']) ?>
            </a>
            <span class="post-badge-tipo"><?= htmlspecialchars($postagem['tipo']) ?></span>
          </div>

          <!-- Título -->
          <h1 class="post-titulo"><?= htmlspecialchars($postagem['titulo']) ?></h1>

          <!-- Meta -->
          <div class="post-meta-bar">
            <span class="post-meta-chip">
              <i class="bi bi-person-fill"></i> <?= htmlspecialchars($postagem['autor']) ?>
            </span>
            <span class="post-meta-sep">·</span>
            <span class="post-meta-chip">
              <i class="bi bi-calendar3"></i> <?= htmlspecialchars($postagem['data']) ?>
            </span>
          </div>

          <!-- Imagem de capa -->
          <a href="<?= htmlspecialchars($postagem['urlimagem']) ?>" target="_blank" class="post-hero-link">
            <img
              src="<?= htmlspecialchars(BASE_URL . '/' . $postagem['imagem']) ?>"
              alt="<?= htmlspecialchars($postagem['titulo']) ?>"
              class="post-hero-img"
            >
          </a>

          <!-- Conteúdo -->
          <article class="post-content mb-5">
            <?= $postagem['conteudo'] ?>
          </article>

          <!-- Compartilhar -->
          <?php include __DIR__ . '/assets/views/compartilhar.php'; ?>

          <!-- Tags -->
          <?php if (!empty($postagem['tags']) && is_array($postagem['tags'])): ?>
            <section class="post-tags-section mb-5">
              <span class="post-tags-label"><i class="bi bi-tags-fill"></i> Tags</span>
              <div class="post-tags-list">
                <?php foreach ($postagem['tags'] as $tag): ?>
                  <a href="<?= BASE_URL ?>/resultados?pesquisa=<?= urlencode($tag) ?>" class="post-tag">
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
            <p>Desculpe, não conseguimos encontrar a postagem que você está procurando.</p>
            <hr>
            <p class="mb-0">Volte para a <a href="<?= BASE_URL ?>" class="alert-link">página inicial</a>.</p>
          </div>
        </div>
      <?php endif; ?>

      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>

    </div><!-- row -->
  </div>

  <?php include __DIR__ . '/assets/views/relacionados.php'; ?>

  <?php include __DIR__ . '/assets/views/footer.php'; ?>

  <?php if ($postEncontrado): ?>
  <script src="<?= BASE_URL ?>/assets/js/compartilhar.js?v=<?=time()?>"></script>
  <?php endif; ?>

</body>
