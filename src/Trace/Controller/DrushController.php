<?php

/**
 * Routes the html requests to the correct twig
 * template files
 *
 * @package    **Trace**
 * @category   Trace Controller
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Controller;

use Silex\Application;
use Trace\Forms\DrushCommand;
use Trace\Model\BashExecute;

class DrushController {

	/**
	 *  
	 * Build List
	 *
	 *  @param  array $app Application container
	 *  @return array Returns builds currently being managed
	 */
	private function buildList(Application $app) {

		$directoryList = array('none' => 'Please a website');
		$handle = opendir($app['trace.config']->websitesDirectory);
		$blacklist = array('.', '..', 'somedir', '.txt');
		while (false !== ($file = readdir($handle))) {
			if (!in_array($file, $blacklist)) {
				$directoryList[$file] = $file;
			}
		}
		closedir($handle);

		return $directoryList;
	}

	/**
	 *  
	 * Build Drush form
	 *
	 *  @param  array $app Application container
	 *  @return array Building drush form
	 */
	private function drushForm(Application $app) {

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
	 *  
	 * Drush command to be executed
	 * 
	 *  @todo   Filter drush commands
	 *  @param  array $app Application container
	 *  @return array for  Twig templating file
	 */
	public function drushResponse(Application $app) {

		$request = $app['request']->get('drush_command');
		$websiteName = $request['managed_website'] . '/build/';
		$directoryPath = $app['trace.config']->websitesDirectory;
		$buildPath = $directoryPath . '/' . $websiteName;
		$exec = new BashExecute($app);
		
		return $exec->drushExecute($buildPath, $request['drush']);
	}

	/**
	 *  
	 * Drush action controller
	 *
	 *  @param  array $app Application container
	 *  @return array For twig templating file
	 */
	public function drushFormatting(Application $app) {
		$returnArr = array();
		$dR = $this->drushResponse($app);
		foreach ($dR as $dRVal) {
			//-- find disable modules
			if (strpos($dRVal, 'Disabled') !== false) {
				$returnArr[] = '<span class="disable">' . $dRVal . '</span>';
			} else {
				$returnArr[] = $dRVal;
			}
		}
		
		return $returnArr;
	}

	/**
	 *  
	 * Drush action controller
	 *
	 *  @param  array $app Application container
	 *  @return array For twig templating file
	 */
	public function drushAction(Application $app) {
		
		return $app['twig']->render('auth_drush.html.twig', array(
			    'drush_form' => $this->drushForm($app),
			    'drush_feedback' => (array) $this->drushFormatting($app)
		));
	}

}
