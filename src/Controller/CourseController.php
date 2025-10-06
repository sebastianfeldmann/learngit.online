<?php

namespace LearnGit\Controller;

use LearnGit\CourseReader;
use LearnGit\LessonReader;

class CourseController
{
    private CourseReader $courseReader;
    private LessonReader $lessonReader;

    public function __construct(CourseReader $courseReader, LessonReader $lessonReader)
    {
        $this->courseReader = $courseReader;
        $this->lessonReader = $lessonReader;
    }

    public function __invoke(string $courseSlug): void
    {
        $course = $this->courseReader->getCourseInfo($courseSlug);

        if (!$course) {
            NotFoundController::render404();
            return;
        }

        // Get lesson details for each lesson in the course
        $lessons = [];
        if (!empty($course['lessons'])) {
            foreach ($course['lessons'] as $lessonSlug) {
                $lessonData = $this->lessonReader->getLesson($lessonSlug);
                if ($lessonData) {
                    $lessonData['slug'] = $lessonSlug;
                    $lessons[] = $lessonData;
                }
            }
        }

        require TPL_DIR . 'course.tpl.php';
    }
}
