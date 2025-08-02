<?php
header('Content-Type: application/json; charset=utf-8');
include('config/config.php');



// Conectar ao banco de dados usando PDO
// Connect to database using PDO
$connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

// Configurar PDO para lançar exceções em caso de erros
// Configure PDO to throw exceptions in case of errors
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$query = "SELECT * FROM posts WHERE `id`= 1";
$result = $connection->query($query);

if ($result->rowCount() == 1) {
    $post = $result->fetch(PDO::FETCH_ASSOC);
}


// === Se o campo tags estiver armazenado como JSON no banco, decodifica; caso contrário, adapta ===
if (is_string($post['tags'])) {
    $decoded = json_decode($post['tags'], true);
    $post['tags'] = is_array($decoded) ? $decoded : [];
}

// === Formata o retorno como você tinha no exemplo ===
$response = [
    'titulo' => $post['titulo'],
    'autor' => $post['autor'],
    'data' => $post['data'],
    'categoria' => $post['categoria'],
    'imagem' => $post['imagem'],
    'altimagem' => $post['altimagem'],
    'conteudo' => $post['conteudo'], // cuidado: se isso vier do usuário, sanitize antes de exibir no front
    'tags' => $post['tags'],
];

// === Envia resposta ===
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
