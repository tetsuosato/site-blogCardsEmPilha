<?php
// Abre a estrutura do dashboard. Espera $paginaAtual e $tituloPagina definidos.
$title = ($tituloPagina ?? 'Dashboard') . ' | Backoffice Blog Pablo Sato';
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include __DIR__ . '/head.php'; ?>

<body class="bo-body">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="bo-main">
        <?php include __DIR__ . '/topbar.php'; ?>
        <main class="bo-conteudo">
