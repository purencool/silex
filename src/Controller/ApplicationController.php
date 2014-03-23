<?php 
/**
 * Routes the trace path to the relavant pagess
 *
 * @package    Trace
 * @category   Routing
 * @author     Purencool Website Development
 * @license    GPL3
 */
namespace Controller;

use Silex\Application;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;

class ApplicationController
{
   private function loginForm(Application $app)
   {
        $data = array();
        $formBuilder = $app['form.factory']->createBuilder(new \src\Forms\LoginForm() , $data);
        $form = $formBuilder->getForm();
        $form_view = $form->createView();
        
        return $param = array(
           'form' => $form_view,  
        );
    }   
    
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
        $param =$this->loginForm($app);
        return $app['twig']->render('about.html.twig',$param);
    }
     
}
