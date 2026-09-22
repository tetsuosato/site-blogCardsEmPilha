<?php
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Postagem.php';
require_once __DIR__ . '/../class/YouTube.php';

$paginaAtual  = 'video';
$tituloPagina = 'Postar Vídeo';

$urlPagina = BASE_URL . '/backoffice/main/postagem-video';
$erros     = [];

$formulario = [
    'id'        => '',
    'link'      => '',
    'video'     => '',
    'titulo'    => '',
    'tipo'      => '',
    'categoria' => '',
    'tags'      => '',
    'data'      => date('Y-m-d\TH:i'),
    'conteudo'  => '',
    'novolink'  => '',
];

$tipos      = Postagem::tiposLocais();
$categorias = Postagem::categorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $erros[] = 'Sessão inválida. Recarregue a página e tente novamente.';
    } else {
        // Título e conteúdo chegam codificados para não serem barrados pelo
        // firewall da hospedagem; os demais campos vêm como sempre.
        foreach (array_keys($formulario) as $campo) {
            $formulario[$campo] = trim(post_protegido($campo));
        }

        $id = $formulario['id'] !== '' ? (int) $formulario['id'] : null;

        // Na edição o vídeo vem do registro, não do formulário: trocar o vídeo
        // significaria outra postagem.
        if ($id !== null) {
            $original = Postagem::buscar($id);

            if (!$original) {
                $erros[] = 'Postagem não encontrada.';
                $id = null;
            }
        }

        $videoId = isset($original) && $original
            ? YouTube::extrairId($original['urlimagem'])
            : YouTube::extrairId($formulario['video'] !== '' ? $formulario['video'] : $formulario['link']);

        // O vídeo só é exigido ao criar. Na edição ele não muda, e há postagens
        // sem vídeo algum (as de demonstração, por exemplo) que precisam
        // continuar editáveis.
        if ($id === null && $videoId === null) {
            $erros[] = 'Carregue um vídeo do YouTube antes de salvar.';
        }

        if ($formulario['titulo'] === '') {
            $erros[] = 'Informe o título da postagem.';
        }

        if ($formulario['tipo'] === '' || !in_array((int) $formulario['tipo'], array_column($tipos, 'id'), false)) {
            $erros[] = 'Escolha o tipo da postagem.';
        }

        if ($formulario['categoria'] === '' || !in_array((int) $formulario['categoria'], array_column($categorias, 'id'), false)) {
            $erros[] = 'Escolha a categoria.';
        }

        // O campo datetime-local chega como 2026-09-19T14:30.
        $quando = DateTime::createFromFormat('Y-m-d\TH:i', $formulario['data']);

        if (!$quando) {
            $erros[] = 'Informe uma data de publicação válida.';
        }

        if ($formulario['conteudo'] === '') {
            $erros[] = 'O conteúdo não pode ficar vazio.';
        }

        // Só na criação: a mesma checagem na edição acusaria a própria postagem.
        if ($id === null && $videoId !== null && Postagem::porVideo($videoId)) {
            $erros[] = 'Este vídeo já possui uma postagem cadastrada.';
        }

        if (!$erros) {
            $tags = Postagem::normalizarTags($formulario['tags']);

            // Candidatas a remoção: o que foi enviado agora e, na edição, o que
            // o post já exibia. Sem a segunda parte, uma imagem antiga retirada
            // do conteúdo ficaria para sempre ocupando espaço no servidor.
            $candidatasImagens = $_SESSION['imagens_enviadas'] ?? [];

            if ($id !== null) {
                $candidatasImagens = array_merge(
                    $candidatasImagens,
                    Postagem::imagensDoConteudo($original['conteudo'])
                );
            }

            if ($id === null) {
                $arquivoCapa = YouTube::salvarCapa($videoId);

                if ($arquivoCapa === null) {
                    $erros[] = 'Não foi possível baixar a capa do vídeo. Tente novamente.';
                }
            }

            if (!$erros && $id === null) {
                Postagem::criar([
                    'titulo'    => $formulario['titulo'],
                    'slug'      => Postagem::slugUnico(Postagem::gerarSlug($formulario['titulo'], $videoId)),
                    'autor'     => (int) $_SESSION['id_user'],
                    'data'      => $quando->format('Y-m-d H:i:s'),
                    'tipo'      => (int) $formulario['tipo'],
                    'categoria' => (int) $formulario['categoria'],
                    'imagem'    => $arquivoCapa,
                    'urlimagem' => YouTube::urlCurta($videoId),
                    'conteudo'  => $formulario['conteudo'],
                    'tags'      => $tags,
                ]);

                $acao = $quando->getTimestamp() > time()
                    ? 'Postagem agendada para ' . $quando->format('d/m/Y H:i') . '.'
                    : 'Postagem publicada com sucesso.';

            } elseif (!$erros) {
                // O link só é refeito quando pedido, para não invalidar
                // endereços que já circulam.
                $novoSlug = $formulario['novolink'] !== ''
                    ? Postagem::slugUnico(Postagem::gerarSlug($formulario['titulo'], $videoId), $id)
                    : null;

                Postagem::atualizar($id, [
                    'titulo'    => $formulario['titulo'],
                    'slug'      => $novoSlug,
                    'data'      => $quando->format('Y-m-d H:i:s'),
                    'tipo'      => (int) $formulario['tipo'],
                    'categoria' => (int) $formulario['categoria'],
                    'conteudo'  => $formulario['conteudo'],
                    'tags'      => $tags,
                ]);

                $acao = 'Postagem atualizada.';

                if ($novoSlug !== null && $novoSlug !== $original['slug']) {
                    $acao .= ' O link passou a ser /' . $novoSlug . '.';
                }
            }

            if (!$erros) {
                // Roda depois de gravar: a conferência de uso consulta o banco e
                // precisa enxergar o conteúdo já atualizado.
                unset($_SESSION['imagens_enviadas']);
                $descartadas = Postagem::limparImagensOrfas($candidatasImagens, $formulario['conteudo']);

                if ($descartadas) {
                    $acao .= ' ' . count($descartadas)
                           . (count($descartadas) === 1 ? ' imagem não utilizada foi removida.' : ' imagens não utilizadas foram removidas.');
                }

                $_SESSION['flash_ok'] = $acao;

                header('Location: ' . ($id === null ? $urlPagina : BASE_URL . '/backoffice/main/postagens'));
                exit;
            }
        }
    }
}

