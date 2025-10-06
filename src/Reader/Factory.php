<?php

namespace LearnGit\Reader;

use LearnGit\CategoryReader;
use LearnGit\CourseReader;
use LearnGit\LessonReader;

class Factory
{
    /**
     * Create a CategoryReader based on the configured format
     */
    public static function createCategoryReader(?string $categoriesPath = null): CategoryReader
    {        
        switch (LESSON_FORMAT) {
            case 'yaml':
                return new CategoryYaml($categoriesPath);
            case 'json':
            default:
                return new CategoryJson($categoriesPath);
        }
    }

    /**
     * Create a CourseReader based on the configured format
     */
    public static function createCourseReader(?string $coursesPath = null): CourseReader
    {
        switch (LESSON_FORMAT) {
            case 'yaml':
                return new CourseYaml($coursesPath);
            case 'json':
            default:
                // JSON course reader not implemented yet, return YAML as fallback
                return new CourseYaml($coursesPath);
        }
    }

    /**
     * Create a LessonReader based on the configured format
     */
    public static function createLessonReader(?string $lessonsPath = null, ?CategoryReader $categoryReader = null): LessonReader
    {       
        switch (LESSON_FORMAT) {
            case 'yaml':
                return new LessonYaml($lessonsPath, $categoryReader);
            case 'json':
            default:
                return new LessonJson($lessonsPath, $categoryReader);
        }
    }

    /**
     * Get the current format
     */
    public static function getFormat(): string
    {
        return LESSON_FORMAT;
    }
}
