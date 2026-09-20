<?php
/**
 * Consulta um vídeo do YouTube a partir do link colado na tela de cadastro.
 *
 * Devolve o título, o canal, o endereço da capa para pré-visualização e o
 * HTML que abre o conteúdo do post. Nada é gravado aqui: a capa só vira
 * arquivo quando o cadastro é salvo.
 *
 * Uso: GET youtube-dados.php?link=<url ou id do vídeo>
 */

$guardaJson = true;
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/YouTube.php';

header('Content-Type: application/json; charset=utf-8');

/** Encerra devolvendo um erro que a tela mostra ao usuário. */
function responder_erro($mensagem, $status = 400) {
    http_response_code($status);
    echo json_encode(['ok' => false, 'erro' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}

$link = isset($_GET['link']) ? $_GET['link'] : '';

if (trim($link) === '') {
    responder_erro('Informe o link do vídeo.');
}

$id = YouTube::extrairId($link);

if ($id === null) {
    responder_erro('Link inválido. Cole o endereço do vídeo no YouTube.');
}

$dados = YouTube::buscarDados($id);

if ($dados === null) {
    responder_erro('Vídeo não encontrado. Verifique se o link está correto e se o vídeo é público.');
}

// Avisa quando o vídeo já tem post, para não duplicar sem perceber.
$consulta = Database::get()->prepare("
    SELECT id, titulo
    FROM posts
    WHERE urlimagem = :url OR imagem = :arquivo
    LIMIT 1
");
$consulta->execute([
    ':url'     => YouTube::urlCurta($id),
    ':arquivo' => $id . '.webp',
]);
$jaCadastrado = $consulta->fetch();

echo json_encode([
    'ok'           => true,
    'id'           => $id,
    'titulo'       => $dados['titulo'],
    'autor'        => $dados['autor'],
    'urlCurta'     => YouTube::urlCurta($id),
    'capa'         => YouTube::urlCapa($id),
    'conteudo'     => YouTube::htmlIncorporado($id),
    'jaCadastrado' => $jaCadastrado ? [
        'id'     => (int) $jaCadastrado['id'],
        'titulo' => $jaCadastrado['titulo'],
    ] : null,
], JSON_UNESCAPED_UNICODE);
