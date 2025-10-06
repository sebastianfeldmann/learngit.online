<?php

namespace LearnGit\Reader;

use LearnGit\CourseReader;
use Symfony\Component\Yaml\Yaml;

class CourseYaml implements CourseReader
{
    private string $coursesPath;

    public function __construct(string $coursesPath = null)
    {
        $this->coursesPath = $coursesPath ?? __DIR__ . '/../../../learngit.online.playbook/data/courses';
    }

    /**
     * Get all courses
     */
    public function getCourses(): array
    {
        if (!is_dir($this->coursesPath)) {
            return [];
        }

        $courses = [];
        $files = glob($this->coursesPath . '/*.yaml');

        foreach ($files as $file) {
            $slug = basename($file, '.yaml');
            $content = file_get_contents($file);
            $data = Yaml::parse($content);

            if ($data) {
                $data['slug'] = $slug;
                $courses[$slug] = $data;
            }
        }

        // Sort by order
        uasort($courses, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $courses;
    }

    /**
     * Get course information
     */
    public function getCourseInfo(string $courseSlug): ?array
    {
        $filePath = $this->coursesPath . '/' . $courseSlug . '.yaml';

        if (!file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $data = Yaml::parse($content);

        if ($data) {
            $data['slug'] = $courseSlug;
        }

        return $data;
    }

    /**
     * Check if a course exists
     */
    public function courseExists(string $courseSlug): bool
    {
        $filePath = $this->coursesPath . '/' . $courseSlug . '.yaml';
        return file_exists($filePath);
    }
}
