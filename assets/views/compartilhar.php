<?php
/**
 * Barra "Compartilhar" da página de postagem.
 *
 * Espera $urlPost e $ogTitulo, definidos no postagem.php.
 *
 * São links comuns para a página de compartilhamento de cada rede — sem
 * scripts nem botões oficiais delas, que carregam rastreadores e passariam a
 * depender do aviso de cookies. Funcionam mesmo sem JavaScript; o
 * compartilhar.js só acrescenta o "Copiar link" e, no celular, o menu de
 * compartilhamento do próprio aparelho (único caminho para o Instagram, que
 * não tem link de compartilhamento pela web).
 */
if (empty($urlPost)) {
    return;
}

$tituloCompartilhar = isset($ogTitulo) ? (string) $ogTitulo : '';
$urlCodificada      = rawurlencode($urlPost);
$tituloCodificado   = rawurlencode($tituloCompartilhar);

$redes = [
    [
        'nome'   => 'WhatsApp',
        'classe' => 'whatsapp',
        'icone'  => 'bi-whatsapp',
        'link'   => 'https://api.whatsapp.com/send?text=' . rawurlencode($tituloCompartilhar . ' ' . $urlPost),
    ],
    [
        'nome'   => 'Facebook',
        'classe' => 'facebook',
        'icone'  => 'bi-facebook',
        'link'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $urlCodificada,
    ],
    [
        'nome'   => 'X (Twitter)',
        'classe' => 'x',
        'icone'  => 'bi-twitter-x',
        'link'   => 'https://twitter.com/intent/tweet?text=' . $tituloCodificado . '&url=' . $urlCodificada,
    ],
    [
        'nome'   => 'Telegram',
        'classe' => 'telegram',
        'icone'  => 'bi-telegram',
        'link'   => 'https://t.me/share/url?url=' . $urlCodificada . '&text=' . $tituloCodificado,
    ],
];
?>
<section class="post-compartilhar mb-4"
         data-url="<?= htmlspecialchars($urlPost, ENT_QUOTES, 'UTF-8') ?>"
         data-titulo="<?= htmlspecialchars($tituloCompartilhar, ENT_QUOTES, 'UTF-8') ?>">
    <span class="post-compartilhar-label"><i class="bi bi-share-fill"></i> Compartilhar</span>

    <div class="post-compartilhar-botoes">
        <?php foreach ($redes as $rede): ?>
            <a href="<?= htmlspecialchars($rede['link'], ENT_QUOTES, 'UTF-8') ?>"
               class="post-compartilhar-btn post-compartilhar-<?= $rede['classe'] ?>"
               target="_blank" rel="noopener noreferrer"
               title="Compartilhar no <?= $rede['nome'] ?>" aria-label="Compartilhar no <?= $rede['nome'] ?>">
                <i class="bi <?= $rede['icone'] ?>"></i>
            </a>
        <?php endforeach; ?>

        <button type="button" class="post-compartilhar-btn post-compartilhar-copiar" data-compartilhar="copiar"
                title="Copiar link" aria-label="Copiar link da postagem">
            <i class="bi bi-link-45deg"></i>
        </button>

        <!-- Menu de compartilhamento do aparelho: aparece só no celular (compartilhar.js) -->
        <button type="button" class="post-compartilhar-btn post-compartilhar-nativo" data-compartilhar="nativo"
                title="Mais opções" aria-label="Mais opções de compartilhamento" hidden>
            <i class="bi bi-three-dots"></i>
        </button>

        <span class="post-compartilhar-aviso" role="status" aria-live="polite"></span>
    </div>
</section>