// Abertura para edição a partir da listagem.
$editando = false;
if (!$erros && isset($_GET['editar'])) {
    $registro = Postagem::buscar((int) $_GET['editar']);

    if ($registro) {
        $editando = true;
        $formulario = [
            'id'        => $registro['id'],
            'link'      => $registro['urlimagem'],
            'video'     => YouTube::extrairId($registro['urlimagem']),
            'titulo'    => $registro['titulo'],
            'tipo'      => $registro['tipo'],
            'categoria' => $registro['categoria'],
            'tags'      => implode(',', json_decode($registro['tags'], true) ?: []),
            'data'      => date('Y-m-d\TH:i', strtotime($registro['data'])),
            'conteudo'  => $registro['conteudo'],
            'novolink'  => '',
        ];
    } else {
        $erros[] = 'Postagem não encontrada.';
    }
}

if ($formulario['id'] !== '') {
    $editando = true;
}

$tituloPagina = $editando ? 'Editar Postagem' : 'Postar Vídeo';

$flashOk = $_SESSION['flash_ok'] ?? null;
unset($_SESSION['flash_ok']);

// O bloco de dados abre já preenchido na edição e quando a validação falha.
$mostrarDados = $editando || ($erros && $formulario['titulo'] !== '');

// Na edição a capa é lida do registro, e não deduzida do vídeo: postagens sem
// vídeo usam capas com outros nomes, às vezes compartilhadas entre várias.
$capaAtual = '';
$temVideo  = false;

