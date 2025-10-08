<?php
/**
 * @var array $course
 * @var array $lessons
 */

$html['title']       = 'Learn Git | ' . htmlspecialchars($course['title']);
$html['description'] = htmlspecialchars($course['description']);
$html['url']         = 'https://learngit.online/course/' . htmlspecialchars($course['slug']);
$html['image']       = 'https://example.com/image.jpg';

$breadcrumb = [
    [
        'url'  => '/learning',
        'name' => 'Learning'
    ],
    [
        'url'  => '/course/' . $course['slug'],
        'name' => $course['title']
    ]
];
?>
<?php include TPL_DIR . 'layout/head.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/header.tpl.php'; ?>

    <?php include TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <main class="container my-5">

        <!-- Course Header -->
        <div class="course-header mb-5 text-center">
            <h1 class="highlighted mb-3">🎓 <?= htmlspecialchars($course['title']) ?></h1>
            <div class="course-meta mb-3">
                <span class="lesson-level level-<?= htmlspecialchars($course['level'] ?? 'beginner') ?>"><?= htmlspecialchars(ucfirst($course['level'] ?? 'beginner')) ?></span>
                <span class="lesson-time"><?= htmlspecialchars($course['time'] ?? 'N/A') ?></span>
            </div>
            <p class="lead" style="max-width: 800px; margin: 0 auto;">
                <?= htmlspecialchars($course['description']) ?>
            </p>
        </div>

        <!-- Lessons List -->
        <?php if (!empty($lessons)): ?>
            <div class="lessons-section">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Lesson</th>
                                <th style="width: 120px;">Level</th>
                                <th style="width: 120px;">Time</th>
                                <th style="width: 120px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $lessonNumber = 1;
                            foreach ($lessons as $lesson): 
                            ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $lessonNumber++ ?></td>
                                    <td>
                                        <a href="/course/<?= htmlspecialchars($course['slug']) ?>/<?= htmlspecialchars($lesson['slug']) ?>" class="text-decoration-none">
                                            <strong><?= htmlspecialchars($lesson['title']) ?></strong>
                                        </a>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($lesson['description']) ?></small>
                                    </td>
                                    <td>
                                        <span class="lesson-level level-<?= htmlspecialchars($lesson['level'] ?? 'beginner') ?>"><?= htmlspecialchars(ucfirst($lesson['level'] ?? 'beginner')) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($lesson['time'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="/course/<?= htmlspecialchars($course['slug']) ?>/<?= htmlspecialchars($lesson['slug']) ?>" class="btn btn-sm btn-outline-primary">Start</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <p>This course is under development. Lessons will be added soon!</p>
            </div>
        <?php endif; ?>

    </main>

    <?php include TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
