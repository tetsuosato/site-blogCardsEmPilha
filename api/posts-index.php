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

    // filtros opcionais
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    $slug = filter_input(INPUT_GET, 'slug', FILTER_UNSAFE_RAW);
    if ($slug !== null) {
        $slug = trim($slug);
        // permite só letras, números, hífen e underscore
        $slug = preg_replace('/[^a-z0-9\-_]/i', '', $slug);
        $slug = strtolower($slug);
    }

    $categoria = filter_input(INPUT_GET, 'categoria', FILTER_UNSAFE_RAW);
    if ($categoria !== null) {
        $categoria = trim($categoria);
        $allowed = ['rock','metal','pop']; // exemplo: sua lista real
        if (!in_array($categoria, $allowed, true)) {
            $categoria = null;
        }
    }

    $whereClauses = [];
    $params = [];

    if ($id !== null && $id > 0) {
        $whereClauses[] = 'p.id = :id';
        $params[':id'] = $id;
    }

    if ($slug !== null && $slug !== '') {
        $whereClauses[] = 'p.slug = :slug';
        $params[':slug'] = $slug;
    }

    if ($categoria !== null && $categoria !== '') {
        $whereClauses[] = 'p.categoria = :categoria';
        $params[':categoria'] = $categoria;
    }

    // total para paginação
    $countSql = "SELECT COUNT(*) FROM posts p";
    if (count($whereClauses) > 0) {
        $countSql .= ' WHERE ' . implode(' AND ', $whereClauses);
    }
    $countStmt = $connection->prepare($countSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    // consulta principal (trazemos conteudo para gerar resumo, mas não retornamos inteiro)
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
