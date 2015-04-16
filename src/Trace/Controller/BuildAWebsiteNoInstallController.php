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
use Trace\Forms\BuildAWebsite;


class BuildAWebsiteNoInstallController {


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
			$formFeedBack['email'] = 'The email is valid';
		}

		return $formFeedBack;
	}

	/**
	 * 
	 *  Index action controller
	 *
	 *  @param  array $app Application container
	 *  @return array For twig templating file
	 */
	public function indexAction(Application $app) {

		$urlName = array();
		$request = $app['request']->get('new_site');
		$email = $app->escape($request['email']);

		$url = $app->escape($request['url']);
		if ($url != '' && $email != '') {

			$newWebSiteParamObj = new \Trace\Model\SmallWebsiteNoInstallModel($app);
			$newLogin = $newWebSiteParamObj->buildSmallWebsite($url, $email);
			$urlName['name'] = $newLogin['name'];
			$urlName['url'] = $newLogin['url'];
		} else {
			$urlName['name'] = '';
			$urlName['url'] = '';
		}
                $appFeedback = $app[feedback]->getFeedback();
		return $app['twig']->render('index.html.twig', array(
			    'new_website_form' => $this->buildAWebsiteForm($app),
			    'form_feed_back' => $this->testEmail($app, $email),
			    'new_website' => $urlName,
		            'app_feedback' =>  $appFeedback,
		));
	}

}