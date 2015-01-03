<?php
/**
* Routes the html requests to the correct twig
* template files
*
* @package    Trace
* @category
* @author     Purencool Website Development
* @license    GPL3
*/
namespace Controller;

use Silex\Application;
use Forms\DrushCommand;
use Symfony\Component\HttpFoundation\Request;

class DrushController {

 /**
 *  Build Drush form
 *
 *  @param array $app application container
 *  @return array building drush form
 *
 */
 private function drushForm(Application $app)
 {

  $request = $app['request'];
  $data = array();

  $formBuild = $app['form.factory']->createBuilder(new DrushCommand(), $data);
  $form = $formBuild->getForm();
  $form->bind($request);
  $form_view = $form->createView();

  return $param = array(
   'drush_command' => $form_view,
  );
 }


 /**
 *  Drush form reponse
 *
 *  @param array $app application container
 *  @return array for twig templating file
 *
 */

 public function drushResponse(Application $app)
 {

  //if($form->isValid()) {
  $data = $this->drushForm($app);
   print_r($data);
  //return $app['twig']->render('result.twig', array('data'=>$data));
 //} else {
 //print "Nothing to see";
 //return 'form error!';
  //}

}


 /**
 *  Drush action controller
 *
 *  @param array $app application container
 *  @return array for twig templating file
 *
 */

 public function drushAction(Application $app)
 {
  $param = $this->drushForm($app);
  //$this->drushResponse($app);
  //
  //$request =  $_GET;
  $request = $app['request']->get('drush_command');
  print_r( $request );
  return $app['twig']->render('auth_drush.html.twig',$param);
 }
}
