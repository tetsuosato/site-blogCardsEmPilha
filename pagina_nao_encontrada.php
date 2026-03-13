<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Página não encontrada - Blog Pablo Sato"; // título da aba
include('assets/views/head.php');
?>


<style>
    .rock-title {
      font-size: 3rem;
      font-weight: 900;
      letter-spacing: 3px;
      color: #ff2b2b;
      text-shadow: 0 0 15px rgba(255, 43, 43, 0.7);
    }

    .btn-rock {
      background: #ff2b2b;
      border: none;
      font-weight: bold;
    }

    .btn-rock:hover {
      background: #ff4d4d;
    }
</style>

<body class="bg-light text-dark">
    <!-- Header e Navbar -->
    <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

    <br><br>
    <div class="d-flex align-items-center justify-content-center text-center h-100">
        <div class="container">

            <h1>404</h1>

            <h1 class="rock-title mb-3">Blog Pablo Sato</h1>

            <h2 class="mb-3">Página não encontrada</h2>

            <p class="mb-4">
            Ops! Página não encontrada!<br>
            A página que você procura não existe ou foi removida.
            </p>

            <a href="<?= BASE_URL ?>" class="btn btn-rock btn-lg px-4">
            Voltar para a Home
            </a>
        </div>
    </div>
    <br><br><br><br><br><br>

    <!-- Footer -->
    <?php include __DIR__ . '/assets/views/footer.php'; ?>
</body>
</html>