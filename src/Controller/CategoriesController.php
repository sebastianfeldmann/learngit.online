<?php

namespace LearnGit\Controller;

use LearnGit\CategoryReader;

class CategoriesController
{
    private CategoryReader $categoryReader;

    public function __construct(CategoryReader $categoryReader)
    {
        $this->categoryReader = $categoryReader;
    }

    public function __invoke(): void
    {
        $categories = $this->categoryReader->getCategories();
        require TPL_DIR . 'categories.tpl.php';
    }
}
