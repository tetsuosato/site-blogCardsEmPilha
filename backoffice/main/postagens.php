<?php
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Postagem.php';

$paginaAtual  = 'postagens';
$tituloPagina = 'Postagens';

$urlPagina = BASE_URL . '/backoffice/main/postagens';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $_SESSION['flash_erro'] = 'Sessão inválida. Recarregue a página e tente novamente.';
    } elseif (($_POST['acao'] ?? '') === 'excluir') {
        $alvo = (int) ($_POST['id'] ?? 0);
        $post = Postagem::buscar($alvo);

        if (!$post) {
            $_SESSION['flash_erro'] = 'Postagem não encontrada.';
        } else {
            $removidos = Postagem::excluir($alvo);

            $mensagem = 'Postagem excluída.';
            $arquivos = [];

            if ($removidos['capa']) {
                $arquivos[] = 'a capa';
            }
            if ($removidos['imagens'] > 0) {
                $arquivos[] = $removidos['imagens']
                            . ($removidos['imagens'] === 1 ? ' imagem do conteúdo' : ' imagens do conteúdo');
            }
            if ($arquivos) {
                $mensagem .= ' Também foram removidos do servidor: ' . implode(' e ', $arquivos) . '.';
            }

            $_SESSION['flash_ok'] = $mensagem;
        }
    }

    // Mantém busca e página atuais depois da ação.
    header('Location: ' . $urlPagina . (($_POST['volta'] ?? '') !== '' ? '?' . $_POST['volta'] : ''));
    exit;
}

$busca    = trim($_GET['busca'] ?? '');
$situacao = $_GET['situacao'] ?? '';
$pagina   = max(1, (int) ($_GET['pagina'] ?? 1));
$porPagina = 20;

if (!in_array($situacao, ['publicadas', 'agendadas'], true)) {
    $situacao = '';
}

$total     = Postagem::contar($busca, $situacao);
$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina    = min($pagina, $totalPaginas);
$postagens = Postagem::listar($busca, $situacao, $porPagina, ($pagina - 1) * $porPagina);

// Preserva os filtros nos links e no retorno das ações.
$filtros = array_filter([
    'busca'    => $busca,
    'situacao' => $situacao,
    'pagina'   => $pagina > 1 ? $pagina : '',
], function ($v) { return $v !== '' && $v !== null; });
$queryAtual = http_build_query($filtros);

$flashOk   = $_SESSION['flash_ok'] ?? null;
$flashErro = $_SESSION['flash_erro'] ?? null;
unset($_SESSION['flash_ok'], $_SESSION['flash_erro']);

/** Monta o endereço da listagem trocando apenas os filtros informados. */
function linkFiltro(array $trocas) {
    global $filtros, $urlPagina;

    $novos = array_merge($filtros, $trocas);
    $novos = array_filter($novos, function ($v) { return $v !== '' && $v !== null; });

    return $urlPagina . ($novos ? '?' . http_build_query($novos) : '');
}

include __DIR__ . '/../assets/views/dash-topo.php';
?>

<?php if ($flashOk): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= e($flashOk) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if ($flashErro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($flashErro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<div class="bo-painel">
    <div class="bo-painel-cab">
        <h2 class="bo-painel-titulo">
            Postagens
            <span class="badge bg-secondary ms-1"><?= $total ?></span>
        </h2>

        <a href="<?= BASE_URL ?>/backoffice/main/postagem-video" class="btn btn-sm btn-danger ms-auto">
            <i class="bi bi-plus-lg"></i> Nova postagem
        </a>
    </div>

    <div class="bo-painel-corpo">
        <form method="get" class="row g-2 align-items-end mb-3">
            <div class="col-md-6">
                <label for="busca" class="form-label">Buscar</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="busca" name="busca"
                           value="<?= e($busca) ?>" placeholder="Título ou tag">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <label for="situacao" class="form-label">Situação</label>
                <select class="form-select" id="situacao" name="situacao" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <option value="publicadas" <?= $situacao === 'publicadas' ? 'selected' : '' ?>>Publicadas</option>
                    <option value="agendadas"  <?= $situacao === 'agendadas'  ? 'selected' : '' ?>>Agendadas</option>
                </select>
            </div>
            <?php if ($busca !== '' || $situacao !== ''): ?>
                <div class="col-md-2">
                    <a href="<?= $urlPagina ?>" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            <?php endif; ?>
        </form>

        <?php if (!$postagens): ?>
            <p class="text-muted mb-0">
                <?= $busca !== '' || $situacao !== ''
                    ? 'Nenhuma postagem encontrada para esse filtro.'
                    : 'Nenhuma postagem cadastrada ainda.' ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if ($postagens): ?>
        <div class="table-responsive">
            <table class="table bo-tabela align-middle">
                <thead>
                    <tr>
                        <th style="width:88px">Capa</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Autor</th>
                        <th>Publicação</th>
                        <th style="width:130px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($postagens as $post): ?>
                        <?php $agendado = strtotime($post['data']) > time(); ?>
                        <tr>
                            <td>
                                <img src="<?= BASE_URL ?>/images/img-youtube/<?= e($post['imagem']) ?>"
                                     alt="" class="bo-thumb" loading="lazy">
                            </td>
                            <td>
                                <div><?= e($post['titulo']) ?></div>
                                <div class="text-muted small"><?= e($post['tipo']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= e($post['categoria']) ?></span></td>
                            <td class="text-muted small"><?= e($post['autor']) ?></td>
                            <td class="small text-nowrap">
                                <?= date('d/m/Y H:i', strtotime($post['data'])) ?>
                                <?php if ($agendado): ?>
                                    <span class="badge bg-warning text-dark d-block mt-1">agendada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <a href="<?= BASE_URL ?>/backoffice/main/postagem-video?editar=<?= (int) $post['id'] ?>"
                                   class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <a href="<?= BASE_URL ?>/postagem/<?= slugify($post['categoria']) ?>/<?= e($post['slug']) ?>/<?= (int) $post['id'] ?>"
                                   target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver no site">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>

                                <form method="post" class="d-inline"
                                      data-confirmar="Excluir a postagem &quot;<?= e($post['titulo']) ?>&quot;? A capa e as imagens usadas só nela também serão apagadas. Esta ação não pode ser desfeita."
                                      data-confirmar-titulo="Excluir postagem"
                                      data-confirmar-ok="Excluir">
                                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                                    <input type="hidden" name="volta" value="<?= e($queryAtual) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>
            <div class="bo-painel-corpo d-flex justify-content-between align-items-center">
                <span class="text-muted small">Página <?= $pagina ?> de <?= $totalPaginas ?></span>
                <div class="btn-group">
                    <a href="<?= linkFiltro(['pagina' => $pagina - 1]) ?>"
                       class="btn btn-sm btn-outline-secondary <?= $pagina <= 1 ? 'disabled' : '' ?>">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </a>
                    <a href="<?= linkFiltro(['pagina' => $pagina + 1]) ?>"
                       class="btn btn-sm btn-outline-secondary <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                        Próxima <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../assets/views/dash-rodape.php'; ?>
