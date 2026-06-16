<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true, // só HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];

if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
    $_SESSION['ua'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_destroy();
    exit;
}

require_once('../../lib/config.php');
require_once('../class/LoginAuthentication.php');

if (empty($_SESSION['token']) || 
    !LoginAuthentication::validateToken($_SESSION['token'])) {

    session_destroy();
    header('Location: ../session-expired');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<?php
$title = "Backoffice | Home | Blog Pablo Sato"; // título da aba
include('../assets/views/head.php');
?>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main></main>
    <div class="container bg-light">
        <h1>WELCOME TO THE SYSTEM!</h1>
        <h2>Instagram: @pablo_sato</h2>
        <h3><a href="https://github.com/tetsuosato" target="_blank" rel="Siga-me no Github">Follow me on GitHub</a></h3>
        <p>Hello <?= $_SESSION['name'].' '.$_SESSION['lastname'] ?></p>
        <br><br>
        <a href="#" id="logout">Exit</a>
    </div>
    
    <!-- Função Sair do sistema -->
    <?php include('../modals/exit.html'); ?>
    <script src="../js/exit.js"></script>


    <!-- Bootstrap JavaScript Libraries -->
    <!-- Footer -->
    <?php include('../assets/views/footer.php'); ?>

</body>
</html>








