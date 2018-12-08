<?php
/**
 * User: grimbees
 * Date: 12/8/2018
 * Time: 8:30 PM
 */

namespace GrimBees\DocRouter;

use GrimBees\DocRouter\Router\Collector;
use Illuminate\Support\ServiceProvider;

class DocRouterServiceProvider extends ServiceProvider {

    /**
     * Register the Document Router Service Provider
     */
    public function register() {
        $collector = new Collector();
        $collector->process();
    }

}