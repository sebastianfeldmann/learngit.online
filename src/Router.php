<?php

namespace LearnGit;

class Router
{
    private array $routes = [];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function add404(callable $action): void
    {
        $this->routes['404'] = $action;
    }

    public function addRoute(string $method, string $path, callable $action): void
    {
        $this->routes[$method][$path] = $action;
    }

    public function route(): void
    {
        $method = $this->request->method();
        $path   = $this->request->path();
        $exec   = ['action' => $this->routes['404'], 'matches' => []];

        foreach ($this->routes[$method] as $route => $action) {
            if (preg_match('#^' . $route . '$#', $path, $matches)) {
                $exec = ['action' => $action, 'matches' => $matches];
                break;
            }
        }
        $exec['action'](...array_slice($exec['matches'], 1));
    }
}

