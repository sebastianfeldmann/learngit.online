<?php
/**
 * @var array $courses
 * @var array $categorizedLessons
 */

$html['title']       = 'Learn Git | Start Learning';
$html['description'] = 'Learn Git with interactive courses and lessons';
$html['url']         = 'https://learngit.online/learning';
$html['image']       = 'https://example.com/image.jpg';

$breadcrumb = [
    [
        'url'  => '/learning',
        'name' => 'Learning'
    ]
];
?>
<?php include TPL_DIR . 'layout/head.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/header.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <main class="container my-5">

        <!-- Courses Section -->
        <?php if (!empty($courses)): ?>
            <div class="courses-section mb-5">
                <h3 class="mb-4">🎓 Courses</h3>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3">
                    <?php foreach ($courses as $course): ?>
                        <div class="col">
                            <div class="lesson-card course-card">
                                <h3><?= htmlspecialchars($course['title']) ?></h3>
                                <div class="lesson-meta">
                                    <span class="lesson-level level-<?= htmlspecialchars($course['level'] ?? 'beginner') ?>"><?= htmlspecialchars(ucfirst($course['level'] ?? 'beginner')) ?></span>
                                    <span class="lesson-time"><?= htmlspecialchars($course['time'] ?? 'N/A') ?></span>
                                </div>
                                <p class="lesson-description"><?= htmlspecialchars($course['description']) ?></p>
                                <?php if (!empty($course['lessons']) && count($course['lessons']) > 0): ?>
                                    <a href="/course/<?= htmlspecialchars($course['slug']) ?>" class="btn btn-outline-primary">Start Course</a>
                                <?php else: ?>
                                    <span class="btn btn-outline-secondary disabled">Coming Soon</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lessons by Category Section -->
        <?php if (!empty($categorizedLessons)): ?>
            <div class="lessons-section">
                <?php foreach ($categorizedLessons as $categorySlug => $categoryData): ?>
                    <div class="category-section mb-5">
                        <div class="category-header">
                            <h3>
                                <a href="/category/<?= htmlspecialchars($categorySlug) ?>">
                                    <?= htmlspecialchars($categoryData['info']['icon'] ?? '') ?>
                                    <?= htmlspecialchars($categoryData['info']['displayName']) ?>
                                </a>
                            </h3>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3">

                            <?php foreach ($categoryData['lessons'] as $lesson): ?>
                            <?php include TPL_DIR . 'lesson/list-item.tpl.php'; ?>
                            <?php endforeach; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No lessons available yet.</p>
        <?php endif; ?>

    </main>

    <?php include TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
