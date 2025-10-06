<?php

namespace LearnGit\Controller;

use LearnGit\CategoryReader;
use LearnGit\CourseReader;
use LearnGit\LessonReader;

class CourseLessonController
{
    private CourseReader $courseReader;
    private LessonReader $lessonReader;
    private CategoryReader $categoryReader;

    public function __construct(CourseReader $courseReader, LessonReader $lessonReader, CategoryReader $categoryReader)
    {
        $this->courseReader = $courseReader;
        $this->lessonReader = $lessonReader;
        $this->categoryReader = $categoryReader;
    }

    public function __invoke(string $courseSlug, string $lessonSlug): void
    {
        // Get course info
        $course = $this->courseReader->getCourseInfo($courseSlug);
        if (!$course) {
            NotFoundController::render404();
            return;
        }

        // Get lesson data
        $lessonData = $this->lessonReader->getLessonWithAllData($lessonSlug);
        if (!$lessonData) {
            NotFoundController::render404();
            return;
        }

        $lesson = $lessonData['lesson'];
        $lesson['slug'] = $lessonSlug;
        $lesson['related'] = $lessonData['related'];

        // Calculate previous/next within the course context
        $previousLesson = null;
        $nextLesson = null;

        if (!empty($course['lessons'])) {
            $currentIndex = array_search($lessonSlug, $course['lessons']);
            
            if ($currentIndex !== false) {
                // Get previous lesson in course
                if ($currentIndex > 0) {
                    $prevSlug = $course['lessons'][$currentIndex - 1];
                    $prevData = $this->lessonReader->getLesson($prevSlug);
                    if ($prevData) {
                        $previousLesson = $prevData;
                        $previousLesson['slug'] = $prevSlug;
                    }
                }

                // Get next lesson in course
                if ($currentIndex < count($course['lessons']) - 1) {
                    $nextSlug = $course['lessons'][$currentIndex + 1];
                    $nextData = $this->lessonReader->getLesson($nextSlug);
                    if ($nextData) {
                        $nextLesson = $nextData;
                        $nextLesson['slug'] = $nextSlug;
                    }
                }
            }
        }

        // Get category info for breadcrumbs (fallback if needed)
        $categoryInfo = null;
        if (isset($lesson['category'])) {
            $categoryInfo = $this->categoryReader->getCategoryInfo($lesson['category']);
        }

        // Use course context for navigation
        $courseContext = [
            'slug' => $courseSlug,
            'title' => $course['title']
        ];

        require TPL_DIR . 'lesson.tpl.php';
    }
}
