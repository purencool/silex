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

namespace Trace\Controller;

use Silex\Application;
use Trace\Forms\BuildAWebsite;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationController {

	/**
	 *  Build Website Form
	 *
	 *  @param array $app application container
	 *  @return array building login form
	 *
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
	 *  Index action controller
	 *
	 *  @param array $app application container
	 *  @return array for twig templating file
	 *
	 */
	public function indexAction(Application $app) {
		
		$urlName = array();
		$request = $app['request']->get('new_site');
		$email = $app->escape($request['email']);
		$errors = $app['validator']->validateValue($email, new Assert\Email());

		if (count($errors) > 0) {
			$formFeedBack['email'] = (string) $errors;
		} else {
			$formFeedBack['email'] = 'The email is valid';
		}

		$url = $app->escape($request['url']);
		if ($url != '' && $email != '' && $errors >= 0) {
			
			$newWebSiteParamObj = new \Trace\Model\SmallWebsiteModel($app);
			$newLogin = $newWebSiteParamObj->buildSmallWebsite($url,$email);
			$urlName['name'] = $newLogin['name'];
			$urlName['url'] =  $newLogin['url'];
			
		} else {
			$urlName['name'] = '';
			$urlName['url'] = '';
		}

		return $app['twig']->render('index.html.twig', array(
			    'new_website_form' => $this->buildAWebsiteForm($app),
			    'form_feed_back' => $formFeedBack,
			    'new_website' => $urlName,
		));
	}

	/**
	 *  Contact action controller
	 *
	 *  @param array $app application container
	 *  @return array for twig templating file
	 *
	 */
	public function contactAction(Application $app) {
		$param = $this->loginForm($app);
		return $app['twig']->render('contact.html.twig', $param);
	}

	/**
	 *  About action controller
	 *
	 *  @param array $app application container
	 *  @return array for twig templating file
	 *
	 */
	public function aboutAction(Application $app) {
		$param = $this->loginForm($app);
		return $app['twig']->render('about.html.twig', $param);
	}

}
