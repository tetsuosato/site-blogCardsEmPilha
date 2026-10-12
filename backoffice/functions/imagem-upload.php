<?php
/**
 * Recebe as imagens enviadas pelo editor de texto e grava em images/img-post.
 *
 * Qualquer formato aceito entra convertido para WebP, que é o que o site usa.
 * A resposta segue o formato que o TinyMCE espera: {"location": "<url>"}.
 */

$guardaJson = true;
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Imagem.php';

header('Content-Type: application/json; charset=utf-8');

function falhar($mensagem, $status = 400) {
    http_response_code($status);
    echo json_encode(['error' => $mensagem], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    falhar('Requisição inválida.', 405);
}

// O editor envia o token no cabeçalho: o corpo é multipart e já leva o arquivo.
if (!csrf_valid($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')) {
    falhar('Sessão inválida. Recarregue a página e tente novamente.', 403);
}

if (!isset($_FILES['file'])) {
    falhar('Nenhum arquivo recebido.');
}

$arquivo = $_FILES['file'];
$envio   = Imagem::lerEnvio($arquivo);

if ($envio['erro'] !== null) {
    falhar($envio['erro']);
}

$bytes = $envio['bytes'];
$nome  = Imagem::nomeArquivo($arquivo['name']);
$destino = Imagem::pastaPosts() . '/' . $nome;

$resultado = Imagem::converterParaWebp($bytes, $destino);

if ($resultado === null) {
    falhar('Não foi possível converter a imagem.');
}

// Guarda o que foi enviado nesta sessão de edição. Ao salvar a postagem, o que
// não tiver sido usado é apagado — é comum trocar de imagem e a antiga ficar
// esquecida na pasta.
if (!isset($_SESSION['imagens_enviadas'])) {
    $_SESSION['imagens_enviadas'] = [];
}
$_SESSION['imagens_enviadas'][] = $nome;

echo json_encode([
    'location' => BASE_URL . '/images/img-post/' . $nome,
    'largura'  => $resultado['largura'],
    'altura'   => $resultado['altura'],
], JSON_UNESCAPED_UNICODE);
