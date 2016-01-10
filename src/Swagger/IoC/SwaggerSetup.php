<?php

namespace DC\Router\Swagger\IoC;

class SwaggerSetup
{
    public static function setup(\DC\IoC\Container $container, \DC\Router\Swagger\Options $swaggerOptions) {
        $container->register($swaggerOptions);
    }
}