<?php
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/class/LoginAuthentication.php';

$painel = BASE_URL . '/backoffice/main/';
$falhou = false;

if (isset($_POST['acao'])) {
    $login    = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha  = $_POST['captcha'] ?? '';

    if (LoginAuthentication::authenticate($login, $password, $captcha)) {
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
        header('Location: ' . $painel);
        exit();
    }

    $falhou = true;
}

if (isset($_SESSION['token'])) {
    if (LoginAuthentication::validateToken($_SESSION['token'])) {
        header('Location: ' . $painel);
        exit();
    }

    session_unset();
    session_destroy();
    header('Location: ' . BASE_URL . '/backoffice/session-expired');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Backoffice | Blog Pablo Sato";
include __DIR__ . '/assets/views/head.php';
?>

<body class="bo-body">
    <div class="bo-login">
        <div class="bo-login-card">
            <div class="text-center mb-4">
                <img src="<?= BASE_URL ?>/backoffice/assets/image/logo.png"
                     alt="Blog Pablo Sato" class="bo-login-logo">
                <h1 class="h5 mt-3 mb-0">Backoffice</h1>
                <p class="text-muted small mb-0">Blog Pablo Sato</p>
            </div>

            <?php if ($falhou): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Dados inválidos. Verifique suas credenciais e tente novamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
                <div class="mb-3">
                    <label for="login" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="login" name="login"
                           placeholder="Digite seu e-mail" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Digite sua senha" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="captcha" class="form-label">Digite o conteúdo da imagem</label>
                    <div class="d-flex gap-2 align-items-center">
                        <img src="<?= BASE_URL ?>/backoffice/functions/captcha.php" alt="CAPTCHA" class="rounded border">
                        <input type="text" class="form-control" id="captcha" name="captcha" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100" name="acao" value="Login">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </button>
            </form>

            <hr class="my-4">
            <a href="<?= BASE_URL ?>" class="d-block text-center text-muted small text-decoration-none">
                <i class="bi bi-arrow-left"></i> Voltar ao site
            </a>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/bootstrap/5.3.8/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= BASE_URL ?>/backoffice/assets/js/dashboard.js?v=<?= time() ?>"></script>
</body>
</html>
