<?php

/**
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Model;

use Symfony\Component\Filesystem\Filesystem;

class SmallWebsiteModel extends BuildAWebsiteBaseModel {

	/**
	 * 
	 * @param type $app
	 */
	public function __construct($app) {
		//-- Added $app to parent constructor
		parent::__construct($app);
	}

	/**
	 * @overriden
	 */
	public function websiteBackup() {
		$this->feedBack[] = 'backup was over written';
	}

	/**
	 * 
	 */
	public function installationOfSmallWebsite() {
		$path = $this->getSitePathDirectory();
		$path = $path . "/build";

		$siteInstall = $this->app['trace.config']->bashDirectory
			. "/installationSmallWebSite  $path";

		foreach ($this->execShell->executeShell($siteInstall) as $installVal) {
			$this->feedBack[] = $installVal;
		}
	}

	/**
	 * 
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
	 * @param string $url
	 * @param string $email
	 * @return array
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
