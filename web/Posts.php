<?php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader as YamlRouting;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

//-- Telling Silex where the yaml settings file is kept
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../src/Resources/config/settings.yml'));
$app['debug'] = $app['config']['debug'];

//-- Load twig and giving silex path to the twig files
$app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../src/Resources/view/',
));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    return $twig;
}));

//-- Loading yaml url routes file is kept and then loading file 
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, $app) {
    $loader     = new YamlRouting(new FileLocator(__DIR__ . '/../src/Resources/config'));
    $collection = $loader->load('routes.yml');
    $routes->addCollection($collection);

    return $routes;
});

$app->register(new Silex\Provider\SessionServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


