<?php

namespace LearnGit\Controller;

use LearnGit\LessonReader;

class LessonsController
{
    private LessonReader $lessonReader;

    public function __construct(LessonReader $lessonReader)
    {
        $this->lessonReader = $lessonReader;
    }

    public function __invoke(): void
    {
        $categorizedLessons = $this->lessonReader->getAllLessonsByCategory();
        require TPL_DIR . 'lessons.tpl.php';
    }
}
