<?php
/**
 * @var array $lesson
 * @var array $categoryInfo
 * @var array $previousLesson
 * @var array $nextLesson
 */

 $html['title']       = 'Learn Git | Lesson ' . htmlspecialchars($lesson['title']);
 $html['description'] = 'Learn Git';
 $html['url']         = 'https://learngit.online/lesson/' . htmlspecialchars($lesson['slug']);
 $html['image']       = 'https://example.com/image.jpg';
 
 $breadcrumb = [
     [
         'url'  => '/category/' . htmlspecialchars($lesson['category']),
         'name' => htmlspecialchars($categoryInfo['displayName']),
     ],
     [
        'url'  => '/lesson/' . htmlspecialchars($lesson['slug']),
        'name' => htmlspecialchars($lesson['title']),
    ]
 ];
 ?>
 <?php include self::TPL_DIR . 'layout/head.tpl.php'; ?>
 
     <?php include self::TPL_DIR . 'layout/header.tpl.php'; ?>
 
     <?php include self::TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <div class="container position-relative text-center mb-5 mt-4">
        <?php if ($previousLesson): ?>
        <a href="/lesson/<?= htmlspecialchars($previousLesson['slug']) ?>" class="nav-arrow nav-arrow-left" title="Previous: <?= htmlspecialchars($previousLesson['title']) ?>">
            <span class="arrow">❮</span>
        </a>
        <?php endif; ?>
        <?php if ($nextLesson): ?>
        <a href="/lesson/<?= htmlspecialchars($nextLesson['slug']) ?>" class="nav-arrow nav-arrow-right" title="Next: <?= htmlspecialchars($nextLesson['title']) ?>">
            <span class="arrow">❯</span>
        </a>
        <?php endif; ?>
        <div class="lesson-header">
            <h1 class="highlighted"><?= htmlspecialchars($lesson['title']) ?></h1>
            <p><?= htmlspecialchars($lesson['description']) ?></p>
        </div>
    </div>

    <main class="container my-5">
        <div class="lesson-container">
            <div class="lesson-sidebar">
                <div class="lesson-meta-header">
                  <span class="lesson-level level-<?= htmlspecialchars($lesson['level'] ?? 'beginner') ?>"><?= htmlspecialchars(ucfirst($lesson['level'] ?? 'beginner')) ?></span>
                  <span class="lesson-time"><?= htmlspecialchars($lesson['time'] ?? 'N/A') ?></span>
                </div>
                <div class="progress-section">
                    <h3>Progress</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <span class="progress-text" id="progressText">Step 1 of <?= count($lesson['steps']) ?></span>
                </div>

                <div class="current-step-section">
                    <h3>Current Step</h3>
                    <div class="step-info" id="currentStepInfo">
                        <h4 id="stepTitle">Loading...</h4>
                        <p id="stepDescription">Loading...</p>
                    </div>
                </div>

                <div class="hints-section">
                    <h3>Allowed Commands</h3>
                    <ul class="allowed-commands" id="allowedCommands">
                        <!-- Commands will be populated by JavaScript -->
                    </ul>
                </div>
            </div>

            <div class="terminal-section">
                <div class="terminal" id="terminal">
                    <div class="terminal-header">
                        <div class="terminal-controls">
                            <span class="control control-close"></span>
                            <span class="control control-minimize"></span>
                            <span class="control control-maximize"></span>
                        </div>
                        <div class="terminal-title">Git Terminal</div>
                    </div>
                    <div class="terminal-body" id="terminalBody">
                        <div class="terminal-output" id="terminalOutput">
                            <div class="terminal-line">
                                <span class="prompt">$ </span>
                                <span>Type the commands shown in the sidebar to proceed.</span>
                            </div>
                        </div>
                    </div>
                    <div class="terminal-input-line">
                        <span class="prompt">$ </span>
                        <input type="text" class="terminal-input" id="terminalInput" placeholder="Type your git command here..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <div class="lesson-complete" id="lessonComplete" style="display: none;">
            <div class="complete-message">
                <h2>🎉 Lesson Complete!</h2>
                <p>Great job! You've successfully completed this lesson.</p>
                <div class="complete-actions">
                    <a href="/" class="btn btn-primary">Back to Lessons</a>
                    <button class="btn btn-secondary" id="restartLesson">Restart This Lesson</button>
                </div>
            </div>
        </div>
    </main>

    <?php include self::TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include self::TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script>
        // Pass lesson slug to JavaScript
        window.LESSON_SLUG = '<?= $lesson['slug'] ?? '' ?>';
    </script>
    <script src="/assets/js/terminal.js"></script>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

