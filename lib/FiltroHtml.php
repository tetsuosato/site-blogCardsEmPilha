<?php
/**
 * Filtro do HTML do conteúdo das postagens.
 *
 * O conteúdo vem do editor do backoffice e é exibido no site sem escape —
 * precisa ser HTML. Este filtro garante que só passe HTML de texto: o que
 * pode executar código (<script>, onclick=, links javascript:, iframes de
 * outros sites...) é removido.
 *
 * Funciona por lista do que é permitido, não do que é proibido: qualquer
 * coisa fora da lista sai, inclusive truques que ainda não conhecemos.
 *
 *  - Tag permitida: fica, só com os atributos permitidos.
 *  - Tag perigosa (script, style, form...): sai junto com o conteúdo.
 *  - Outra tag desconhecida: sai só a tag, o texto de dentro fica.
 *
 * Usa apenas o DOMDocument, que já vem no PHP — nada a instalar no servidor.
 */
class FiltroHtml {

    /** Tag => atributos próprios dela (além dos globais). */
    private static $tags = [
        'p' => [], 'br' => [], 'hr' => [], 'div' => [], 'span' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'sub' => [], 'sup' => [], 'small' => [], 'mark' => [],
        'blockquote' => ['cite'], 'pre' => [], 'code' => [],
        'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
        'ul' => [], 'ol' => ['start', 'type'], 'li' => [],
        'dl' => [], 'dt' => [], 'dd' => [],
        'figure' => [], 'figcaption' => [],
        'table' => ['border', 'cellpadding', 'cellspacing', 'width'],
        'caption' => [], 'thead' => [], 'tbody' => [], 'tfoot' => [], 'tr' => [],
        'th' => ['colspan', 'rowspan', 'scope', 'width'],
        'td' => ['colspan', 'rowspan', 'width'],
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'loading'],
        'iframe' => ['src', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen', 'referrerpolicy'],
    ];

    /** Atributos aceitos em qualquer tag permitida. */
    private static $globais = ['style', 'class', 'title'];

    /** Saem com tudo o que têm dentro: o conteúdo delas não é texto. */
    private static $perigosas = [
        'script', 'style', 'template', 'noscript', 'object', 'embed', 'applet',
        'form', 'input', 'button', 'select', 'option', 'textarea',
        'link', 'meta', 'base', 'frame', 'frameset', 'svg', 'math', 'head', 'title',
    ];

    /** Único destino aceito em iframe: o player do YouTube. */
    const IFRAME_PERMITIDO = '~^https://(www\.)?youtube(-nocookie)?\.com/embed/[A-Za-z0-9_-]{11}([?&#].*)?$~i';

    public static function limpar($html) {
        $html = (string) $html;

        if (trim($html) === '') {
            return $html;
        }

        $doc = new DOMDocument();
        $erros = libxml_use_internal_errors(true);

        // A declaração de codificação faz o parser ler UTF-8 (sem ela, lê
        // ISO-8859-1 e estraga os acentos). O <div> dá uma raiz única.
        $doc->loadHTML(
            '<?xml encoding="UTF-8"?><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($erros);

        $raiz = null;
        foreach ($doc->childNodes as $no) {
            if ($no->nodeType === XML_ELEMENT_NODE) {
                $raiz = $no;
                break;
            }
        }

        if ($raiz === null) {
            return '';
        }

        self::filtrarFilhos($raiz);

        $saida = '';
        foreach ($raiz->childNodes as $no) {
            $saida .= $doc->saveHTML($no);
        }

        return $saida;
    }

    private static function filtrarFilhos(DOMNode $pai) {
        // A lista é copiada antes: remover nós durante a varredura pularia irmãos.
        foreach (iterator_to_array($pai->childNodes) as $no) {

            if ($no->nodeType === XML_TEXT_NODE || $no->nodeType === XML_CDATA_SECTION_NODE) {
                continue;
            }

            // Comentários e instruções de processamento não aparecem para o
            // leitor e já serviram para esconder código (comentários condicionais).
            if ($no->nodeType !== XML_ELEMENT_NODE) {
                $pai->removeChild($no);
                continue;
            }

            $tag = strtolower($no->nodeName);

            if (in_array($tag, self::$perigosas, true)) {
                $pai->removeChild($no);
                continue;
            }

            if (!isset(self::$tags[$tag])) {
                // Tag desconhecida: aproveita o que tem dentro e descarta a tag.
                self::filtrarFilhos($no);
                while ($no->firstChild) {
                    $pai->insertBefore($no->firstChild, $no);
                }
                $pai->removeChild($no);
                continue;
            }

            if ($tag === 'iframe' && !preg_match(self::IFRAME_PERMITIDO, self::url($no->getAttribute('src')))) {
                $pai->removeChild($no);
                continue;
            }

            self::filtrarAtributos($no, $tag);
            self::filtrarFilhos($no);
        }
    }

    private static function filtrarAtributos(DOMElement $el, $tag) {
        $permitidos = array_merge(self::$globais, self::$tags[$tag]);

        foreach (iterator_to_array($el->attributes) as $atributo) {
            $nome  = strtolower($atributo->name);
            $valor = $atributo->value;

            $manter = in_array($nome, $permitidos, true);

            if ($manter && ($nome === 'href' || $nome === 'src' || $nome === 'cite')) {
                $manter = self::urlSegura($valor, $nome === 'href');
            }

            if ($manter && $nome === 'style') {
                $manter = self::estiloSeguro($valor);
            }

            if (!$manter) {
                $el->removeAttribute($atributo->name);
            }
        }

        // Link que abre outra aba não pode ter acesso à aba do site.
        if ($tag === 'a' && strtolower($el->getAttribute('target')) === '_blank') {
            $rel = preg_split('/\s+/', strtolower($el->getAttribute('rel')), -1, PREG_SPLIT_NO_EMPTY);
            if (!in_array('noopener', $rel, true)) {
                $rel[] = 'noopener';
                $el->setAttribute('rel', implode(' ', $rel));
            }
        }
    }

    /**
     * Endereço sem espaços e caracteres de controle no meio — o navegador os
     * ignora, então "java\tscript:" funcionaria como "javascript:".
     */
    private static function url($valor) {
        return preg_replace('/[\x00-\x20\x7F]+/', '', html_entity_decode((string) $valor, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /** Aceita http, https, endereço relativo e âncora; mailto só em links. */
    private static function urlSegura($valor, $ehLink) {
        $url = self::url($valor);

        if ($url === '') {
            return true;
        }

        // Sem esquema (relativo, âncora, //host): o "esquema" seria o texto
        // antes de um ":" que apareça antes de qualquer / ? #.
        if (!preg_match('~^([a-z][a-z0-9+.\-]*):~i', $url, $achado)) {
            return true;
        }

        $esquema = strtolower($achado[1]);
        $aceitos = $ehLink ? ['http', 'https', 'mailto'] : ['http', 'https'];

        return in_array($esquema, $aceitos, true);
    }

    /** Barra os poucos recursos de CSS que buscam ou executam algo. */
    private static function estiloSeguro($valor) {
        $css = strtolower(preg_replace('/[\x00-\x20\x7F\\\\]+/', '', html_entity_decode((string) $valor, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        foreach (['expression(', 'url(', 'javascript:', 'vbscript:', 'behavior:', '-moz-binding', '@import'] as $proibido) {
            if (strpos($css, $proibido) !== false) {
                return false;
            }
        }

        return true;
    }
}
