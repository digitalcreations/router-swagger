<?php

require_once '../vendor/autoload.php';

class DefaultController extends \DC\Router\ControllerBase {
    /**
     * @route GET /
     */
    public function getRoot() {
        echo "Root!";
    }
}

/**
 * Cats. They're awesome.
 */
class CatsController extends \DC\Router\JsonController implements \DC\Router\Swagger\ISwaggerAPI {
    private $repository;

    function __construct()
    {
        $this->repository = [
            1 => new \Cat(1, "Squiggles"),
            2 => new \Cat(2, "Squishy"),
            3 => new \Cat(3, "Angry")
        ];
    }

    /**
     * Record the birth of a cat.
     *
     * @route POST /api/cats
     * @param Cat $cat
     * @body $cat
     * @return Cat
     */
    public function postCat(\Cat $cat) {
        return $cat;
    }

    /**
     * List all cats.
     *
     * Return a list of all cats in the system.
     *
     * @route GET /api/cats
     * @return \Cat[] Cats
     */
    public function getAll() {
        return array_values($this->repository);
    }

    /**
     * Get a specific cat by id.
     *
     * @route GET /api/cat/{id:int}
     * @param int $id Cat ID
     * @return \Cat A single cat
     */
    public function get($id) {
        return $this->repository[$id];
    }

    /**
     * @route GET /api/cats/search?q={name}
     * @param string $name Name
     * @return Cat[]
     */
    public function getByName($name) {
        return array_filter($this->repository, function($cat) use ($name) {
            return stripos($cat->name, $name) !== false;
        });
    }

    /**
     * Get the cat's birthday.
     *
     * @route GET /api/cat/{id:int}/birthday?format={foo:int}
     * @param int $id Cat ID
     * @param int $foo DateTime format
     * @return \DateTime Name
     * @throws \Exception When the kitten isn't born yet.
     */
    public function getBirthday($id, $foo = 23) {
        return new \DateTime();
    }
}

class Cat {
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $name;

    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

$container = new \DC\IoC\Container();
$container->register(function() {
    return new \DC\Cache\Implementations\Memcached\MemcacheConfiguration('localhost', 11211);
})->to('\DC\Cache\Implementations\Memcached\MemcacheConfiguration')->withContainerLifetime();
$container->register('\DC\Cache\Implementations\Memcached\Cache')->to('\DC\Cache\ICache')->withContainerLifetime();

\DC\JSON\IoC\SerializerSetup::setup($container);
\DC\Router\Swagger\IoC\SwaggerSetup::setup($container, new \DC\Router\Swagger\Options(
    new \DC\Router\Swagger\ComposerPackage(realpath("../composer.json"))
));
\DC\Router\IoC\RouterSetup::route($container,
    ['\DefaultController', '\CatsController', '\DC\Router\Swagger\SwaggerController']);