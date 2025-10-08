<div class="container my-2">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="/">
                    <svg width="14" height="14" aria-hidden="true"><use xlink:href="#house-door-fill"></use></svg>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <?php foreach ($breadcrumb as $index => $item): ?>
            <?php if ($index < count($breadcrumb) - 1): ?>
            <li class="breadcrumb-item">
                <a class="link-body-emphasis fw-semibold text-decoration-none" href="<?=$item['url'] ?>"><?=htmlspecialchars($item['name']) ?></a>
            </li>
            <?php else: ?>
            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($item['name']) ?>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
            
        </ol>
    </nav>
</div>