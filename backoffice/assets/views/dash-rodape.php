        </main>
    </div>

    <?php include __DIR__ . '/modal-confirmacao.php'; ?>

    <script src="<?= BASE_URL ?>/assets/bootstrap/5.3.8/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= BASE_URL ?>/backoffice/assets/js/dashboard.js?v=<?= time() ?>"></script>

    <?php // Scripts da página, com caminho a partir de backoffice/assets/ e na ordem informada. ?>
    <?php foreach (($scriptsExtras ?? []) as $script): ?>
        <script src="<?= BASE_URL ?>/backoffice/assets/<?= $script ?>?v=<?= time() ?>"></script>
    <?php endforeach; ?>
</body>
</html>
