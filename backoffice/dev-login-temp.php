<?php
if ($_SERVER['HTTP_HOST'] !== 'localhost') { http_response_code(404); exit; }
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Strict']);
session_start();
require_once __DIR__ . '/../lib/config.php';
require_once __DIR__ . '/class/Database.php';
$pdo = Database::get();
$u = $pdo->query("SELECT * FROM users WHERE ativo = 1 ORDER BY id LIMIT 1")->fetch();
$token = bin2hex(random_bytes(32));
$pdo->prepare("UPDATE users SET token = :t, token_expiry = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE id = :id")->execute([':t'=>$token,':id'=>$u['id']]);
$_SESSION['token']=$token; $_SESSION['id_user']=$u['id']; $_SESSION['user']=$u['user'];
$_SESSION['name']=$u['name']; $_SESSION['lastname']=$u['lastname']; $_SESSION['email']=$u['email'];
$_SESSION['ip']=$_SERVER['REMOTE_ADDR']; $_SESSION['ua']=$_SERVER['HTTP_USER_AGENT'];
header('Location: ' . BASE_URL . '/backoffice/main/');
