<?php

/**
 * After the base Drupal installation has been created then we can add and remove 
 * things from the build by extending the base install. The small website does
 * just that. This adds and removes diffent modules by the use of drush and
 * the bash shell
 * 
 *
 * @package    **Trace**
 * @category   Trace Model
 * @author     Purencool Website Development
 * @license    GPL3
 *
 */

namespace Trace\Model;

use Symfony\Component\Filesystem\Filesystem;

class SmallWebsiteModel extends BuildAWebsiteBaseModel {

	/**
	 *
	 * @var array Array of objects for the parent constructor
	 */
	public function __construct($app) {
		//-- Added $app to parent constructor
		parent::__construct($app);
	}

	/**
	 * @overriden not needed in this build
	 */
	public function websiteBackup() {
		$this->feedBack[] = 'backup was over written';
	}

	/**
	 * 
	 * Install the small website Drupal modules using drush
	 */
	public function installationOfSmallWebsite() {
		$path = $this->getSitePathDirectory();
		$path = $path . "/build";

		$siteInstall = $this->app['trace.config']->bashDirectory
			. "/installationSmallWebSite  $path";

		$buildOutput = $this->execShell->executeShell($siteInstall);
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'buildWebsiteStructure', $buildOutput);
	}

	/**
	 * 
	 *  Remove all excess directories and Drupal modules
	 */
	public function removeExcess() {
		$filesystem = new Filesystem();
		$path = $this->getSitePathDirectory();

		if ($filesystem->exists("$path/databases")) {
			$filesystem->remove("$path/databases");
		}
		if ($filesystem->exists("$path/build/sites/all/modules/contribxtra")) {
			$filesystem->remove("$path/build/sites/all/modules/contribxtra");
		}

		if ($filesystem->exists("$path/build/sites/all/modules/development")) {
			$filesystem->remove("$path/build/sites/all/modules/development");
		}
		rmdir($path . '/backup-build');
		rmdir($path . '/documents');
		rmdir($path . '/features');
		rmdir($path . '/images');
	}

	/**
	 * 
	 * Configures Drupal small website installation 
	 * @see BuildAWebsiteBaseModel->buildWebsiteStructure()
	 * @see SmallWebsiteModel->installationOfSmallWebsite()
	 * @see BuildAWebsiteBaseModel->websiteEditor()
	 * @see SmallWebsiteModel->removeExcess()
	 * @param string $url Gets url the user wants to get
	 * @param string $email Gets the new users email
	 * @return array Array of feedback
	 */
	public function buildSmallWebsite($url, $email) {
		$return = array();

		$this->buildWebsiteStructure($url, $email);
		$this->installationOfSmallWebsite();
		$this->websiteEditor();
		$this->removeExcess();

		$newLogin = $this->getLoginUrl();
		$return['name'] = $this->getNewWebsiteName();
		$return['url'] = $this->app['trace.config']->tempWebsiteUrl . $return['name'] . "/build/" . $newLogin;

		return $return;
	}

	/**
	 *  @return string
	 */
	public function __toString() {
		return "Trace\Model\SmallWebsite";
	}

}
