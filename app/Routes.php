<?php
Class Router
{
    protected $routes = array();
    protected $request;
    protected $uri;
    protected $is_callable = true;
    protected $is_renderable = true;

    public function __construct()
    {
        $env = \Slim\Environment::getInstance();
        $this->request = new \Slim\Http\Request($env);
        $this->uri     = $this->request->getResourceUri();
        $this->routes  = array();

        $this->init();
    }

    public function init()
    {
        $custom_routes = require(CONFIG_PATH . 'routes.php');
        $route         = $this->parseRoutes();

        $routes = array_merge($route, $custom_routes);
        $this->addRoutes($routes);
    }

    public function parseRoutes()
    {
        $resourceUri = $this->request->getResourceUri();
        $uriPath     = array_values(array_filter(preg_split('/\//', $resourceUri)));

        return array(
            $resourceUri => array(
                'controller' => reset($uriPath),
                'action'     => $uriPath[1]
            )
        );
    }

    public function addRoutes($routes = array())
    {
        foreach ($routes as $route => $path) {
            $method = "any";
            $func   = $this->processCallback($path);

            if ($func) {
                $r = new \Slim\Route($route, $func);
                $r->setHttpMethods(strtoupper($method));

                array_push($this->routes, $r);
            }
        }
    }

    protected function processCallback($route = null)
    {
        $class      = isset($route['controller']) ? ucwords($route['controller']) . 'Controller' : 'AppController';
        $function   = isset($route['action']) ? $route['action'] : 'index';
        $controller = CONTROLLER_PATH . $class . '.php';
        $view       = VIEW_PATH . $function . '.php';

        if (!file_exists($controller)) {
            $this->is_callable = false;
            return false;
        }

        if (!file_exists($view)) {
            $this->is_renderable = false;
            return false;
        }

        $func = function () use ($class, $function, $controller) {
            require $controller;
            $class = new $class();

            $args = func_get_args();
            return call_user_func_array(array($class, $function), $args);
        };

        return $func;
    }

    public function run()
    {
        $uri = $this->request->getResourceUri();
        $method = $this->request->getMethod();

        if (!$this->is_callable) {
            echo $uri;
            echo '<br/>';
            echo 'Controlller not found';
            return false;
        }

        if (!$this->is_renderable) {
            echo $uri;
            echo '<br/>';
            echo 'View not found';
            return false;
        }

        foreach ($this->routes as $i => $route) {
            if ($route->matches($uri)) {
                if ($route->supportsHttpMethod($method) || $route->supportsHttpMethod("ANY")) {
                    call_user_func_array($route->getCallable(), array_values($route->getParams()));
                }
            }
        }
    }

}
