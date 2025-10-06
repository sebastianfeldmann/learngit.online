<?php

namespace LearnGit;

interface LessonReader
{
    /**
     * Get all available lessons
     */
    public function getAllLessons(): array;

    /**
     * Get a specific lesson by slug
     */
    public function getLesson(string $slug): ?array;

    /**
     * Check if a lesson exists
     */
    public function lessonExists(string $slug): bool;

    /**
     * Get all lessons grouped by category
     */
    public function getAllLessonsByCategory(): array;

    /**
     * Get lessons for a specific category
     */
    public function getLessonsByCategory(string $categorySlug): ?array;

    /**
     * Get previous lesson data
     */
    public function getPreviousLesson(string $currentSlug): ?array;

    /**
     * Get next lesson data
     */
    public function getNextLesson(string $currentSlug): ?array;

    /**
     * Get related lessons data
     */
    public function getRelatedLessons(string $currentSlug): array;
}

