<?php

namespace LearnGit;

use LearnGit\Reader\Factory;

class App
{
    private const string TPL_DIR = __DIR__ . '/../tpl/';
    private Router $router;
    private LessonReader $lessonReader;
    private CategoryReader $categoryReader;

    public function __construct(Request $request)
    {
        $this->router         = new Router($request);
        $this->categoryReader = Factory::createCategoryReader();
        $this->lessonReader   = Factory::createLessonReader(null, $this->categoryReader);
    }

    public function run(): void
    {
        $this->router->add404(\Closure::fromCallable([$this, 'handle404']));
        $this->router->addRoute('GET', '/', \Closure::fromCallable([$this, 'handleIndex']));
        $this->router->addRoute('GET', '/lessons', \Closure::fromCallable([$this, 'handleLessons']));
        $this->router->addRoute('GET', '/categories', \Closure::fromCallable([$this, 'handleCategories']));
        $this->router->addRoute('GET', '/lesson/([a-z\-_]+)', \Closure::fromCallable([$this, 'handleLesson']));
        $this->router->addRoute('GET', '/category/([a-z\-_]+)', \Closure::fromCallable([$this, 'handleCategory']));
        $this->router->addRoute('GET', '/data/([a-z\-_]+)', \Closure::fromCallable([$this, 'handleData']));
        $this->router->route();
    }

    private function handleIndex(): void
    {
        // Load taglines config and pick a random one
        $taglines = require __DIR__ . '/../config/taglines.php';
        $tagline = $taglines[array_rand($taglines)];
        //$tagline = 'Mastering <em>Git</em>';
        
        require self::TPL_DIR . 'index.tpl.php';
    }

    private function handleLessons(): void
    {
        $categorizedLessons = $this->lessonReader->getAllLessonsByCategory();
        require self::TPL_DIR . 'lessons.tpl.php';
    }

    private function handleCategories(): void
    {
        $categories = $this->categoryReader->getCategories();
        require self::TPL_DIR . 'categories.tpl.php';
    }

    private function handleLesson(string $slug): void
    {
        $lesson = $this->lessonReader->getLesson($slug);

        if (!$lesson) {
            $this->handle404();
            return;
        }

        $lesson['slug'] = $slug;

        // Get category info for breadcrumbs
        $categoryInfo = null;
        if (isset($lesson['category'])) {
            $categoryInfo = $this->categoryReader->getCategoryInfo($lesson['category']);
        }

        // Get navigation data
        $previousLesson = $this->lessonReader->getPreviousLesson($slug);
        $nextLesson = $this->lessonReader->getNextLesson($slug);

        require self::TPL_DIR . 'lesson.tpl.php';
    }

    private function handleCategory(string $categorySlug): void
    {
        $categoryData = $this->lessonReader->getLessonsByCategory($categorySlug);

        if (!$categoryData) {
            $this->handle404();
            return;
        }

        // Verify category exists
        if (!$this->categoryReader->categoryExists($categorySlug)) {
            $this->handle404();
            return;
        }

        $category = $categoryData['info'];
        $lessons = $categoryData['lessons'];

        require self::TPL_DIR . 'category.tpl.php';
    }

    private function handleData(string $slug): void
    {
        $lesson = $this->lessonReader->getLesson($slug);

        if (!$lesson) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Lesson not found']);
            return;
        }

        // Add navigation data
        $lesson['navigation'] = [
            'previous' => $this->lessonReader->getPreviousLesson($slug),
            'next' => $this->lessonReader->getNextLesson($slug)
        ];

        header('Content-Type: application/json');
        echo json_encode($lesson);
    }

    private function handle404(): void
    {
        http_response_code(404);
        require self::TPL_DIR . '404.tpl.php';
    }
}
