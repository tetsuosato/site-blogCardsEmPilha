<?php
require_once __DIR__ . '/../../api/functions/functions.php';

/**
 * Conversão de imagens para WebP.
 *
 * Todo material que entra no site passa por aqui: a capa vinda do YouTube e,
 * mais adiante, as imagens que o editor de texto enviar.
 */
class Imagem {

    /** Formatos aceitos na entrada (a saída é sempre WebP). */
    const TIPOS_ACEITOS = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];

    /**
     * Grava os bytes recebidos como WebP nas dimensões exatas pedidas.
     *
     * Quando a proporção da origem não bate com a do destino, a imagem é
     * cortada pelo centro antes de redimensionar — assim a capa nunca sai
     * esticada nem com tarja preta.
     *
     * @return bool sucesso
     */
    public static function redimensionarParaWebp($bytes, $destino, $largura, $altura, $qualidade = 82) {
        $origem = @imagecreatefromstring($bytes);
        if ($origem === false) {
            return false;
        }

        $larguraOrigem = imagesx($origem);
        $alturaOrigem  = imagesy($origem);

        list($x, $y, $larguraCorte, $alturaCorte) =
            self::corteCentral($larguraOrigem, $alturaOrigem, $largura / $altura);

        $destinoImg = imagecreatetruecolor($largura, $altura);

        // Preserva transparência de PNG/GIF ao converter.
        imagealphablending($destinoImg, false);
        imagesavealpha($destinoImg, true);

        imagecopyresampled(
            $destinoImg, $origem,
            0, 0, $x, $y,
            $largura, $altura,
            $larguraCorte, $alturaCorte
        );

        if (!is_dir(dirname($destino))) {
            mkdir(dirname($destino), 0775, true);
        }

        // Grava ao lado e só depois substitui: ao atualizar uma capa que já
        // está no ar, uma gravação interrompida não deixa o site com um
        // arquivo pela metade.
        $temporario = $destino . '.tmp';
        $ok = imagewebp($destinoImg, $temporario, $qualidade) && rename($temporario, $destino);

        if (!$ok && is_file($temporario)) {
            @unlink($temporario);
        }

        imagedestroy($origem);
        imagedestroy($destinoImg);

        return $ok;
    }

    /**
     * Converte para WebP mantendo a proporção original.
     *
     * Usado nas imagens que o editor envia: aqui não há enquadramento a
     * respeitar, só o limite de largura para o arquivo não ficar pesado.
     * Imagens menores que o limite são convertidas sem ampliar.
     *
     * @return array|null ['arquivo' => nome, 'largura' => int, 'altura' => int]
     */
    public static function converterParaWebp($bytes, $destino, $larguraMaxima = 1200, $qualidade = 82) {
        $origem = @imagecreatefromstring($bytes);

        if ($origem === false) {
            return null;
        }

        $largura = imagesx($origem);
        $altura  = imagesy($origem);

        if ($largura > $larguraMaxima) {
            $novaLargura = $larguraMaxima;
            $novaAltura  = (int) round($altura * ($larguraMaxima / $largura));
        } else {
            $novaLargura = $largura;
            $novaAltura  = $altura;
        }

        $destinoImg = imagecreatetruecolor($novaLargura, $novaAltura);
        imagealphablending($destinoImg, false);
        imagesavealpha($destinoImg, true);

        imagecopyresampled($destinoImg, $origem, 0, 0, 0, 0, $novaLargura, $novaAltura, $largura, $altura);

        if (!is_dir(dirname($destino))) {
            mkdir(dirname($destino), 0775, true);
        }

        $ok = imagewebp($destinoImg, $destino, $qualidade);

        imagedestroy($origem);
        imagedestroy($destinoImg);

        return $ok ? ['largura' => $novaLargura, 'altura' => $novaAltura] : null;
    }

    /** Pasta onde ficam as imagens usadas dentro dos posts. */
    public static function pastaPosts() {
        return dirname(__DIR__, 2) . '/images/img-post';
    }

    /**
     * Monta um nome de arquivo legível e único a partir do nome enviado.
     * O sufixo aleatório evita que dois envios com o mesmo nome se sobrescrevam.
     */
    public static function nomeArquivo($nomeOriginal) {
        $base = pathinfo($nomeOriginal, PATHINFO_FILENAME);

        // Emoji e acentos fora do Latim viram espaço antes da transliteração.
        $base = preg_replace('/[^\x{0000}-\x{024F}]/u', ' ', $base);
        $base = slugify($base);

        if ($base === '') {
            $base = 'imagem';
        }

        return mb_substr($base, 0, 60) . '-' . bin2hex(random_bytes(4)) . '.webp';
    }

    /**
     * Calcula a maior área central da origem que respeita a proporção desejada.
     *
     * @return array [x, y, largura, altura]
     */
    private static function corteCentral($largura, $altura, $proporcaoAlvo) {
        $proporcaoOrigem = $largura / $altura;

        // Mesma proporção: aproveita a imagem inteira.
        if (abs($proporcaoOrigem - $proporcaoAlvo) < 0.01) {
            return [0, 0, $largura, $altura];
        }

        // Origem mais larga que o alvo: corta as laterais.
        if ($proporcaoOrigem > $proporcaoAlvo) {
            $larguraCorte = (int) round($altura * $proporcaoAlvo);
            return [(int) round(($largura - $larguraCorte) / 2), 0, $larguraCorte, $altura];
        }

        // Origem mais alta que o alvo: corta topo e base.
        $alturaCorte = (int) round($largura / $proporcaoAlvo);
        return [0, (int) round(($altura - $alturaCorte) / 2), $largura, $alturaCorte];
    }
}
