<?php
require_once __DIR__ . '/Imagem.php';

/**
 * Lê os dados públicos de um vídeo do YouTube e prepara a capa do post.
 *
 * Não exige chave de API: o título vem do endpoint oEmbed público e a capa
 * do servidor de imagens do YouTube.
 */
class YouTube {

    /** Dimensões da capa gravada em images/img-youtube. */
    const LARGURA_CAPA = 480;
    const ALTURA_CAPA  = 270;

    /**
     * Versões de capa da melhor para a pior.
     *
     * maxresdefault e mqdefault já são 16:9; sddefault e hqdefault são 4:3 e
     * entram cortadas pelo centro. mqdefault é o último recurso porque tem
     * menos pixels que o destino e precisaria ser ampliada.
     */
    private static $versoesCapa = ['maxresdefault', 'sddefault', 'hqdefault', 'mqdefault'];

    /**
     * Extrai o identificador do vídeo.
     *
     * Aceita os formatos que o YouTube usa ao compartilhar (watch, youtu.be,
     * embed, shorts, live), com ou sem parâmetros extras como ?si= e &t=, e
     * também o identificador colado sozinho.
     *
     * @return string|null
     */
    public static function extrairId($entrada) {
        $entrada = trim((string) $entrada);

        if ($entrada === '') {
            return null;
        }

        // O identificador colado sozinho.
        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $entrada)) {
            return $entrada;
        }

        $padroes = [
            '~[?&]v=([A-Za-z0-9_-]{11})~',      // youtube.com/watch?v=ID
            '~youtu\.be/([A-Za-z0-9_-]{11})~',  // youtu.be/ID
            '~/embed/([A-Za-z0-9_-]{11})~',     // youtube.com/embed/ID
            '~/shorts/([A-Za-z0-9_-]{11})~',    // youtube.com/shorts/ID
            '~/live/([A-Za-z0-9_-]{11})~',      // youtube.com/live/ID
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $entrada, $achado)) {
                return $achado[1];
            }
        }

        return null;
    }

    /**
     * Busca título e canal do vídeo.
     *
     * Devolve null quando o vídeo não existe, é privado ou o YouTube está
     * fora do ar — casos em que o cadastro não deve seguir.
     *
     * @return array|null ['titulo' => string, 'autor' => string]
     */
    public static function buscarDados($id) {
        $url = 'https://www.youtube.com/oembed?url='
             . urlencode('https://www.youtube.com/watch?v=' . $id)
             . '&format=json';

        $resposta = self::baixar($url);

        if ($resposta === null) {
            return null;
        }

        $dados = json_decode($resposta, true);

        if (!is_array($dados) || !isset($dados['title'])) {
            return null;
        }

        return [
            'titulo' => $dados['title'],
            'autor'  => isset($dados['author_name']) ? $dados['author_name'] : '',
        ];
    }

    /**
     * Endereço da capa para exibir na tela antes de salvar.
     * Aponta direto para o YouTube: nada é gravado aqui.
     */
    public static function urlCapa($id, $versao = 'maxresdefault') {
        return "https://img.youtube.com/vi/{$id}/{$versao}.jpg";
    }

    /** Link curto gravado na coluna urlimagem, no mesmo formato dos posts atuais. */
    public static function urlCurta($id) {
        return 'https://youtu.be/' . $id;
    }

    /** Pasta onde as capas são gravadas. */
    public static function pastaCapas() {
        return dirname(__DIR__, 2) . '/images/img-youtube';
    }

    /**
     * Baixa a capa do vídeo e grava como WebP 480x270.
     *
     * O nome do arquivo é o identificador do vídeo, seguindo a convenção dos
     * posts já cadastrados (ex.: fYp_OUysyhY.webp).
     *
     * @return string|null nome do arquivo gravado
     */
    public static function salvarCapa($id, $pasta = null) {
        $bytes = self::baixarCapa($id);

        if ($bytes === null) {
            return null;
        }

        $pasta   = $pasta === null ? self::pastaCapas() : rtrim($pasta, "/\\");
        $arquivo = $id . '.webp';

        $ok = Imagem::redimensionarParaWebp(
            $bytes,
            $pasta . '/' . $arquivo,
            self::LARGURA_CAPA,
            self::ALTURA_CAPA
        );

        return $ok ? $arquivo : null;
    }

    /**
     * Busca a melhor capa disponível.
     *
     * Vídeos antigos ou pouco acessados nem sempre têm maxresdefault; nesses
     * casos o YouTube responde com uma imagem substituta de 120x90, que é
     * descartada aqui para não virar uma capa borrada.
     */
    private static function baixarCapa($id) {
        foreach (self::$versoesCapa as $versao) {
            $bytes = self::baixar(self::urlCapa($id, $versao));

            if ($bytes === null) {
                continue;
            }

            $medidas = @getimagesizefromstring($bytes);

            if ($medidas === false || $medidas[0] < 200) {
                continue;
            }

            return $bytes;
        }

        return null;
    }

    /**
     * Monta o HTML do vídeo para abrir o conteúdo do post.
     *
     * O bloco externo reserva 56,25% de altura (a proporção 16:9) para o
     * vídeo acompanhar a largura da tela sem cortar nem sobrar espaço.
     *
     * Vai acompanhado de parágrafos vazios antes e depois: no editor eles
     * viram linhas onde dá para clicar e escrever em volta do vídeo, que
     * sozinho ocuparia todo o espaço e não deixaria onde digitar.
     */
    public static function htmlIncorporado($id) {
        $linhaVazia = '<p>&nbsp;</p>' . "\n";

        return $linhaVazia . $linhaVazia
             . '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;max-width:100%;margin:0 auto;">' . "\n"
             . '  <iframe src="https://www.youtube.com/embed/' . $id . '"' . "\n"
             . '    title="YouTube video player"' . "\n"
             . '    style="position:absolute;top:0;left:0;width:100%;height:100%;"' . "\n"
             . '    frameborder="0"' . "\n"
             . '    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"' . "\n"
             . '    referrerpolicy="strict-origin-when-cross-origin"' . "\n"
             . '    allowfullscreen></iframe>' . "\n"
             . '</div>' . "\n"
             . $linhaVazia . $linhaVazia;
    }

    /**
     * Busca uma URL usando cURL e, se não houver, o wrapper de arquivos.
     *
     * Os dois caminhos existem porque servidores de hospedagem costumam
     * desligar um ou outro.
     *
     * @return string|null corpo da resposta
     */
    private static function baixar($url, $segundos = 10) {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => $segundos,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; BackofficeVlogRock/1.0)',
            ]);

            $corpo  = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($corpo !== false && $status === 200) {
                return $corpo;
            }
        }

        if (ini_get('allow_url_fopen')) {
            $contexto = stream_context_create([
                'http' => [
                    'timeout'    => $segundos,
                    'user_agent' => 'Mozilla/5.0 (compatible; BackofficeVlogRock/1.0)',
                ],
            ]);

            $corpo = @file_get_contents($url, false, $contexto);

            if ($corpo !== false) {
                return $corpo;
            }
        }

        return null;
    }
}
