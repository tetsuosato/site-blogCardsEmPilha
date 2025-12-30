<?php
date_default_timezone_set('America/Sao_Paulo');

header('Content-Type: application/json; charset=utf-8');
include('../lib/config.php');
include('functions/functions.php');

mb_internal_encoding('UTF-8');

try {
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // fuso horário Brasil
    $connection->exec("SET time_zone = '-03:00'");

    // paginação
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
        'options' => ['default' => 1, 'min_range' => 1]
    ]);
    $per_page = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT, [
        'options' => ['default' => 10, 'min_range' => 1, 'max_range' => 50]
    ]);

    // total para paginação
    $countSql = "SELECT COUNT(*) FROM posts p WHERE p.`data` <= NOW()";
    $countStmt = $connection->prepare($countSql);
    $countStmt->execute();
    $total = (int) $countStmt->fetchColumn();

    // consulta principal (trazemos conteudo para gerar resumo, mas não retornamos inteiro)
    $sql = "SELECT  p.id, 
                    p.titulo, 
                    p.autor, 
                    p.`data`, 
                    p.tipo, 
                    p.categoria, 
                    p.imagem, 
                    p.conteudo, 
                    p.slug, 
                    p.tags
                FROM posts p
                WHERE p.`data` <= NOW()
    ";
    $sql .= " ORDER BY p.`data` DESC";
    $offset = ($page - 1) * $per_page;
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $connection->prepare($sql);
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    // monta o array final com resumo gerado
    $posts = array_map(function ($row) {
        // formata data
        $dt = new DateTime($row['data']);
        $data_formatada = $dt->format('Y-m-d H:i');

        return [
            'id' => (string) $row['id'],
            'titulo' => $row['titulo'],
            'categoria' => $row['categoria'],
            'tipo' => $row['tipo'],
            'autor' => $row['autor'],
            'data' => $data_formatada,
            'resumo' => gerar_resumo($row['conteudo']),
            'imagem' => 'imagesposts'.'/'.$row['imagem'],
            'slug' => $row['slug'],
            'tags' => isset($row['tags']) ? $row['tags'] : null, // opcional
        ];
    }, $rows);

    $has_more = ($offset + count($posts)) < $total;

    $response = [
        'posts' => $posts,
        'pagination' => [
            'page' => $page,
            'per_page' => $per_page,
            'total' => $total,
            'has_more' => $has_more
        ]
    ];

    http_response_code(200);
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao consultar o banco de dados',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
