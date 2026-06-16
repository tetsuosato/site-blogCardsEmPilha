<?php
session_start(); 
include('../lib/config.php');
require_once('class/LoginAuthentication.php');

// Verificar se o login
// Check if login
if(isset($_POST['acao'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    if(LoginAuthentication::authenticate($login, $password, $captcha)) {
        // Login bem-sucedido, redirecionar para a página inicial (index.php)
        // Login successful, redirect to home page (index.php)
        header('Location: main/');
        exit();
    } else {
        // Dados inválidos
        // Invalid data
        echo '<script src="js/password-invalid.js"></script>';
    }
}

// Se não estiver logado, exibir o formulário de login
// If not logged in, display the login form
if(isset($_SESSION['token'])){
    $token = $_SESSION['token'];

    if(!LoginAuthentication::validateToken($token)) {
        // Token inválido ou expirado, redirecionar para de aviso de expiração
        // Invalid or expired token, redirect to expiration warning
        include('session-expired');
        unset($_SESSION['login']);
        session_destroy();
        session_unset();
        exit();
    }else{
        header('Location: main/');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Backoffice | Blog Pablo Sato"; // título da aba
include('assets/views/head.php');
?>

<body class="bg-light text-dark">
    <header>
        <!-- place navbar here -->
    </header>
    <main></main>

    <div class="container col-sm-4 border border-2 bg-light rounded p-3 mx-auto mt-5 bg-light text-dark">
        <div class="text-center">
            <h4>Dev Pablo Tetsuo Sato</h4>
        </div>

        <!-- Alerta de Dados Inválidos -->
        <!-- Invalid Data Alert -->
        <div id="invalidDataAlert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
            Invalid data. Please check your credentials and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <form method="post">
            <div class="mb-3">
                <label for="login" class="form-label">E-mail</label>
                <input type="text" class="form-control" id="login" name="login" alt="Login" placeholder="Digite seu E-mail">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" alt="password" placeholder="Digite sua password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="row mb-3">
                <label for="captcha" class="form-label">Enter image content</label>
                <div class="col">
                    <img src="functions/captcha.php" alt="CAPTCHA">
                </div>
                
                <div class="col">
                    <input type="text" class="form-control" id="captcha" name="captcha">
                </div>
            </div>
            <div class="d-grid col-6 mx-auto">
                <button type="submit" class="btn btn-primary btn-block" name="acao" value="Login">Login</button>
            </div>
        </form>

        <hr>

    </div><!-- container -->

    
    <!-- Bootstrap JavaScript Libraries -->
    <!-- Footer -->
    <?php include __DIR__ . '/assets/views/footer.php'; ?>

    <!-- Scripts -->
    <script src="js/password-visible.js"></script>
</body>
</html>