<?php
require 'lib/config.php';
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php
$title = "Política de Privacidade - Blog Pablo Sato";
include('assets/views/head.php');
?>

<body class="<?= $temaBody ?>">

  <?php include __DIR__ . '/assets/views/header_navebar.php'; ?>

  <div class="container">
    <div class="row">

      <div class="col-md-9">
        <h1 class="post-titulo pt-4">Política de Privacidade</h1>
        <p class="post-meta-bar mb-4"><small>Última atualização: junho de <?= date('Y') ?></small></p>

        <article class="post-content mb-5">

          <h2>1. Quem somos</h2>
          <p>
            O <strong>Blog Pablo Sato</strong> (<a href="<?= BASE_URL ?>">meusite.com</a>) é um blog
            independente sobre tecnologia e desenvolvimento, operado por Pablo Sato.
          </p>

          <h2>2. Quais dados coletamos</h2>
          <p>Ao navegar neste site, os seguintes dados podem ser coletados automaticamente:</p>
          <ul>
            <li><strong>Cookies de sessão e navegação:</strong> arquivos salvos no navegador para melhorar a experiência (como preferência de tema claro/escuro).</li>
            <li><strong>Dados de acesso:</strong> endereço IP, tipo de navegador, páginas visitadas e tempo de permanência.</li>
            <li><strong>Dados de interação:</strong> buscas realizadas e conteúdos acessados.</li>
          </ul>

          <h2>3. Como usamos os dados</h2>
          <ul>
            <li>Melhorar a experiência de navegação</li>
            <li>Analisar o desempenho e o tráfego do site</li>
            <li>Personalizar conteúdo exibido</li>
          </ul>

          <h2>4. Cookies</h2>
          <p>
            Utilizamos cookies essenciais para o funcionamento do site (como a preferência de tema) e
            cookies analíticos para entender como os visitantes usam o conteúdo. Você pode recusar os
            cookies opcionais pelo banner exibido na primeira visita.
          </p>

          <h2>5. Compartilhamento de dados</h2>
          <p>
            Não vendemos, alugamos nem compartilhamos seus dados pessoais com terceiros para fins comerciais.
            Dados analíticos podem ser processados por ferramentas de terceiros (como Google Analytics),
            sujeitas às suas próprias políticas de privacidade.
          </p>

          <h2>6. Seus direitos (LGPD)</h2>
          <p>Conforme a Lei nº 13.709/2018, você tem direito a:</p>
          <ul>
            <li>Saber quais dados seus são tratados</li>
            <li>Solicitar correção de dados incorretos</li>
            <li>Solicitar exclusão de dados desnecessários</li>
            <li>Revogar seu consentimento a qualquer momento</li>
          </ul>

          <h2>7. Contato</h2>
          <p>
            Para exercer seus direitos ou tirar dúvidas sobre esta política, entre em contato:
            contato@meusite.com
          </p>

          <h2>8. Alterações nesta política</h2>
          <p>
            Esta política pode ser atualizada periodicamente. A data da última revisão está indicada no
            topo desta página. Recomendamos que você a consulte regularmente.
          </p>

        </article>
      </div><!-- col-md-9 -->

      <?php include __DIR__ . '/assets/views/coluna_sobre.php'; ?>

    </div><!-- row -->
  </div>

  <?php include __DIR__ . '/assets/views/footer.php'; ?>

</body>
