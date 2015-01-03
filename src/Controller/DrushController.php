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
use Model\BashExecute;
use Symfony\Component\HttpFoundation\Request;

class DrushController {


 /**
 *  Build List
 *
 *  @param array $app application container
 *  @return array of builds currently being managed
 *
 */
 private function buildList(Application $app)
 {

  $directoryList = array('none' => 'Please a website');

  if ($handle = opendir($app['trace.config']->websitesDirectory)) {
   $blacklist = array('.', '..', 'somedir', '.txt');
   while (false !== ($file = readdir($handle))) {
    if (!in_array($file, $blacklist)) {
     $directoryList[$file] = $file;
    }
   }
   closedir($handle);
  }

  return $directoryList;
 }



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

  $choices = array(
   'build.choices' => $this->buildList($app),
   'build.prefer' => array('none')
  );


  $formBuild = $app['form.factory']->createBuilder(new DrushCommand($choices), $data);
  $form = $formBuild->getForm();
  $form->bind($request);
  $form_view = $form->createView();

  return $param = array(
   'drush_command' => $form_view,
  );
 }


 /**
 *  Drush command to be executed
 *
 *  @param array $app application container
 *  @return array for twig templating file
 *
 *  @todo filter drush commands
 *
 */

 public function drushResponse(Application $app)
 {

  $request = $app['request']->get('drush_command');
  $websiteName = $request['managed_website'].'/build/';
  $directoryPath = $app['trace.config']->websitesDirectory;
  $buildPath = $directoryPath.'/'.$websiteName;
  $exec = new BashExecute($app);
  print_r($exec->drushExecute($buildPath, $request['drush']));

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
  $this->drushResponse($app);
  return $app['twig']->render('auth_drush.html.twig',$param);
 }
}
