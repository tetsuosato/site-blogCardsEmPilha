<?php
/**
 * Captcha da tela de login.
 *
 * A imagem é gerada na mesma requisição que monta a tela e vai embutida na
 * página. Antes ela vinha de um endereço próprio, que abria a sessão por
 * conta própria: se essa requisição e a da tela caíssem em sessões diferentes,
 * o código mostrado não era o que o servidor esperava.
 */
class Captcha {

    /** Sem I, O, 0 e 1, que se confundem entre si na fonte da imagem. */
    const SIMBOLOS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    const TAMANHO = 5;

    /**
     * Sorteia um código novo, guarda na sessão e devolve a imagem pronta para
     * usar no atributo src.
     *
     * @return string data URI de um PNG
     */
    public static function gerar() {
        $texto = '';
        $ultimo = strlen(self::SIMBOLOS) - 1;

        for ($i = 0; $i < self::TAMANHO; $i++) {
            $texto .= self::SIMBOLOS[random_int(0, $ultimo)];
        }

        $_SESSION['captcha'] = $texto;

        $imagem = imagecreate(150, 40);
        imagecolorallocate($imagem, 230, 230, 230);   // fundo
        $cor = imagecolorallocate($imagem, 255, 0, 0);

        $x = (imagesx($imagem) - imagefontwidth(5) * strlen($texto)) / 2;
        $y = (imagesy($imagem) - imagefontheight(5)) / 2;
        imagestring($imagem, 5, (int) $x, (int) $y, $texto, $cor);

        ob_start();
        imagepng($imagem);
        $png = ob_get_clean();
        imagedestroy($imagem);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Confere o código digitado.
     *
     * O código vale para uma única tentativa: acertando ou errando, ele sai da
     * sessão. Sem isso seria possível testar várias senhas seguidas com o
     * mesmo captcha, o que anula a proteção.
     */
    public static function validar($digitado) {
        $esperado = isset($_SESSION['captcha']) ? $_SESSION['captcha'] : null;
        unset($_SESSION['captcha']);

        if (!is_string($esperado) || $esperado === '' || !is_string($digitado)) {
            return false;
        }

        // Maiúsculas e minúsculas valem igual: a imagem só mostra maiúsculas.
        return hash_equals($esperado, strtoupper(trim($digitado)));
    }
}
