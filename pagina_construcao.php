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
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/assets/css/styles.css?v=<?=time()?>">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
  <!-- fontawesome -->
  <script src="https://kit.fontawesome.com/ab3f175b89.js" crossorigin="anonymous"></script>

</head>

<body class="bg-light">
  <!-- Header e Navbar -->
  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container" style="height:1000px">
    <br><br><br>
    <center><h1>Página em construção</h1></center>
  </div>

  <!-- Footer -->
  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<!-- Scripts -->
<script src="<?= BASE_URL ?>/assets/js/scripts.js?v=<?=time()?>"></script>

</html>

