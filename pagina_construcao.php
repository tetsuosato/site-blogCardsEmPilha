<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="pt-br">

<?php
$title = "Blog Pablo Sato - Página em construção"; // título da aba
include('assets/views/head.php');
?>

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

</html>

