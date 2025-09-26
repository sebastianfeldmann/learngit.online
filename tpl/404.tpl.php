<?php
/**
 * @var array  $categorizedLessons
 * @var string $tagline
 */

$html['title']       = 'Learn Git';
$html['description'] = 'Learn Git';
$html['url']         = 'https://lerngit.online/';
$html['image']       = 'https://example.com/image.jpg';
?>
<?php include self::TPL_DIR . 'layout/head.tpl.php'; ?>

  <?php include self::TPL_DIR . 'layout/header.tpl.php'; ?>

    <main class="container e404 text-center">
      <h1>Error 404</h1>
      <h2>PAGE detached</h2>
      <p>
        You are in 'detached PAGE' state.
        You can look around, you can't make any changes anyway,
        do yourself a favour and switch back to an actual page.
      </p>
    </main>

  <?php include self::TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
  <?php include self::TPL_DIR . 'layout/footer.tpl.php'; ?>

  <script src="/assets/js/theme-switch.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

