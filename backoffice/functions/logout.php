<?php
require_once __DIR__ . '/../class/Sessao.php';
Sessao::iniciar();
require_once __DIR__ . '/../../lib/config.php';
require_once __DIR__ . '/../class/Database.php';

// Só sai por POST e com o token CSRF da sessão. Como link (GET), qualquer
// página — inclusive de outro site — podia desconectar o usuário só por
// carregar uma imagem apontando para cá. Pedido fora disso volta para o
// painel sem mexer na sessão.
$csrfSessao = $_SESSION['csrf'] ?? '';
$csrfEnviado = $_POST['csrf'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !is_string($csrfEnviado)
    || $csrfSessao === ''
    || !hash_equals($csrfSessao, $csrfEnviado)) {
    header('Location: ' . BASE_URL . '/backoffice/');
    exit;
}

// Invalida o token no banco para que ele não possa ser reaproveitado.
if (!empty($_SESSION['id_user'])) {
    Database::get()
        ->prepare("UPDATE users SET token = '', token_expiry = '1000-01-01 00:00:00' WHERE id = :id")
        ->execute([':id' => $_SESSION['id_user']]);
}

session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/backoffice/');
exit;
