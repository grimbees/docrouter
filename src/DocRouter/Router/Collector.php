<?php

namespace GrimBees\DocRouter\Router;
use Illuminate\Routing\Route;

/**
 * Created by PhpStorm.
 * User: david
 * Date: 12/8/2018
 * Time: 8:42 PM
 */

class Collector {

    protected $controllers = [];

    public function __construct() {
        $controllers = require_once_base_path('vendor/composer/autoload_classmap.php');
        $controllers = array_filter($controllers, function ($controller) {
            return strpos($controller, 'App\Http\Controllers') !== false;
        });
        $this->controllers = array_map(function ($controller) {

            return str_replace('App\Http\Controllers\\', '', $controller);
        }, $controllers);
    }

    public function process() {
        foreach ($this->controllers as $className) {
            $prefix = '';
            $middleware = '';
            $controller = new \ReflectionClass($className);
            $classDoc = $controller->getDocComment();
            preg_match_all('#@(.*?)\n#s', $classDoc, $classAnnotations);
            foreach($classAnnotations[1] as $classAnnotation) {
                $annotation = explode(' ', $classAnnotation);
                switch(strtolower($annotation[0])) {
                    case 'prefix':
                        $prefix = trim(strtolower($annotation[1])); break;
                    case 'middleware':
                        $middleware = explode(',', trim($annotation[1])); break;
                }
            }
            $groupParams = array();
            if(!empty($middleware))
                $groupParams['middleware'] = $middleware;
            if(!empty($prefix))
                $groupParams['prefix'] = $prefix;
            Route::group($groupParams, function() use ($className, $controller) {
                $functions = $controller->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach($functions as $function) {
                    $method = null;
                    $routePath = null;
                    $routeName = null;
                    $middleware = null;
                    $doc = $function->getDocComment();
                    preg_match_all('#@(.*?)\n#s', $doc, $annotations);

                    foreach($annotations[1] as $annotation) {
                        $values = explode(' ', $annotation);
                        switch(strtolower($values[0])) {
                            case 'method':
                                $method = trim(strtolower($values[1])); break;
                            case 'route':
                                $routePath = trim(strtolower($values[1])); break;
                            case 'name':
                                $routeName = trim(strtolower($values[1])); break;
                            case 'middleware':
                                $middleware = trim(strtolower($values[1])); break;
                        }
                    }

                    if(!empty($method)) {
                        switch ($method) {
                            case 'post':
                                $route = Route::post($routePath, $className.'@'.$function->name);
                                break;
                            case 'get':
                                $route = Route::get($routePath, $className.'@'.$function->name);
                                break;
                            default:
                                $route = Route::any($routePath, $className.'@'.$function->name);
                                break;
                        }
                        if(isset($routeName))
                            $route->name($routeName);
                        if(isset($middleware))
                            $route->middleware($middleware);
                    }
                }
            });
        }
    }

}