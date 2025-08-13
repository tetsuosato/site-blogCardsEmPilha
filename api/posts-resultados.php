<?php
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

    // paginação
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
        'options' => ['default' => 1, 'min_range' => 1]
    ]);
    $per_page = filter_input(INPUT_GET, 'per_page', FILTER_VALIDATE_INT, [
        'options' => ['default' => 10, 'min_range' => 1, 'max_range' => 50]
    ]);

    // Pesquisa
    $pesquisa = filter_input(INPUT_GET, 'pesquisa', FILTER_UNSAFE_RAW);
    if ($pesquisa !== null) {
        $pesquisa = trim($pesquisa);
        $pesquisa = strip_tags($pesquisa); // Remove HTML
        $pesquisa = mb_substr($pesquisa, 0, 100); // Limita tamanho (ex: 100 caracteres)
    }
    
    $whereClauses = [];
    $params = [];

    // pesquisa livre
    if ($pesquisa !== null && $pesquisa !== '') {
        $whereClauses[] = "(
            p.titulo LIKE :pesquisa1
            OR p.slug LIKE :pesquisa2
            OR p.autor LIKE :pesquisa3
            OR p.tipo LIKE :pesquisa4
            OR p.categoria LIKE :pesquisa5
            OR p.tags LIKE :pesquisa6
        )";
        for ($i = 1; $i <= 6; $i++) {
            $params[":pesquisa{$i}"] = '%' . $pesquisa . '%';
        }
    }

    // total para paginação
    $countSql = "SELECT COUNT(*) FROM posts p";
    if (count($whereClauses) > 0) {
        $countSql .= ' WHERE ' . implode(' AND ', $whereClauses);
    }
    //echo '<pre>'.$countSql; die();
    $countStmt = $connection->prepare($countSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    // consulta principal
    $sql = "SELECT p.id, p.titulo, p.autor, p.`data`, p.tipo, p.categoria, p.imagem, p.conteudo, p.slug, p.tags
            FROM posts p";
    if (count($whereClauses) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $whereClauses);
    }
    $sql .= " ORDER BY p.`data` DESC";
    $offset = ($page - 1) * $per_page;
    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $connection->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $posts = array_map(function ($row) {
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
            'imagem' => 'imagesposts' . '/' . $row['imagem'],
            'slug' => $row['slug'],
            'tags' => isset($row['tags']) ? $row['tags'] : null,
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
