<?php
header('Content-Type: application/json; charset=utf-8');
include('../lib/config.php');

try {
    $connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );

    $stmt = $connection->query(
        "SELECT categoria, COUNT(*) as total FROM posts GROUP BY categoria ORDER BY total DESC"
    );

    echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
