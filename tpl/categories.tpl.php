<?php
/**
 * @var array $categories
 */

$html['title']       = 'Learn Git | Categories';
$html['description'] = 'Browse all Git learning categories';
$html['url']         = 'https://learngit.online/categories';
$html['image']       = 'https://example.com/image.jpg';

$breadcrumb = [
    [
        'url'  => '/categories',
        'name' => 'Categories'
    ]
];
?>
<?php include self::TPL_DIR . 'layout/head.tpl.php'; ?>

    <?php include self::TPL_DIR . 'layout/header.tpl.php'; ?>

    <?php include self::TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <main class="container my-5">

        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Categories</h1>
                <p class="lead mb-5">Explore different areas of Git knowledge organized by topic and skill level.</p>
            </div>
        </div>

        <?php if (empty($categories)): ?>
            <div class="alert alert-info">
                <h4>No categories available</h4>
                <p>Categories will be available soon.</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($categories as $categorySlug => $category): ?>
                    <div class="col">
                        <div class="lesson-card">
                            <div class="d-flex align-items-center mb-3">
                                <span class="fs-1 me-3"><?= htmlspecialchars($category['icon'] ?? '📚') ?></span>
                                <h3 class="card-title mb-0">
                                    <a href="/category/<?= htmlspecialchars($categorySlug) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($category['displayName']) ?>
                                    </a>
                                </h3>
                            </div>
                            
                            <p class="card-text text-muted flex-grow-1">
                                <?= htmlspecialchars($category['description']) ?>
                            </p>
                            
                            <div class="mt-auto">
                                <a href="/category/<?= htmlspecialchars($categorySlug) ?>" class="btn btn-outline-primary">
                                    Explore Lessons
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <?php include self::TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include self::TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
