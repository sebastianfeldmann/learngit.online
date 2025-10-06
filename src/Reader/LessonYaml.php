<?php

namespace LearnGit\Reader;

use LearnGit\CategoryReader;
use LearnGit\LessonReader;
use Symfony\Component\Yaml\Yaml;

class LessonYaml implements LessonReader
{
    private string $lessonsPath;
    private CategoryReader $categoryReader;

    public function __construct(?string $lessonsPath = null, ?CategoryReader $categoryReader = null)
    {
        $this->lessonsPath    = $lessonsPath ?? __DIR__ . '/../../../learngit.online.playbook/data/lessons';
        $this->categoryReader = $categoryReader ?? new CategoryYaml();
    }

    /**
     * Get all available lessons
     */
    public function getAllLessons(): array
    {
        $lessons = [];
        $files = glob($this->lessonsPath . '/*.yaml');

        foreach ($files as $file) {
            $slug = basename($file, '.yaml');
            $lesson = $this->getLesson($slug);
            if ($lesson && isset($lesson['category'])) {
                $lessons[$slug] = $lesson;
            }
        }

        return $lessons;
    }

    /**
     * Get a specific lesson by slug
     */
    public function getLesson(string $slug): ?array
    {
        $filePath = $this->lessonsPath . '/' . $slug . '.yaml';

        if (!file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $data = Yaml::parse($content);

        if ($data === null) {
            return null;
        }

        // Only return lessons with categories
        return ($data && isset($data['category'])) ? $data : null;
    }

    /**
     * Check if a lesson exists
     */
    public function lessonExists(string $slug): bool
    {
        return file_exists($this->lessonsPath . '/' . $slug . '.yaml');
    }

    /**
     * Get all lessons grouped by category
     */
    public function getAllLessonsByCategory(): array
    {
        $lessons = $this->getAllLessons();
        $categories = $this->categoryReader->getCategories();
        $grouped = [];

        // Group lessons by category
        foreach ($lessons as $slug => $lesson) {
            $categorySlug = $lesson['category'];
            if (!isset($grouped[$categorySlug])) {
                $grouped[$categorySlug] = [
                    'info' => $categories[$categorySlug] ?? ['displayName' => ucfirst($categorySlug), 'order' => 999],
                    'lessons' => []
                ];
            }
            $lesson['slug'] = $slug;
            $grouped[$categorySlug]['lessons'][] = $lesson;
        }

        // Sort categories by order
        uasort($grouped, fn($a, $b) => ($a['info']['order'] ?? 999) - ($b['info']['order'] ?? 999));

        // Sort lessons within each category by order, then by title
        foreach ($grouped as &$category) {
            usort($category['lessons'], function($a, $b) {
                $orderA = $a['order'] ?? 999;
                $orderB = $b['order'] ?? 999;
                if ($orderA === $orderB) {
                    return strcmp($a['title'], $b['title']);
                }
                return $orderA - $orderB;
            });
        }

        return $grouped;
    }

    /**
     * Get lessons for a specific category
     */
    public function getLessonsByCategory(string $categorySlug): ?array
    {
        $allByCategory = $this->getAllLessonsByCategory();
        return $allByCategory[$categorySlug] ?? null;
    }

    /**
     * Get previous lesson data
     */
    public function getPreviousLesson(string $currentSlug): ?array
    {
        $lesson = $this->getLesson($currentSlug);
        if (!$lesson || !isset($lesson['previous']) || $lesson['previous'] === null) {
            return null;
        }

        $previousLesson = $this->getLesson($lesson['previous']);
        if (!$previousLesson) {
            return null;
        }

        return [
            'title' => $previousLesson['title'],
            'slug' => $lesson['previous']
        ];
    }

    /**
     * Get next lesson data
     */
    public function getNextLesson(string $currentSlug): ?array
    {
        $lesson = $this->getLesson($currentSlug);
        if (!$lesson || !isset($lesson['next']) || $lesson['next'] === null) {
            return null;
        }

        $nextLesson = $this->getLesson($lesson['next']);
        if (!$nextLesson) {
            return null;
        }

        return [
            'title' => $nextLesson['title'],
            'slug' => $lesson['next']
        ];
    }

    /**
     * Get related lessons data
     */
    public function getRelatedLessons(string $currentSlug): array
    {
        $lesson = $this->getLesson($currentSlug);
        if (!$lesson || !isset($lesson['related']) || !is_array($lesson['related'])) {
            return [];
        }

        $relatedLessons = [];
        foreach ($lesson['related'] as $relatedSlug) {
            $relatedLesson = $this->getLesson($relatedSlug);
            if ($relatedLesson) {
                $relatedLessons[] = [
                    'title' => $relatedLesson['title'],
                    'slug' => $relatedSlug
                ];
            }
        }

        return $relatedLessons;
    }

    /**
     * Get lesson with all navigation data in a single efficient call
     * Reads the current lesson once and only fetches additional lessons when needed
     */
    public function getLessonWithAllData(string $slug): ?array
    {
        $lesson = $this->getLesson($slug);
        if (!$lesson) {
            return null;
        }

        $result = [
            'lesson' => $lesson,
            'previous' => null,
            'next' => null,
            'related' => []
        ];

        // Only read previous lesson if it exists
        if (isset($lesson['previous']) && $lesson['previous'] !== null) {
            $previousLesson = $this->getLesson($lesson['previous']);
            if ($previousLesson) {
                $result['previous'] = [
                    'title' => $previousLesson['title'],
                    'slug' => $lesson['previous']
                ];
            }
        }

        // Only read next lesson if it exists
        if (isset($lesson['next']) && $lesson['next'] !== null) {
            $nextLesson = $this->getLesson($lesson['next']);
            if ($nextLesson) {
                $result['next'] = [
                    'title' => $nextLesson['title'],
                    'slug' => $lesson['next']
                ];
            }
        }

        // Only read related lessons if they exist
        if (isset($lesson['related']) && is_array($lesson['related'])) {
            foreach ($lesson['related'] as $relatedSlug) {
                $relatedLesson = $this->getLesson($relatedSlug);
                if ($relatedLesson) {
                    $result['related'][] = [
                        'title' => $relatedLesson['title'],
                        'slug' => $relatedSlug
                    ];
                }
            }
        }

        return $result;
    }
}
