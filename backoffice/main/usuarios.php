<?php
require_once __DIR__ . '/../assets/views/auth-guard.php';
require_once __DIR__ . '/../class/Usuario.php';

$paginaAtual  = 'usuarios';
$tituloPagina = 'Usuários';

$urlPagina = BASE_URL . '/backoffice/main/usuarios';
$erros     = [];
$formulario = [
    'id' => '', 'name' => '', 'lastname' => '', 'nickname' => '',
    'user' => '', 'email' => '', 'ativo' => 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? '')) {
        $erros[] = 'Sessão inválida. Recarregue a página e tente novamente.';
    } else {
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'excluir' || $acao === 'alternar') {
            $alvo = (int) ($_POST['id'] ?? 0);

            if ($alvo === (int) $_SESSION['id_user']) {
                $_SESSION['flash_erro'] = 'Você não pode excluir ou desativar a própria conta.';
            } elseif ($acao === 'excluir' && Usuario::totalPosts($alvo) > 0) {
                $_SESSION['flash_erro'] = 'Este usuário possui postagens vinculadas. Desative-o em vez de excluir.';
            } elseif ($acao === 'excluir') {
                Usuario::excluir($alvo);
                $_SESSION['flash_ok'] = 'Usuário excluído.';
            } else {
                Usuario::alternarAtivo($alvo);
                $_SESSION['flash_ok'] = 'Status do usuário atualizado.';
            }

            header('Location: ' . $urlPagina);
            exit;
        }

        if ($acao === 'salvar') {
            $id = $_POST['id'] !== '' ? (int) $_POST['id'] : null;

            $formulario = [
                'id'       => $_POST['id'] ?? '',
                'name'     => trim($_POST['name'] ?? ''),
                'lastname' => trim($_POST['lastname'] ?? ''),
                'nickname' => trim($_POST['nickname'] ?? ''),
                'user'     => trim($_POST['user'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
                'ativo'    => isset($_POST['ativo']) ? 1 : 0,
            ];
            $senha = $_POST['password'] ?? '';

            if ($formulario['name'] === '')     $erros[] = 'Informe o nome.';
            if ($formulario['lastname'] === '') $erros[] = 'Informe o sobrenome.';
            if ($formulario['nickname'] === '') $erros[] = 'Informe o nome de exibição (aparece como autor no site).';
            if ($formulario['user'] === '')     $erros[] = 'Informe o login.';

            if (!filter_var($formulario['email'], FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'Informe um e-mail válido.';
            } elseif (Usuario::emUso('email', $formulario['email'], $id)) {
                $erros[] = 'Este e-mail já está em uso por outro usuário.';
            }

            if ($formulario['user'] !== '' && Usuario::emUso('user', $formulario['user'], $id)) {
                $erros[] = 'Este login já está em uso por outro usuário.';
            }

            if ($id === null && $senha === '') {
                $erros[] = 'Informe uma senha para o novo usuário.';
            } elseif ($senha !== '' && strlen($senha) < 8) {
                $erros[] = 'A senha deve ter no mínimo 8 caracteres.';
            }

            // Um usuário não pode se auto-desativar e perder o acesso na sequência.
            if ($id === (int) $_SESSION['id_user'] && $formulario['ativo'] === 0) {
                $erros[] = 'Você não pode desativar a própria conta.';
            }

            if (!$erros) {
                $dados = $formulario;
                $dados['password'] = $senha;

                if ($id === null) {
                    Usuario::criar($dados);
                    $_SESSION['flash_ok'] = 'Usuário cadastrado com sucesso.';
                } else {
                    Usuario::atualizar($id, $dados);
                    $_SESSION['flash_ok'] = 'Usuário atualizado com sucesso.';
                }

                header('Location: ' . $urlPagina);
                exit;
            }
        }
    }
}

// Edição: carrega o usuário no formulário.
$editando = false;
if (!$erros && isset($_GET['editar'])) {
    $registro = Usuario::buscar((int) $_GET['editar']);
    if ($registro) {
        $formulario = $registro;
        $editando = true;
    }
}
if ($erros && ($_POST['id'] ?? '') !== '') {
    $editando = true;
}

$usuarios = Usuario::listar();

$flashOk  = $_SESSION['flash_ok']  ?? null;
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
                    <i class="bi <?= $editando ? 'bi-pencil-square' : 'bi-person-plus-fill' ?>"></i>
                    <?= $editando ? 'Editar usuário' : 'Novo usuário' ?>
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

                    <div class="row g-2">
                        <div class="col-sm-6 mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= e($formulario['name']) ?>" maxlength="100" required>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="lastname" class="form-label">Sobrenome</label>
                            <input type="text" class="form-control" id="lastname" name="lastname"
                                   value="<?= e($formulario['lastname']) ?>" maxlength="100" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nickname" class="form-label">Nome de exibição</label>
                        <input type="text" class="form-control" id="nickname" name="nickname"
                               value="<?= e($formulario['nickname']) ?>" maxlength="100" required>
                        <div class="form-text">É o nome que aparece como autor das postagens no site.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($formulario['email']) ?>" maxlength="150" required>
                        <div class="form-text">Usado para entrar no backoffice.</div>
                    </div>

                    <div class="mb-3">
                        <label for="user" class="form-label">Login</label>
                        <input type="text" class="form-control" id="user" name="user"
                               value="<?= e($formulario['user']) ?>" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Senha <?= $editando ? '<span class="text-muted fw-normal">(deixe vazio para manter)</span>' : '' ?>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password"
                                   minlength="8" <?= $editando ? '' : 'required' ?>>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Mínimo de 8 caracteres.</div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                               <?= $formulario['ativo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ativo">Usuário ativo</label>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-save"></i> <?= $editando ? 'Salvar alterações' : 'Cadastrar usuário' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="bo-painel">
            <div class="bo-painel-cab">
                <h2 class="bo-painel-titulo">Usuários cadastrados</h2>
                <span class="badge bg-secondary ms-auto"><?= count($usuarios) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table bo-tabela align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th>Último acesso</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <?php $souEu = (int) $u['id'] === (int) $_SESSION['id_user']; ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= e($u['name'] . ' ' . $u['lastname']) ?></div>
                                    <div class="text-muted small">
                                        <?= e($u['nickname']) ?>
                                        <?php if ($souEu): ?>
                                            <span class="badge bg-primary ms-1">você</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="small"><?= e($u['email']) ?></td>
                                <td>
                                    <?php if ($u['ativo']): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small text-nowrap">
                                    <?php
                                    $acesso = strtotime($u['data_login']);
                                    echo ($acesso && date('Y', $acesso) > 1900) ? date('d/m/Y H:i', $acesso) : '—';
                                    ?>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= $urlPagina ?>?editar=<?= (int) $u['id'] ?>"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="acao" value="alternar">
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                title="<?= $u['ativo'] ? 'Desativar' : 'Ativar' ?>"
                                                <?= $souEu ? 'disabled' : '' ?>>
                                            <i class="bi <?= $u['ativo'] ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                        </button>
                                    </form>

                                    <form method="post" class="d-inline"
                                          data-confirmar="Excluir o usuário <?= e($u['name'] . ' ' . $u['lastname']) ?>? Esta ação não pode ser desfeita."
                                          data-confirmar-titulo="Excluir usuário"
                                          data-confirmar-ok="Excluir">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"
                                                <?= $souEu ? 'disabled' : '' ?>>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/views/dash-rodape.php'; ?>
