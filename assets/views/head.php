<?php
// Metatags da prévia do link (WhatsApp, Facebook, X...).
// A página pode definir antes de incluir este arquivo: $ogTitulo, $ogDescricao,
// $ogImagem, $ogUrl e $ogTipo. A página de postagem usa todas, para o link
// compartilhado mostrar o título, o resumo e a capa do próprio post. Sem elas
// valem os textos padrão do site, como antes.
$metaEsc       = function ($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); };
$metaTitulo    = isset($ogTitulo) ? $ogTitulo : 'Blog Pablo Sato';
$metaDescricao = isset($ogDescricao) && $ogDescricao !== '' ? $ogDescricao : 'Blog Pablo Sato';
$metaImagem    = isset($ogImagem) ? $ogImagem : BASE_URL . '/assets/image/logo_menu.png';
$metaUrl       = isset($ogUrl) ? $ogUrl : BASE_URL;
$metaTipo      = isset($ogTipo) ? $ogTipo : 'website';
?>
<head>
  <title><?= htmlspecialchars(isset($title) ? $title : 'Blog Pablo Sato', ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="icon" href="<?= BASE_URL ?>/assets/image/favicon.png">

  <!-- Required meta tags -->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Pablo Sato">
  <meta name="keywords" content="Blog Pablo Sato, elaborar as keywords">
  <meta name="robots" content="index, follow">

  <!-- Open Graph Tags -->
  <?php if (isset($ogDescricao) && $ogDescricao !== ''): ?>
  <meta name="description" content="<?= $metaEsc($ogDescricao) ?>">
  <?php endif; ?>
  <meta property="og:title" content="<?= $metaEsc($metaTitulo) ?>">
  <meta property="og:description" content="<?= $metaEsc($metaDescricao) ?>">
  <meta property="og:image" content="<?= $metaEsc($metaImagem) ?>">
  <meta property="og:url" content="<?= $metaEsc($metaUrl) ?>">
  <meta property="og:type" content="<?= $metaEsc($metaTipo) ?>">
  <meta property="og:site_name" content="Blog Pablo Sato">
  <meta property="og:locale" content="pt_BR">

  <!-- Twitter Card Tags -->
  <!-- Com a capa do post a prévia usa imagem grande; nas demais páginas, a logo pequena -->
  <meta name="twitter:card" content="<?= isset($ogImagem) ? 'summary_large_image' : 'summary' ?>">
  <meta name="twitter:site" content="@pablo_sato">
  <meta name="twitter:title" content="<?= $metaEsc($metaTitulo) ?>">
  <meta name="twitter:description" content="<?= $metaEsc($metaDescricao) ?>">
  <meta name="twitter:image" content="<?= $metaEsc($metaImagem) ?>">
  <meta name="twitter:url" content="<?= $metaEsc($metaUrl) ?>">

  <?php if (isset($ogUrl)): ?>
  <!-- Endereço oficial desta página (evita conteúdo duplicado no Google) -->
  <link rel="canonical" href="<?= $metaEsc($ogUrl) ?>">
  <?php endif; ?>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" crossorigin="anonymous" href="<?= BASE_URL ?>/assets/bootstrap/5.3.8/css/bootstrap.min.css">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap-icons/1.13.1/bootstrap-icons.css">

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/assets/css/styles.css?v=<?=time()?>">

  <script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
