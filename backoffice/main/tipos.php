<?php
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Tipo.php';

$paginaAtual  = 'tipos';
$tituloPagina = 'Tipos de Postagem';

$urlPagina = BASE_URL . '/backoffice/main/tipos';
$erros     = [];

$formulario = ['id' => '', 'nome' => '', 'local' => Tipo::VIDEO];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $erros[] = 'Sessão inválida. Recarregue a página e tente novamente.';
    } else {
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'excluir') {
            $alvo     = (int) ($_POST['id'] ?? 0);
            $registro = Tipo::buscar($alvo);

            if (!$registro) {
                $_SESSION['flash_erro'] = 'Tipo não encontrado.';
            } else {
                // Excluir um tipo em uso deixaria as postagens apontando para
                // um registro que não existe mais, e elas apareceriam sem tipo
                // no site.
                $emUso = Tipo::totalPosts($alvo);

                if ($emUso > 0) {
                    $_SESSION['flash_erro'] = 'O tipo "' . $registro['Nome'] . '" não pode ser excluído: '
                        . $emUso . ($emUso === 1 ? ' postagem usa' : ' postagens usam') . ' ele. '
                        . 'Troque o tipo dessas postagens antes de excluir.';
                } else {
                    Tipo::excluir($alvo);
                    $_SESSION['flash_ok'] = 'Tipo excluído.';
                }
            }

            header('Location: ' . $urlPagina);
            exit;
        }

        if ($acao === 'salvar') {
            $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;

            $formulario = [
                'id'    => $_POST['id'] ?? '',
                'nome'  => trim($_POST['nome'] ?? ''),
                'local' => (int) ($_POST['local'] ?? Tipo::VIDEO),
            ];

            if ($formulario['nome'] === '') {
                $erros[] = 'Informe o nome do tipo.';
            } elseif (mb_strlen($formulario['nome']) > 50) {
                $erros[] = 'O nome do tipo deve ter no máximo 50 caracteres.';
            } elseif (Tipo::nomeEmUso($formulario['nome'], $id)) {
                $erros[] = 'Já existe um tipo com esse nome.';
            }

            if (!in_array($formulario['local'], [Tipo::VIDEO, Tipo::POSTAGEM], true)) {
                $erros[] = 'Escolha se o tipo é de vídeo ou de postagem escrita.';
            }

            if (!$erros) {
                $dados = ['nome' => $formulario['nome'], 'local' => $formulario['local']];

                if ($id === null) {
                    Tipo::criar($dados);
                    $_SESSION['flash_ok'] = 'Tipo cadastrado.';
                } else {
                    Tipo::atualizar($id, $dados);
                    $_SESSION['flash_ok'] = 'Tipo atualizado.';
                }

                header('Location: ' . $urlPagina);
                exit;
            }
        }
    }
}

// Edição: carrega o tipo no formulário.
$editando = false;

if (!$erros && isset($_GET['editar'])) {
    $registro = Tipo::buscar((int) $_GET['editar']);

    if ($registro) {
        $formulario = [
            'id'    => $registro['id'],
            'nome'  => $registro['Nome'],
            'local' => (int) $registro['Local'],
        ];
        $editando = true;
    }
}

if ($erros && ($_POST['id'] ?? '') !== '') {
    $editando = true;
}

$tipos = Tipo::listar();

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
                    <?= $editando ? 'Editar tipo' : 'Novo tipo' ?>
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
                        <label for="nome" class="form-label">Nome do tipo</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= e($formulario['nome']) ?>" maxlength="50" required autofocus>
                        <div class="form-text">É o que aparece como tipo da postagem no site.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Onde este tipo é usado</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="local" id="local-video"
                                   value="<?= Tipo::VIDEO ?>"
                                   <?= $formulario['local'] === Tipo::VIDEO ? 'checked' : '' ?>>
                            <label class="form-check-label" for="local-video">
                                <strong>Vídeo</strong> &mdash; aparece ao publicar vídeo do YouTube
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="local" id="local-postagem"
                                   value="<?= Tipo::POSTAGEM ?>"
                                   <?= $formulario['local'] === Tipo::POSTAGEM ? 'checked' : '' ?>>
                            <label class="form-check-label" for="local-postagem">
                                <strong>Postagem escrita</strong> &mdash; para os artigos
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i>
                        <?= $editando ? 'Salvar alterações' : 'Cadastrar tipo' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="bo-painel">
            <div class="bo-painel-cab">
                <h2 class="bo-painel-titulo">
                    Tipos cadastrados
                    <span class="badge bg-secondary ms-1"><?= count($tipos) ?></span>
                </h2>
            </div>

            <?php if (!$tipos): ?>
                <div class="bo-painel-corpo">
                    <p class="text-muted mb-0">Nenhum tipo cadastrado ainda.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table bo-tabela align-middle">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Usado em</th>
                                <th>Postagens</th>
                                <th style="width:100px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tipos as $tipo): ?>
                                <?php $usos = (int) $tipo['postagens']; ?>
                                <tr>
                                    <td><?= e($tipo['Nome']) ?></td>
                                    <td>
                                        <?php if ((int) $tipo['Local'] === Tipo::VIDEO): ?>
                                            <span class="badge bg-danger"><i class="bi bi-youtube"></i> Vídeo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><i class="bi bi-file-earmark-text"></i> Postagem</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small"><?= $usos ?></td>
                                    <td class="text-nowrap">
                                        <a href="<?= $urlPagina ?>?editar=<?= (int) $tipo['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <?php if ($usos > 0): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                    title="Em uso por <?= $usos ?> postagem(ns) — não pode ser excluído">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        <?php else: ?>
                                            <form method="post" class="d-inline"
                                                  data-confirmar="Excluir o tipo &quot;<?= e($tipo['Nome']) ?>&quot;? Esta ação não pode ser desfeita."
                                                  data-confirmar-titulo="Excluir tipo"
                                                  data-confirmar-ok="Excluir">
                                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="id" value="<?= (int) $tipo['id'] ?>">
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
                        Tipos já usados por alguma postagem aparecem com cadeado e não podem ser excluídos.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/views/dash-rodape.php'; ?>
