<div class="col">
    <div class="lesson-card">
        <h3><a href="/lesson/<?= htmlspecialchars($lesson['slug']) ?>"><?= htmlspecialchars($lesson['title']) ?></a></h3>
        <div class="lesson-meta">
            <span class="lesson-level level-<?= htmlspecialchars($lesson['level'] ?? 'beginner') ?>"><?= htmlspecialchars(ucfirst($lesson['level'] ?? 'beginner')) ?></span>
            <span class="lesson-time"><?= htmlspecialchars($lesson['time'] ?? 'N/A') ?></span>
        </div>
        <p class="lesson-description"><?= htmlspecialchars($lesson['description']) ?></p>
        <a href="/lesson/<?= htmlspecialchars($lesson['slug']) ?>" class="btn btn-outline-primary">Start Lesson</a>
    </div>
</div>