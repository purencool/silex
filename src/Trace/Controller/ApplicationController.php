<?php

/**
 * Routes the html requests to the correct twig
 * template files
 *
 *
 * @package    **Trace**
 * @category   Trace Controller
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Controller;

use Silex\Application;
use Trace\Forms\BuildAWebsite;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationController {

	/**
	 * 
	 *  Build Website Form
	 *
	 *  @param  array $app Application container
	 *  @return array building drush form
	 */
	private function buildAWebsiteForm(Application $app) {
		$data = array();
		$formBuild = $app['form.factory']->createBuilder(new BuildAWebsite(), $data);
		$form = $formBuild->getForm();
		$form_view = $form->createView();

		return $param = array(
		    'build_a_website' => $form_view,
		);
	}

	/**
	 * 
	 *  Testing to see if email is correct
	 * 
	 * @todo   will need to test mx in the future
	 * @param  type $app  Application container
	 * @param  type $email Email user wants to use
	 * @return array Array of email error
	 */
	private function testEmail(Application $app, $email) {

		$errors = $app['validator']->validateValue($email, new Assert\Email());

		if (count($errors) > 0) {
			$formFeedBack['email'] = (string) $errors;
		} else {
			$formFeedBack['email'] = '';
		}

		return $formFeedBack;
	}

	/**
	 * 
	 * @param String $app
	 * @param String $version
	 * @param String $size
	 * @param String $url
	 * @param String $email
	 * @param String $production
	 * @return Array
	 */
	private function typeOfSite($app, $version, $size, $url, $email, $production) {
		//print $version.$size;
		if ($version == 1) {
			if ($size == 1) {
				// print "This is drupal 8 small";
			} else {
				// print "This is drupal 8 large";
			}
		} else {

			if ($size == 1) {
				//-- Small Drupal 7
				$newWebSiteParamObj = new \Trace\Model\D7\SmallWebsiteModel($app);
				$newLogin = $newWebSiteParamObj->buildSmallWebsite($url, $email, $production);
				$urlName['name'] = $newLogin['name'];
				$urlName['url'] = $newLogin['url'];
			} else {
				//-- Large drupal 7 website
				$newWebSiteParamObj = new \Trace\Model\D7\BuildAWebsiteModel7($app);
				$urlName = $newWebSiteParamObj->buildWebsiteStructure($url, $email);
				$urlName['name'] = $newLogin['name'];
				$urlName['url'] = $newLogin['url'];
			}
		}

		return $urlName;
	}

	/**
	 * 
	 *  Index action controller
	 *
	 *  @param  array $app Application container
	 *  @return array For twig templating file
	 */
	public function indexAction(Application $app, Request $request) {

		$urlName = array('name' => '', 'url' => '');
		$getRequest = $request->get('new_site');
		//print_r($getRequest); exit;
		$email = $app->escape($getRequest['email']);

		$url = $app->escape($getRequest['url']);
		if ($url != '' && $email != '') {
			$production = $app->escape($getRequest['production']);
			//typeOfSite($app, $version, $size,$url,$email, $production) 
			$urlName = $this->typeOfSite($app, $getRequest['D8'], $getRequest['size'], $url, $email, $production
			);
		}

		$appFeedback = $app[feedback]->getFeedback();
		return $app['twig']->render('index.html.twig', array(
			    'new_website_form' => $this->buildAWebsiteForm($app),
			    'form_feed_back' => $this->testEmail($app, $email),
			    'new_website' => $urlName,
			    'app_feedback' => $appFeedback,
		));
	}

}
