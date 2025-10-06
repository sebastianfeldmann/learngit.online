<?php
/**
 * @var array $category
 * @var array $lessons
 */

$html['title']       = 'Learn Git | ' . $category['displayName'];
$html['description'] = 'Learn Git';
$html['url']         = 'http://learngit.online/category/' . $categorySlug;
$html['image']       = 'https://example.com/image.jpg';

$breadcrumb = [
    [
        'url'  => '/learning',
        'name' => 'Learning'
    ],
    [
        'url'  => '/categories',
        'name' => 'Categories'
    ],
    [
        'url'  => '/category/' . $categorySlug,
        'name' => $category['displayName']
    ]
];
?>
<?php include TPL_DIR . 'layout/head.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/header.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <div class="container">
        <h1><?= htmlspecialchars($category['icon'] ?? '') ?> <?= htmlspecialchars($category['displayName']) ?></h1>
        <p><?= htmlspecialchars($category['description']) ?></p>
    </div>

    <main class="container my-5">
        <?php if (empty($lessons)): ?>
        <div class="no-lessons">
            <p>No lessons found in this category.</p>
            <a href="/" class="btn">← Back to All Categories</a>
        </div>

        <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3">      

            <?php foreach ($lessons as $lesson): ?>            
            <?php include TPL_DIR . 'lesson/list-item.tpl.php'; ?>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>
       
    </main>

    <?php include TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
