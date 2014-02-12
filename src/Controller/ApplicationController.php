<?php 
namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController
{
   public function indexAction(Application $app)
    {	
        return $app['twig']->render('index.html.twig');
    }
}
