<?php

namespace LearnGit;

interface CategoryReader
{
    /**
     * Get all categories
     */
    public function getCategories(): array;

    /**
     * Get category information
     */
    public function getCategoryInfo(string $categorySlug): ?array;

    /**
     * Check if a category exists
     */
    public function categoryExists(string $categorySlug): bool;
}