if ($editando && $formulario['id'] !== '') {
    $registroAtual = Postagem::buscar((int) $formulario['id']);

    if ($registroAtual) {
        $capaAtual = (string) $registroAtual['imagem'];
        $temVideo  = YouTube::extrairId((string) $registroAtual['urlimagem']) !== null;
    }
}

include __DIR__ . '/../assets/views/dash-topo.php';
?>

<?php if ($flashOk): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= e($flashOk) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if ($erros): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?php if (count($erros) === 1): ?>
            <?= e($erros[0]) ?>
        <?php else: ?>
            <ul class="mb-0 mt-1"><?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?></ul>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if (!$tipos): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Nenhum tipo encontrado.</strong>
        Para publicar pelo backoffice, ao menos um registro da tabela <code>tipo</code>
        precisa estar com o campo <code>Local</code> igual a <code>1</code>.
    </div>
<?php endif; ?>

<form method="post" id="form-video">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($formulario['id']) ?>">
    <input type="hidden" name="video" id="video" value="<?= e($formulario['video']) ?>">

    <!-- Passo 1: identificar o vídeo -->
    <div class="bo-painel mb-4">
        <div class="bo-painel-cab">
            <h2 class="bo-painel-titulo"><i class="bi bi-youtube text-danger"></i> Vídeo do YouTube</h2>
            <?php if ($editando): ?>
                <a href="<?= BASE_URL ?>/backoffice/main/postagens" class="btn btn-sm btn-outline-secondary ms-auto">
                    <i class="bi bi-arrow-left"></i> Voltar à lista
                </a>
            <?php endif; ?>
        </div>
        <div class="bo-painel-corpo">
            <?php if ($editando): ?>
                <div class="d-flex gap-3 align-items-start flex-wrap">
                    <?php if ($capaAtual !== ''): ?>
                        <img id="capa-atual"
                             src="<?= e(BASE_URL . '/' . caminhoCapa($capaAtual)) ?>"
                             alt="Capa do vídeo" class="rounded border"
                             style="width:240px;aspect-ratio:16/9;object-fit:cover">
                    <?php endif; ?>
                    <div class="small">
                        <div class="text-muted">Vídeo</div>
                        <div class="fw-semibold mb-2">
                            <?php if ($temVideo): ?>
                                <a href="<?= e($formulario['link']) ?>" target="_blank" rel="noopener">
                                    <?= e($formulario['link']) ?> <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-body-secondary fw-normal">Esta postagem não tem vídeo do YouTube.</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted">Capa</div>
                        <div class="fw-semibold mb-2"><?= $capaAtual !== '' ? e($capaAtual) : '&mdash;' ?></div>

                        <?php if ($temVideo): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger mb-2" id="btn-atualizar-capa"
                                    data-post="<?= (int) $formulario['id'] ?>">
                                <i class="bi bi-arrow-repeat"></i> Atualizar capa do YouTube
                            </button>
                            <div id="aviso-capa"></div>

                            <div class="text-body-secondary">
                                Trocou a miniatura no YouTube? Use o botão acima para trazer a nova.<br>
                                O vídeo em si não muda na edição &mdash; para outro vídeo, crie uma nova postagem.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <label for="link" class="form-label">Link do vídeo</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="link" name="link"
                           value="<?= e($formulario['link']) ?>"
                           placeholder="https://www.youtube.com/watch?v=..."
                           <?= $tipos ? '' : 'disabled' ?>>
                    <button class="btn btn-danger" type="button" id="btn-carregar" <?= $tipos ? '' : 'disabled' ?>>
                        <i class="bi bi-download"></i> Carregar vídeo
                    </button>
                </div>
                <div class="form-text">Cole o endereço do vídeo. O título, a capa e o player são preenchidos automaticamente.</div>

                <div id="aviso-video" class="mt-3"></div>

                <div id="previa" class="mt-3 d-none">
                    <div class="d-flex gap-3 align-items-start flex-wrap">
                        <img id="previa-capa" src="" alt="Capa do vídeo" class="rounded border" style="width:240px;aspect-ratio:16/9;object-fit:cover">
                        <div class="small">
                            <div class="text-muted">Canal</div>
                            <div id="previa-canal" class="fw-semibold mb-2"></div>
                            <div class="text-muted">Identificador</div>
                            <div id="previa-id" class="fw-semibold mb-2"></div>
                            <div class="text-muted">Capa que será salva</div>
                            <div class="fw-semibold"><span id="previa-arquivo"></span> &middot; 480&times;270</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Passo 2: dados da postagem -->
    <div class="bo-painel <?= $mostrarDados ? '' : 'd-none' ?>" id="bloco-dados">
        <div class="bo-painel-cab">
            <h2 class="bo-painel-titulo">Dados da postagem</h2>
        </div>
        <div class="bo-painel-corpo">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo"
                       maxlength="255" value="<?= e($formulario['titulo']) ?>">
                <div class="form-text">Vem do YouTube, com emojis. Você pode editar antes de salvar.</div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select class="form-select" id="tipo" name="tipo">
                        <option value="">Selecione...</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= (int) $t['id'] ?>" <?= (string) $formulario['tipo'] === (string) $t['id'] ? 'selected' : '' ?>>
                                <?= e($t['Nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="categoria" class="form-label">Categoria</label>
                    <select class="form-select" id="categoria" name="categoria">
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (string) $formulario['categoria'] === (string) $c['id'] ? 'selected' : '' ?>>
                                <?= e($c['Nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="tag-entrada" class="form-label">Tags</label>
                <div class="form-control d-flex flex-wrap gap-2 align-items-center" id="tags-caixa" style="min-height:calc(2.5rem + 2px)">
                    <input type="text" id="tag-entrada" class="border-0 flex-grow-1"
                           style="outline:none;min-width:12rem" placeholder="Digite e pressione Enter">
                </div>
                <input type="hidden" name="tags" id="tags" value="<?= e($formulario['tags']) ?>">
                <div class="form-text">Enter ou vírgula adiciona a tag. Clique no <i class="bi bi-x"></i> para remover.</div>
            </div>

            <div class="mb-3">
                <label for="data" class="form-label">Data de publicação</label>
                <input type="datetime-local" class="form-control" id="data" name="data"
                       value="<?= e($formulario['data']) ?>" style="max-width:20rem">
                <div class="form-text">Uma data futura agenda a postagem: ela só aparece no site na hora marcada.</div>
            </div>

            <div class="mb-3">
                <label for="conteudo" class="form-label">Conteúdo</label>
                <textarea class="form-control" id="conteudo" name="conteudo" rows="14"><?= e($formulario['conteudo']) ?></textarea>
                <div class="form-text">
                    Já vem com o player do vídeo. Use <strong>Imagem</strong> na barra para enviar fotos
                    &mdash; elas são convertidas para <code>.webp</code> automaticamente.
                </div>
            </div>

            <?php if ($editando): ?>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="novolink" name="novolink"
                           <?= $formulario['novolink'] !== '' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="novolink">
                        Atualizar o link da postagem a partir do novo título
                    </label>
                    <div class="form-text">
                        Sem marcar, o endereço continua o mesmo. Marque só se o título mudou muito &mdash;
                        links já compartilhados deixam de funcionar.
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> <?= $editando ? 'Salvar alterações' : 'Salvar postagem' ?>
                </button>
                <a href="<?= $editando ? BASE_URL . '/backoffice/main/postagens' : $urlPagina ?>"
                   class="btn btn-outline-secondary"><?= $editando ? 'Cancelar' : 'Limpar' ?></a>
            </div>
        </div>
    </div>
</form>

<?php
// O TinyMCE precisa estar carregado antes do script que o inicializa.
$scriptsExtras = ['tinymce/tinymce.min.js', 'js/postagem-video.js'];
include __DIR__ . '/../assets/views/dash-rodape.php';
