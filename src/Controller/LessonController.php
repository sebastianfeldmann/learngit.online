<?php

namespace LearnGit\Controller;

use LearnGit\CategoryReader;
use LearnGit\LessonReader;

class LessonController
{
    private LessonReader $lessonReader;
    private CategoryReader $categoryReader;

    public function __construct(LessonReader $lessonReader, CategoryReader $categoryReader)
    {
        $this->lessonReader = $lessonReader;
        $this->categoryReader = $categoryReader;
    }

    public function __invoke(string $slug): void
    {
        $lessonData = $this->lessonReader->getLessonWithAllData($slug);

        if (!$lessonData) {
            NotFoundController::render404();
            return;
        }

        $lesson            = $lessonData['lesson'];
        $lesson['slug']    = $slug;
        $previousLesson    = $lessonData['previous'];
        $nextLesson        = $lessonData['next'];
        $lesson['related'] = $lessonData['related'];

        // Get category info for breadcrumbs
        $categoryInfo = null;
        if (isset($lesson['category'])) {
            $categoryInfo = $this->categoryReader->getCategoryInfo($lesson['category']);
        }       

        require TPL_DIR . 'lesson.tpl.php';
    }
}
