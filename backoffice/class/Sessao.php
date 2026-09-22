<?php
/**
 * Abertura da sessão do backoffice.
 *
 * Todo ponto de entrada precisa abrir a sessão com os mesmos parâmetros de
 * cookie. Quando um arquivo usava os padrões do PHP e outro estes, o navegador
 * podia acabar com a tela de login numa sessão e o captcha em outra — e a
 * primeira tentativa de login falhava mesmo com tudo digitado certo.
 */
class Sessao {

    public static function iniciar() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
              || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $https,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
    }
}
