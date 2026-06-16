<?php
$password = '1';
$passwordCriptografada = password_hash($password, PASSWORD_DEFAULT);

echo 'password Normal:'.$password . '<br>';
echo 'password Criptografada:'.$passwordCriptografada;