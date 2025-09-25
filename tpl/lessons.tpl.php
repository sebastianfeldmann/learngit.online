<?php
/**
 * @var array $categorizedLessons
 */

$html['title']       = 'Learn Git | All Lessons';
$html['description'] = 'Learn Git';
$html['url']         = 'https://learngit.online/lessons';
$html['image']       = 'https://example.com/image.jpg';

$breadcrumb = [
    [
        'url'  => '/lessons',
        'name' => 'All Lessons'
    ]
];
?>
<?php include self::TPL_DIR . 'layout/head.tpl.php'; ?>

    <?php include self::TPL_DIR . 'layout/header.tpl.php'; ?>

    <?php include self::TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <main class="container my-5">

        <?php if (empty($categorizedLessons)): ?>
            <p>No lessons available yet.</p>
        <?php else: ?>
            <?php foreach ($categorizedLessons as $categorySlug => $categoryData): ?>
                <div class="category-section mb-5">
                    <div class="category-header">
                        <h2>
                            <a href="/category/<?= htmlspecialchars($categorySlug) ?>">
                                <?= htmlspecialchars($categoryData['info']['icon'] ?? '') ?>
                                <?= htmlspecialchars($categoryData['info']['displayName']) ?>
                            </a>
                        </h2>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3">

                        <?php foreach ($categoryData['lessons'] as $lesson): ?>
                        <?php include self::TPL_DIR . 'lesson/list-item.tpl.php'; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </main>

    <?php include self::TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include self::TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
