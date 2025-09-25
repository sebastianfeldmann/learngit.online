<?php

namespace LearnGit\Reader;

use LearnGit\CategoryReader;
use Symfony\Component\Yaml\Yaml;

class CategoryYaml implements CategoryReader
{
    private string $categoriesPath;

    public function __construct(string $categoriesPath = null)
    {
        $this->categoriesPath = $categoriesPath ?? __DIR__ . '/../../../learngit.online.playbook/data/categories/categories.yaml';
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        if (!file_exists($this->categoriesPath)) {
            return [];
        }

        $content = file_get_contents($this->categoriesPath);
        $data = Yaml::parse($content);

        return $data ?: [];
    }

    /**
     * Get category information
     */
    public function getCategoryInfo(string $categorySlug): ?array
    {
        $categories = $this->getCategories();
        return $categories[$categorySlug] ?? null;
    }

    /**
     * Check if a category exists
     */
    public function categoryExists(string $categorySlug): bool
    {
        $categories = $this->getCategories();
        return isset($categories[$categorySlug]);
    }
}
