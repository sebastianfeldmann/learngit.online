<?php

namespace LearnGit\Controller;

use LearnGit\LessonReader;
use LearnGit\CourseReader;

class DataController
{
    private LessonReader $lessonReader;
    private CourseReader $courseReader;

    public function __construct(LessonReader $lessonReader, CourseReader $courseReader)
    {
        $this->lessonReader = $lessonReader;
        $this->courseReader = $courseReader;
    }

    public function __invoke(string $slug): void
    {
        $courseSlug = $_GET['course'] ?? null;
        $lessonData = $this->lessonReader->getLessonWithAllData($slug);

        if (!$lessonData) {
            $this->handle404();
        }        

        $lesson               = $lessonData['lesson'];
        $lesson['navigation'] = $this->setupNavigation($slug, $courseSlug);
        $lesson['related']    = $lessonData['related'];

        header('Content-Type: application/json');
        echo json_encode($lesson);
    }

    private function setupNavigation(string $slug, ?string $courseSlug): array
    {
        $navigation = ['previous' => null, 'next' => null];

        // no course no navigation
        if (!$courseSlug) {
            return $navigation;
        }

        $course = $this->courseReader->getCourseInfo($courseSlug);
        // no lessons no navigation      
        if (!$course || empty($course['lessons'])) {
            return $navigation;
        }

        $currentIndex = array_search($slug, $course['lessons']);
        // current lesson not found no navigation
        if ($currentIndex === false) {
            return $navigation;
        }
        
        // get previous lesson in course
        if ($currentIndex > 0) {
            $prevSlug = $course['lessons'][$currentIndex - 1];
            $navigation['previous'] = $this->getNavData($prevSlug);
        }

        // get next lesson in course
        if ($currentIndex < count($course['lessons']) - 1) {
            $nextSlug = $course['lessons'][$currentIndex + 1];
            $navigation['next'] = $this->getNavData($nextSlug);
        }

        return $navigation;
    }

    private function getNavData(string $slug): ?array
    {
        $data = $this->lessonReader->getLesson($slug);
        return $data ? [
                         'title' => $data['title'],
                         'slug'  => $slug
                       ] 
                     : null;
    }

    private function handle404(): void
    {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Lesson not found']);
    }
}
