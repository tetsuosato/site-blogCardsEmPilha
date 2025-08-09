<?php
header('Content-Type: application/json; charset=utf-8');
include('../lib/config.php');

mb_internal_encoding('UTF-8');

try {
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        // Se não informar id válido, retorna erro
        http_response_code(400);
        echo json_encode(['error' => 'Parâmetro id inválido ou não informado']);
        exit;
    }

    $sql = "SELECT * FROM posts WHERE id = :id LIMIT 1";
    $stmt = $connection->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $post = $stmt->fetch();

    if (!$post) {
        // Post não encontrado
        http_response_code(404);
        echo json_encode(['error' => 'Post não encontrado']);
        exit;
    }

    // Decodifica tags se for JSON válido
    if (isset($post['tags']) && is_string($post['tags'])) {
        $decoded = json_decode($post['tags'], true);
        $post['tags'] = is_array($decoded) ? $decoded : [];
    } else {
        $post['tags'] = [];
    }

    $caminhoImagem = 'imagesposts/' . $post['imagem'];

    $dataOriginal = $post['data'];
    $dataFormatada = date('d/m/Y H:i', strtotime($dataOriginal));

    $response = [
        'titulo' => $post['titulo'],
        'autor' => $post['autor'],
        'data' => $dataFormatada,
        'tipo' => $post['tipo'],
        'categoria' => $post['categoria'],
        'imagem' => $caminhoImagem,
        'urlimagem' => isset($post['urlimagem']) ? $post['urlimagem'] : null,
        'conteudo' => $post['conteudo'], // Se vier do usuário, sanitize no front-end
        'tags' => $post['tags'],
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao consultar o banco de dados',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
