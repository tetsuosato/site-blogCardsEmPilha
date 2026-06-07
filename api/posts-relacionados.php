<?php
header('Content-Type: application/json; charset=utf-8');
include('../lib/config.php');
include('functions/functions.php');

mb_internal_encoding('UTF-8');

$id        = isset($_GET['id'])        ? (int) $_GET['id']        : 0;
$titulo    = isset($_GET['titulo'])    ? trim($_GET['titulo'])     : '';
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria'])  : '';

try {
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $encontrados = [];

    // Passo 1: busca por palavras do título (> 3 chars)
    $palavras = array_filter(explode(' ', $titulo), function ($p) {
        return mb_strlen($p) > 3;
    });

    if (!empty($palavras)) {
        $likes  = implode(' OR ', array_fill(0, count($palavras), 'titulo LIKE ?'));
        $params = array_map(function ($p) { return '%' . $p . '%'; }, $palavras);
        $params[] = $id;

        $stmt = $connection->prepare(
            "SELECT p.id, p.titulo, p.slug, p.conteudo, p.imagem, p.data,
                    u.nickname AS autor,
                    t.Nome AS tipo,
                    c.Nome AS categoria
             FROM posts p
             LEFT JOIN users u ON u.id = p.autor
             LEFT JOIN tipo t ON t.id = p.tipo
             LEFT JOIN categoria c ON c.id = p.categoria
             WHERE ($likes) AND p.id != ?
             ORDER BY p.data DESC
             LIMIT 3"
        );
        $stmt->execute(array_values($params));
        $encontrados = $stmt->fetchAll();
    }

    // Passo 2: completa com mesma categoria se necessário
    $faltam = 3 - count($encontrados);
    if ($faltam > 0 && $categoria !== '') {
        $idsExcluir   = array_merge([$id], array_column($encontrados, 'id'));
        $placeholders = implode(',', array_fill(0, count($idsExcluir), '?'));
        $params2      = array_merge([$categoria], $idsExcluir);

        $stmt2 = $connection->prepare(
            "SELECT p.id, p.titulo, p.slug, p.conteudo, p.imagem, p.data,
                    u.nickname AS autor,
                    t.Nome AS tipo,
                    c.Nome AS categoria
             FROM posts p
             LEFT JOIN users u ON u.id = p.autor
             LEFT JOIN tipo t ON t.id = p.tipo
             LEFT JOIN categoria c ON c.id = p.categoria
             WHERE c.Nome = ? AND p.id NOT IN ($placeholders)
             ORDER BY p.data DESC
             LIMIT $faltam"
        );
        $stmt2->execute($params2);
        $encontrados = array_merge($encontrados, $stmt2->fetchAll());
    }

    // Monta a resposta no mesmo formato das outras APIs
    $resultado = array_map(function ($row) {
        return [
            'id'            => (string) $row['id'],
            'titulo'        => $row['titulo'],
            'slug'          => $row['slug'],
            'resumo'        => gerar_resumo($row['conteudo']),
            'imagem'        => 'imagesposts/' . $row['imagem'],
            'autor'         => $row['autor'],
            'categoria'     => $row['categoria'],
            'categoriaSlug' => slugify($row['categoria']),
            'tipo'          => $row['tipo'],
            'data'          => date('d/m/Y H:i', strtotime($row['data'])),
        ];
    }, array_slice($encontrados, 0, 3));

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
