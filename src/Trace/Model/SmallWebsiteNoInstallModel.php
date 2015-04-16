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

class SmallWebsiteNoInstallModel extends BuildAWebsiteBaseModel {

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

	public function websiteInstallation() {
		$this->feedBack[] = 'Setup done but no database installed';
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
	 * @param type $url
	 * @param type $email
	 * @return string
	 */
	public function buildSmallWebsite($url, $email) {
		$return = array();

		$this->buildWebsiteStructure($url, $email);
		//$this->installationOfSmallWebsite();
		//$this->websiteEditor();
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
