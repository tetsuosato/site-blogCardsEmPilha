<?php
require_once __DIR__ . '/class/Sessao.php';
Sessao::iniciar();
require_once __DIR__ . '/../lib/config.php';

session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Sessão expirada | Backoffice Blog Pablo Sato";
include __DIR__ . '/assets/views/head.php';
?>

<body class="bo-body">
    <div class="bo-login">
        <div class="bo-login-card text-center">
            <i class="bi bi-clock-history" style="font-size:2.5rem;color:#f0ad4e"></i>
            <h1 class="h5 mt-3">Sua sessão expirou</h1>
            <p class="text-muted">Por segurança, você foi desconectado. Faça login novamente para continuar.</p>
            <a href="<?= BASE_URL ?>/backoffice/" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-in-right"></i> Ir para o login
            </a>
        </div>
    </div>
</body>
</html>
