<?php

namespace LearnGit;

interface CourseReader
{
    /**
     * Get all courses
     */
    public function getCourses(): array;

    /**
     * Get course information
     */
    public function getCourseInfo(string $courseSlug): ?array;

    /**
     * Check if a course exists
     */
    public function courseExists(string $courseSlug): bool;
}
