<?php
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Categoria.php';
require_once __DIR__ . '/../../api/functions/functions.php';

$paginaAtual  = 'categorias';
$tituloPagina = 'Categorias';

$urlPagina = BASE_URL . '/backoffice/main/categorias';
$erros     = [];

$formulario = ['id' => '', 'nome' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $erros[] = 'Sessão inválida. Recarregue a página e tente novamente.';
    } else {
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'excluir') {
            $alvo     = (int) ($_POST['id'] ?? 0);
            $registro = Categoria::buscar($alvo);

            if (!$registro) {
                $_SESSION['flash_erro'] = 'Categoria não encontrada.';
            } else {
                // Excluir uma categoria em uso deixaria as postagens apontando
                // para um registro inexistente, e elas apareceriam sem
                // categoria no site.
                $emUso = Categoria::totalPosts($alvo);

                if ($emUso > 0) {
                    $_SESSION['flash_erro'] = 'A categoria "' . $registro['Nome'] . '" não pode ser excluída: '
                        . $emUso . ($emUso === 1 ? ' postagem usa' : ' postagens usam') . ' ela. '
                        . 'Troque a categoria dessas postagens antes de excluir.';
                } else {
                    Categoria::excluir($alvo);
                    $_SESSION['flash_ok'] = 'Categoria excluída.';
                }
            }

            header('Location: ' . $urlPagina);
            exit;
        }

        if ($acao === 'salvar') {
            $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;

            $formulario = [
                'id'   => $_POST['id'] ?? '',
                'nome' => trim($_POST['nome'] ?? ''),
            ];

            if ($formulario['nome'] === '') {
                $erros[] = 'Informe o nome da categoria.';
            } elseif (mb_strlen($formulario['nome']) > 50) {
                $erros[] = 'O nome da categoria deve ter no máximo 50 caracteres.';
            } elseif (Categoria::nomeEmUso($formulario['nome'], $id)) {
                $erros[] = 'Já existe uma categoria com esse nome.';
            }

            if (!$erros) {
                if ($id === null) {
                    Categoria::criar(['nome' => $formulario['nome']]);
                    $_SESSION['flash_ok'] = 'Categoria cadastrada.';
                } else {
                    Categoria::atualizar($id, ['nome' => $formulario['nome']]);
                    $_SESSION['flash_ok'] = 'Categoria atualizada.';
                }

                header('Location: ' . $urlPagina);
                exit;
            }
        }
    }
}

// Edição: carrega a categoria no formulário.
$editando = false;

if (!$erros && isset($_GET['editar'])) {
    $registro = Categoria::buscar((int) $_GET['editar']);

    if ($registro) {
        $formulario = ['id' => $registro['id'], 'nome' => $registro['Nome']];
        $editando   = true;
    }
}

if ($erros && ($_POST['id'] ?? '') !== '') {
    $editando = true;
}

$categorias = Categoria::listar();

$flashOk   = $_SESSION['flash_ok']   ?? null;
$flashErro = $_SESSION['flash_erro'] ?? null;
unset($_SESSION['flash_ok'], $_SESSION['flash_erro']);

include __DIR__ . '/../assets/views/dash-topo.php';
?>

<?php if ($flashOk): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= e($flashOk) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($flashErro): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= e($flashErro) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($erros): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Corrija os itens abaixo:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach ($erros as $erro): ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-xl-5">
        <div class="bo-painel">
            <div class="bo-painel-cab">
                <h2 class="bo-painel-titulo">
                    <i class="bi <?= $editando ? 'bi-pencil-square' : 'bi-plus-circle-fill' ?>"></i>
                    <?= $editando ? 'Editar categoria' : 'Nova categoria' ?>
                </h2>
                <?php if ($editando): ?>
                    <a href="<?= $urlPagina ?>" class="btn btn-sm btn-outline-secondary ms-auto">Cancelar</a>
                <?php endif; ?>
            </div>
            <div class="bo-painel-corpo">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id" value="<?= e($formulario['id']) ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da categoria</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= e($formulario['nome']) ?>" maxlength="50" required autofocus>
                        <div class="form-text">
                            Aparece no site e no endereço das postagens.
                            <?php if ($editando): ?>
                                <br>Renomear não quebra os links já publicados: a página é
                                localizada pelo endereço da própria postagem.
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($editando && $formulario['nome'] !== ''): ?>
                        <div class="mb-3 small text-body-secondary">
                            Endereço gerado:
                            <code>/postagem/<?= e(slugify($formulario['nome'])) ?>/...</code>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i>
                        <?= $editando ? 'Salvar alterações' : 'Cadastrar categoria' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="bo-painel">
            <div class="bo-painel-cab">
                <h2 class="bo-painel-titulo">
                    Categorias cadastradas
                    <span class="badge bg-secondary ms-1"><?= count($categorias) ?></span>
                </h2>
            </div>

            <?php if (!$categorias): ?>
                <div class="bo-painel-corpo">
                    <p class="text-muted mb-0">Nenhuma categoria cadastrada ainda.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table bo-tabela align-middle">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Postagens</th>
                                <th style="width:100px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $categoria): ?>
                                <?php $usos = (int) $categoria['postagens']; ?>
                                <tr>
                                    <td><?= e($categoria['Nome']) ?></td>
                                    <td class="text-muted small"><?= $usos ?></td>
                                    <td class="text-nowrap">
                                        <a href="<?= $urlPagina ?>?editar=<?= (int) $categoria['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <?php if ($usos > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                    title="Em uso por <?= $usos ?> postagem(ns) — não pode ser excluída">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        <?php else: ?>
                                            <form method="post" class="d-inline"
                                                  data-confirmar="Excluir a categoria &quot;<?= e($categoria['Nome']) ?>&quot;? Esta ação não pode ser desfeita."
                                                  data-confirmar-titulo="Excluir categoria"
                                                  data-confirmar-ok="Excluir">
                                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="bo-painel-corpo pt-0">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle"></i>
                        Categorias já usadas por alguma postagem aparecem com cadeado e não podem ser excluídas.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/views/dash-rodape.php'; ?>
