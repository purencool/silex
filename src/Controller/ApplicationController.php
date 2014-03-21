<?php 
namespace Controller;

use Silex\Application;

//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;

class ApplicationController
{
    public function indexAction(Application $app)
    {
        return $app['twig']->render('index.html.twig');
    }
  
    public function contactAction(Application $app)
    {
        return $app['twig']->render('contact.html.twig');
    }

    public function aboutAction(Application $app)
    {
        $data = array();
        $formBuilder = $app['form.factory']->createBuilder(new \Trace\LoginForm, $data);
        $form = $formBuilder->getForm();
        $form_view = $form->createView();
        
        $param = array(
           'form' => $form_view,  
        );
        return $app['twig']->render('about.html.twig',$param);
    }
}
