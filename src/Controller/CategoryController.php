<?php

namespace LearnGit\Controller;

use LearnGit\CategoryReader;
use LearnGit\LessonReader;

class CategoryController
{
    private LessonReader $lessonReader;
    private CategoryReader $categoryReader;

    public function __construct(LessonReader $lessonReader, CategoryReader $categoryReader)
    {
        $this->lessonReader = $lessonReader;
        $this->categoryReader = $categoryReader;
    }

    public function __invoke(string $categorySlug): void
    {
        $categoryData = $this->lessonReader->getLessonsByCategory($categorySlug);

        if (!$categoryData) {
            NotFoundController::render404();
            return;
        }

        // Verify category exists
        if (!$this->categoryReader->categoryExists($categorySlug)) {
            $this->handle404();
            return;
        }

        $category = $categoryData['info'];
        $lessons = $categoryData['lessons'];

        require TPL_DIR . 'category.tpl.php';
    }
}
