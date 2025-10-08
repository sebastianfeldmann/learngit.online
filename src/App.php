<?php

namespace LearnGit;

use LearnGit\Controller\CategoriesController;
use LearnGit\Controller\CategoryController;
use LearnGit\Controller\CourseController;
use LearnGit\Controller\CourseLessonController;
use LearnGit\Controller\DataController;
use LearnGit\Controller\IndexController;
use LearnGit\Controller\LearningController;
use LearnGit\Controller\LessonController;
use LearnGit\Controller\LessonsController;
use LearnGit\Controller\NotFoundController;
use LearnGit\Reader\Factory;

class App
{
    private Router $router;
    private LessonReader $lessonReader;
    private CategoryReader $categoryReader;
    private CourseReader $courseReader;

    public function __construct(Request $request)
    {
        $this->router         = new Router($request);
        $this->categoryReader = Factory::createCategoryReader();
        $this->courseReader   = Factory::createCourseReader();
        $this->lessonReader   = Factory::createLessonReader(null, $this->categoryReader);
    }

    public function run(): void
    {
        $this->router->add404(new NotFoundController());
        $this->router->addRoute('GET', '/', new IndexController());
        $this->router->addRoute('GET', '/learning', new LearningController($this->courseReader, $this->lessonReader));
        $this->router->addRoute('GET', '/lessons', new LessonsController($this->lessonReader));
        $this->router->addRoute('GET', '/categories', new CategoriesController($this->categoryReader));
        $this->router->addRoute('GET', '/course/([0-9a-z\-_]+)/([0-9a-z\-_]+)', new CourseLessonController($this->courseReader, $this->lessonReader, $this->categoryReader));
        $this->router->addRoute('GET', '/course/([0-9a-z\-_]+)', new CourseController($this->courseReader, $this->lessonReader));
        $this->router->addRoute('GET', '/lesson/([0-9a-z\-_]+)', new LessonController($this->lessonReader, $this->categoryReader));
        $this->router->addRoute('GET', '/category/([a-z\-_]+)', new CategoryController($this->lessonReader, $this->categoryReader));
        $this->router->addRoute('GET', '/data/([0-9a-z\-_]+)', new DataController($this->lessonReader, $this->courseReader));
        $this->router->route();
    }
}
