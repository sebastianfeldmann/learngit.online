<?php

namespace LearnGit\Controller;

use LearnGit\CourseReader;
use LearnGit\LessonReader;

class LearningController
{
    private CourseReader $courseReader;
    private LessonReader $lessonReader;

    public function __construct(CourseReader $courseReader, LessonReader $lessonReader)
    {
        $this->courseReader = $courseReader;
        $this->lessonReader = $lessonReader;
    }

    public function __invoke(): void
    {
        $courses            = $this->courseReader->getCourses();
        $categorizedLessons = $this->lessonReader->getAllLessonsByCategory();
        require TPL_DIR . 'learning.tpl.php';
    }
}
