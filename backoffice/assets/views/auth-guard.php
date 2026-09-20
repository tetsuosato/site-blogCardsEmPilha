<?php
// Protege as páginas internas do backoffice.
// Inclua no topo de qualquer arquivo em main/ antes de qualquer saída.

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

require_once __DIR__ . '/../../../lib/config.php';
require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../class/LoginAuthentication.php';

$backofficeUrl = BASE_URL . '/backoffice';

// Endpoints chamados por JavaScript definem $guardaJson = true antes de incluir
// este arquivo: um redirecionamento devolveria HTML onde a tela espera JSON.
$guardaJson = isset($guardaJson) && $guardaJson;

/** Encerra a sessão e interrompe a requisição no formato que o chamador espera. */
function guarda_encerrar($backofficeUrl, $emJson) {
    session_unset();
    session_destroy();

    if ($emJson) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'   => false,
            'erro' => 'Sessão expirada. Entre novamente.',
        ], JSON_UNESCAPED_UNICODE);
    } else {
        header('Location: ' . $backofficeUrl . '/session-expired');
    }

    exit;
}

// Sessão sequestrada: o IP ou o navegador mudaram no meio da sessão.
if (isset($_SESSION['ip'], $_SESSION['ua'])) {
    if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['ua'] !== $_SERVER['HTTP_USER_AGENT']) {
        guarda_encerrar($backofficeUrl, $guardaJson);
    }
} else {
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['ua'] = $_SERVER['HTTP_USER_AGENT'];
}

if (empty($_SESSION['token']) || !LoginAuthentication::validateToken($_SESSION['token'])) {
    guarda_encerrar($backofficeUrl, $guardaJson);
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function csrf_token() {
    return $_SESSION['csrf'];
}

function csrf_valid($token) {
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
