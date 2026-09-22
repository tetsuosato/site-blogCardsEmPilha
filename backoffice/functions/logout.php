<?php
require_once __DIR__ . '/../class/Sessao.php';
Sessao::iniciar();
require_once __DIR__ . '/../../lib/config.php';
require_once __DIR__ . '/../class/Database.php';

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
