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
        ]
    );

    $sql = "SELECT  d.`destaques`,
                    d.ordem,
                    p.* 
                FROM destaques d
                    LEFT JOIN posts p ON d.idpost = p.id
                    ORDER BY destaques ASC, ordem ASC
    ";
    $stmt = $connection->prepare($sql);
    $stmt->execute();

    $destaques = $stmt->fetchAll();

    if (!$destaques) {
        // Post não encontrado
        http_response_code(404);
        echo json_encode(['error' => 'Destaques não encontrados']);
        exit;
    }

   //  echo '<pre>'; print_r($destaques); die();



    foreach ($destaques as $row) {

        if (isset($row['tags']) && is_string($row['tags'])) {
            $decoded = json_decode($row['tags'], true);
            $row['tags'] = is_array($decoded) ? $decoded : [];
        } else {
            $row['tags'] = [];
        }

        $caminhoImagem = 'imagesposts/' . $row['imagem'];

        $dataOriginal = $row['data'];
        $dataFormatada = date('d/m/Y H:i', strtotime($dataOriginal));
        
        if($row['destaques'] == '1') {
            $post['destaque1'][] = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'autor' => $row['autor'],
                'data' => $dataFormatada,
                'tipo' => $row['tipo'],
                'categoria' => $row['categoria'],
                'categoriaSlug' => slugify($row['categoria']),
                'slug' => $row['slug'],
                'imagem' => $caminhoImagem,
                'urlimagem' => isset($row['urlimagem']) ? $row['urlimagem'] : null,
                'resumo' => gerar_resumo($row['conteudo']),
                'tags' => $row['tags'],
            ];
        }

        if($row['destaques'] == '2') {
            $post['destaque2'][] = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'autor' => $row['autor'],
                'data' => $dataFormatada,
                'tipo' => $row['tipo'],
                'categoria' => $row['categoria'],
                'categoriaSlug' => slugify($row['categoria']),
                'slug' => $row['slug'],
                'imagem' => $caminhoImagem,
                'urlimagem' => isset($row['urlimagem']) ? $row['urlimagem'] : null,
                'resumo' => gerar_resumo($row['conteudo']),
                'tags' => $row['tags'],
            ];
        }

    } // foreach

    http_response_code(200);
    echo json_encode($post, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro ao consultar o banco de dados',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
