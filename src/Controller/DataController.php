<?php

namespace LearnGit\Controller;

use LearnGit\LessonReader;

class DataController
{
    private LessonReader $lessonReader;

    public function __construct(LessonReader $lessonReader)
    {
        $this->lessonReader = $lessonReader;
    }

    public function __invoke(string $slug): void
    {
        $lessonData = $this->lessonReader->getLessonWithAllData($slug);

        if (!$lessonData) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Lesson not found']);
            return;
        }

        $lesson = $lessonData['lesson'];

        // Add navigation data
        $lesson['navigation'] = [
            'previous' => $lessonData['previous'],
            'next'     => $lessonData['next']
        ];

        // Add related lessons data
        $lesson['related'] = $lessonData['related'];

        header('Content-Type: application/json');
        echo json_encode($lesson);
    }
}
