<?php
/**
 * Capa do post em JPG, para a prévia do link nas redes sociais.
 *
 * O site grava as capas em WebP, formato que nem todas as redes exibem na
 * prévia (WhatsApp e LinkedIn falham com frequência). Esta é a mesma capa
 * convertida para JPG — usada só no og:image; as páginas seguem com o WebP.
 *
 * Uso: capa-previa?img=img-youtube/<arquivo>.webp
 *
 * A conversão acontece na primeira vez que a capa é pedida e fica guardada em
 * images/img-previa/. A pasta é um cache descartável: apagá-la a qualquer
 * momento é seguro, os arquivos são refeitos sob demanda. Se a capa original
 * for atualizada (mais nova que o JPG), o JPG é refeito na próxima chamada.
 *
 * Só aceita um .webp que já exista em img-youtube ou img-post — nada de
 * caminhos, outros formatos ou endereços externos. Em qualquer falha devolve
 * a logo do site, para a prévia não ficar sem imagem.
 */

$raizImagens = dirname(__DIR__) . '/images';

/** Entrega a logo do site no lugar da capa. */
function entregar_logo() {
    $logo = dirname(__DIR__) . '/assets/image/logo_menu.png';
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=3600');
    header('Content-Length: ' . filesize($logo));
    readfile($logo);
    exit;
}

$pedido = $_GET['img'] ?? '';

if (!is_string($pedido)
    || !preg_match('~^(img-youtube|img-post)/([A-Za-z0-9_-][A-Za-z0-9._-]{0,150})\.webp$~', $pedido, $partes)) {
    entregar_logo();
}

[, $pasta, $nome] = $partes;

$origem  = "$raizImagens/$pasta/$nome.webp";
$destino = "$raizImagens/img-previa/{$pasta}__{$nome}.jpg";

if (!is_file($origem)) {
    entregar_logo();
}

if (!is_file($destino) || filemtime($destino) < filemtime($origem)) {
    $imagem = @imagecreatefromwebp($origem);

    if ($imagem === false) {
        error_log("capa-previa: não foi possível ler $pasta/$nome.webp");
        entregar_logo();
    }

    $largura = imagesx($imagem);
    $altura  = imagesy($imagem);

    // JPG não tem transparência: o que for transparente vira fundo branco.
    $jpg = imagecreatetruecolor($largura, $altura);
    imagefill($jpg, 0, 0, imagecolorallocate($jpg, 255, 255, 255));
    imagecopy($jpg, $imagem, 0, 0, 0, 0, $largura, $altura);

    if (!is_dir(dirname($destino))) {
        @mkdir(dirname($destino), 0775, true);
    }

    // Grava ao lado e só depois substitui: duas visitas ao mesmo tempo nunca
    // leem um arquivo pela metade.
    $temporario = $destino . '.' . bin2hex(random_bytes(4)) . '.tmp';
    $ok = imagejpeg($jpg, $temporario, 85) && rename($temporario, $destino);

    imagedestroy($imagem);
    imagedestroy($jpg);

    if (!$ok) {
        @unlink($temporario);
        error_log("capa-previa: não foi possível gravar $destino");
        entregar_logo();
    }

    clearstatcache(true, $destino);
}

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($destino)) . ' GMT');
header('Content-Length: ' . filesize($destino));
readfile($destino);
