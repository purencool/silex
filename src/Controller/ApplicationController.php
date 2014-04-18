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
        $formBuilder = $app['form.factory']->createBuilder(new \Forms\LoginForm() , $data);
        $form = $formBuilder->getForm();
        $form_view = $form->createView();

        return $param = array(
           'login_form' => $form_view,
        );
    }

    public function indexAction(Application $app)
    {
        $param = $this->loginForm($app);
        return $app['twig']->render('index.html.twig', $param);
    }

    public function contactAction(Application $app)
    {
        $param = $this->loginForm($app);
        return $app['twig']->render('contact.html.twig', $param);
    }

    public function aboutAction(Application $app)
    {
        $param = $this->loginForm($app);
        return $app['twig']->render('about.html.twig', $param);
    }
    public function userPanelAction(Application $app)
    {
        return $app['twig']->render('user_panel.html.twig');
    }
}
