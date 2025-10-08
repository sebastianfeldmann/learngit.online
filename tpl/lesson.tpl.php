<?php
/**
 * @var array $lesson
 * @var array $categoryInfo
 * @var array $previousLesson
 * @var array $nextLesson
 * @var array $courseContext (optional - set when viewing lesson in course context)
 */

 // Determine if we're in a course context
 $inCourseContext = isset($courseContext) && !empty($courseContext);
 
 $html['title']       = 'Learn Git | Lesson ' . htmlspecialchars($lesson['title']);
 $html['description'] = 'Learn Git';
 $html['url']         = $inCourseContext 
     ? 'https://learngit.online/course/' . htmlspecialchars($courseContext['slug']) . '/' . htmlspecialchars($lesson['slug'])
     : 'https://learngit.online/lesson/' . htmlspecialchars($lesson['slug']);
 $html['image']       = 'https://example.com/image.jpg';
 
 // Build breadcrumb based on context
 if ($inCourseContext) {
     $breadcrumb = [
         [
             'url'  => '/learning',
             'name' => 'Learning',
         ],
         [
             'url'  => '/course/' . $courseContext['slug'],
             'name' => $courseContext['title'],
         ],
         [
            'url'  => '/course/' . $courseContext['slug'] . '/' . $lesson['slug'],
            'name' => $lesson['title'],
        ]
     ];
 } else {
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
 }
 
 // Build navigation URLs based on context
 $prevUrl = $previousLesson ? ($inCourseContext 
     ? '/course/' . htmlspecialchars($courseContext['slug']) . '/' . htmlspecialchars($previousLesson['slug'])
     : '/lesson/' . htmlspecialchars($previousLesson['slug'])) : null;
 
 $nextUrl = $nextLesson ? ($inCourseContext 
     ? '/course/' . htmlspecialchars($courseContext['slug']) . '/' . htmlspecialchars($nextLesson['slug'])
     : '/lesson/' . htmlspecialchars($nextLesson['slug'])) : null;
 ?>
 <?php include TPL_DIR . 'layout/head.tpl.php'; ?>
 
    <?php include TPL_DIR . 'layout/header.tpl.php'; ?>
 
    <?php include TPL_DIR . 'layout/breadcrumb.tpl.php'; ?>

    <div class="container position-relative text-center mb-5 mt-4">
        <?php if ($prevUrl): ?>
        <a href="<?= $prevUrl ?>" class="nav-arrow nav-arrow-left" title="Previous: <?= htmlspecialchars($previousLesson['title']) ?>">
            <span class="arrow">❮</span>
        </a>
        <?php endif; ?>
        <?php if ($nextUrl): ?>
        <a href="<?= $nextUrl ?>" class="nav-arrow nav-arrow-right" title="Next: <?= htmlspecialchars($nextLesson['title']) ?>">
            <span class="arrow">❯</span>
        </a>
        <?php endif; ?>
        <div class="lesson-header">
            <h1 class="highlighted"><?= htmlspecialchars($lesson['title']) ?></h1>
            <div class="lesson-description" style="width:80%; margin:0 auto;">
                <p><?= htmlspecialchars($lesson['description']) ?></p>                
            </div>
            <?php if (isset($lesson['__story']) && $lesson['story']): ?>
                <div class="text-start text-muted">
                    <?= $lesson['story'] ?>
                </div>
            <?php endif; ?>
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

                <?php if (isset($lesson['related']) && !empty($lesson['related'])): ?>
                <div class="related-section">
                    <h3>Related Lessons</h3>
                    <ul class="related-lessons" id="relatedLessons">
                        <?php foreach ($lesson['related'] as $relatedLesson): ?>
                        <li class="text-muted">
                            <a href="/lesson/<?= htmlspecialchars($relatedLesson['slug']) ?>"><?= htmlspecialchars($relatedLesson['title']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="terminal-section">
                <div class="terminal" id="terminal">
                    <div class="terminal-header">
                        <div class="terminal-controls">
                            <span class="control control-close"></span>
                            <span class="control control-minimize"></span>
                            <span class="control control-maximize"></span>
                        </div>
                        <div class="terminal-title">Terminal</div>
                    </div>
                    <div class="terminal-body" id="terminalBody">
                        <div class="terminal-editor" id="terminalEditor" style="display: none;">
                            <div class="editor-content" id="editorContent"></div>
                        </div>
                        <div class="terminal-output" id="terminalOutput">
                            <div class="terminal-line">
                                <span class="prompt">$ </span>
                                <span>Type the commands shown in the sidebar to proceed.</span>
                            </div>
                        </div>
                    </div>
                    <div class="terminal-input-line">
                        <span class="prompt">$ </span>
                        <input type="text" class="terminal-input" id="terminalInput" placeholder="Type your command here..." autocomplete="off">
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

    <?php include TPL_DIR . 'layout/switch-theme.tpl.php'; ?>
    <?php include TPL_DIR . 'layout/footer.tpl.php'; ?>

    <script>
        // Pass lesson slug to JavaScript
        window.LESSON_SLUG = '<?= $lesson['slug'] ?? '' ?>';
        // Pass course context to JavaScript if in course context
        <?php if ($inCourseContext): ?>
        window.COURSE_SLUG = '<?= htmlspecialchars($courseContext['slug']) ?>';
        <?php endif; ?>
    </script>
    <script src="/assets/js/terminal.js"></script>

    <script src="/assets/js/theme-switch.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

