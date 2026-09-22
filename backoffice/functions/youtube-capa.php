<?php
/**
 * Baixa de novo a capa de um vídeo já publicado e grava por cima da atual.
 *
 * Usado quando a miniatura é trocada no YouTube depois da postagem. O arquivo
 * mantém o mesmo nome; como a versão das URLs vem da data de modificação,
 * todas as páginas passam a exibir a capa nova sem mais nenhuma alteração.
 *
 * Uso: POST youtube-capa.php  (id=<id da postagem>, cabeçalho X-CSRF-Token)
 */

$guardaJson = true;
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Postagem.php';

header('Content-Type: application/json; charset=utf-8');

function responder_erro($mensagem, $status = 400) {
    http_response_code($status);
    echo json_encode(['ok' => false, 'erro' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder_erro('Requisição inválida.', 405);
}

if (!csrf_valid($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
    responder_erro('Sessão inválida. Recarregue a página e tente novamente.', 403);
}

$post = Postagem::buscar((int) ($_POST['id'] ?? 0));

if (!$post) {
    responder_erro('Postagem não encontrada.', 404);
}

$videoId = YouTube::extrairId($post['urlimagem']);

if ($videoId === null) {
    responder_erro('Esta postagem não tem um vídeo do YouTube associado.');
}

$arquivo = YouTube::salvarCapa($videoId);

if ($arquivo === null) {
    responder_erro('Não foi possível baixar a capa do YouTube. Tente novamente em instantes.', 502);
}

// Postagens antigas podem ter a capa gravada com outro nome; a nova passa a
// seguir o padrão do identificador do vídeo.
if ($arquivo !== $post['imagem']) {
    Database::get()->prepare("UPDATE posts SET imagem = :imagem WHERE id = :id")
                   ->execute([':imagem' => $arquivo, ':id' => $post['id']]);
}

// A leitura da data de modificação pode vir do cache interno do PHP, que
// ainda guarda a do arquivo antes de ser sobrescrito.
clearstatcache();

echo json_encode([
    'ok'   => true,
    'capa' => BASE_URL . '/' . caminhoCapa($arquivo),
], JSON_UNESCAPED_UNICODE);
