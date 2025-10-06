<?php

$html['title']       = 'Learn Git';
$html['description'] = 'Learn Git';
$html['url']         = 'https://lerngit.online/';
$html['image']       = 'https://example.com/image.jpg';

?>
<?php include TPL_DIR . 'layout/head.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/header.tpl.php'; ?>

    <main>
        <?php include TPL_DIR . 'home/teaser.tpl.php'; ?>

        <?php include TPL_DIR . 'home/symbols.tpl.php'; ?>

        <?php include TPL_DIR . 'home/how-it-works.tpl.php'; ?>

        <?php include TPL_DIR . 'home/contribute.tpl.php'; ?>

        <?php include TPL_DIR . 'home/thanks.tpl.php'; ?>

    </main>

    <?php include TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
