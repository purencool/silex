<?php

/**
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Model;

class BuildAWebsiteBaseModel {

	protected $app;
	protected $execShell;
	private $newWebsiteName;
	private $sitePathDirectory = '';
	private $sitePathBuildDirectory = '';
	private $buildType = '/build ';
	private $installationType = '/installation ';
	private $loginUrl = '';
	private $editorEmail = '';

	/**
	 * 
	 * @param type $app
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->execShell = new \Trace\Model\BashExecute($app);
	}

	/**
	 * 
	 */
	private function buildMethods() {
		$this->websiteSetup();
		$this->websiteInstallation();
		$sF = new BuildAWebsiteBaseFilesModel($this->app , $this->newWebsiteName, $this->sitePathDirectory , $this->sitePathBuildDirectory);
		
		$this->websiteBackup();
	}
	
	/**
	 * 
	 */
	private function websiteName() {
		$sitArr = explode('/', $this->sitePathDirectory);
		$this->newWebsiteName = end($sitArr);
	}

	/**
	 * 
	 * @param type $websiteName
	 * @param type $editorEmail
	 */
	public function buildWebsiteStructure($websiteName = NULL, $editorEmail = NULL) {
		
		$this->editorEmail = $editorEmail;
		
		$buildBashPath = $this->app['trace.config']->bashDirectory
			. $this->buildType
			. ' ' . $this->app['trace.config']->websitesDirectory;
		
		if ($websiteName == NULL) {
			$buildBashPath .= ' ' . $this->app['trace.config']->siteName;
		} else {
			$newstr = preg_replace('/[^a-zA-Z0-9\']/', '_', $websiteName);
			$newsite = strtolower (str_replace("'", '', $newstr));
			$buildBashPath .= ' ' . $newsite;
		}

		$buildOutput = $this->execShell->executeShell($buildBashPath);
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'buildWebsiteStructure', $buildOutput);

		//-- Get website paths and name
		$this->sitePathDirectory = $buildOutput[1];
		$this->sitePathBuildDirectory = $buildOutput[2];
		$this->websiteName();

		//-- Install website if file exits
		if (file_exists($this->sitePathBuildDirectory . '/sites/all/themes/mothership/README.txt')) {
			$this->buildMethods();
		}
	}
	

	/**
	 * 
	 */
	public function websiteSetup() {

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files';
		mkdir($createFilesDir, 0777);

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files/tmp';
		mkdir($createFilesDir, 0777);

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files/private';
		mkdir($createFilesDir, 0777);

		$settings = $this->sitePathBuildDirectory . '/sites/default/settings.php';
		$settingsDefault = $this->sitePathBuildDirectory . '/sites/default/default.settings.php';
		copy($settingsDefault, $settings);
		
                $this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteSetup', array("<span>$settings have been created</span>"));
	}

	/**
	 * 
	 */
	public function websiteInstallation() {
		
		$websiteType = $this->app['trace.config']->websiteType;
		$sitename    = $this->newWebsiteName;
		$user        = $this->app['trace.config']->siteUser;
		$password    = $this->app['trace.config']->sitePassword;
		$dbUser      = $this->app['trace.config']->databaseUser;
		$dbPass      = $this->app['trace.config']->databasePassword;
		$buildPath   = $this->sitePathBuildDirectory . '/';
		$email       = $this->app['trace.config']->siteEmail;
                $siteVars    = " $websiteType $sitename $user $password $dbUser $dbPass $buildPath $email";
		
		$install = $this->app['trace.config']->bashDirectory . $this->installationType . $siteVars;

		$execOutput = $this->execShell->executeShell($install);
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteInstallation',$execOutput);
	}

	/**
	 * 
	 */
	public function websiteEditor() {
		if ($this->editorEmail == '') {
			$USEREDITOR = $this->app['trace.config']->userEditor;
			$EMAILEDITOR = $this->app['trace.config']->emailEditor;
		} else {
			$userName = explode('@', $this->editorEmail);
			$USEREDITOR =  $userName[0];
			$EMAILEDITOR = $this->editorEmail;
		}

		$PASSWORDEDITOR = $this->app['trace.config']->passwordEditor;
		$SITEPATH = $this->sitePathBuildDirectory . '/';


		$editor = $this->app['trace.config']->bashDirectory
			. '/editor ' . " $USEREDITOR $PASSWORDEDITOR $EMAILEDITOR $SITEPATH";

		$output = $this->execShell->executeShell($editor);
		$feedback = $this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteEditor',$output);
		$url = array_pop($feedback);
		$urlEx = explode('/', $url);
		$this->loginUrl = $urlEx[3] . "/" . $urlEx[4] . "/" . $urlEx[5] . "/" . $urlEx[6] . "/" . $urlEx[7] . "/" . $urlEx[8];
	}


	/**
	 * 
	 */
	public function websiteBackup() {
		$SITENAME = $this->newWebsiteName;
		$DATABASEUSER = $this->app['trace.config']->databaseUser;
		$DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
		$SITEPATHBUILD = $this->sitePathBuildDirectory . '/';
		$SITEPATH = $this->sitePathDirectory . '/';


		$firstBackup = $this->app['trace.config']->bashDirectory
			. "/dev/backup $SITENAME $DATABASEUSER $DATABASEPASSWORD  "
			. $SITEPATH . "databases";

		$backupOutput = $this->execShell->executeShell($firstBackup);
                $this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteBackup',$backupOutput);


		$firstBuildBackup = $this->app['trace.config']->bashDirectory
			. "/backupBuild $SITENAME $SITEPATHBUILD "
			. $SITEPATH . "backup-build/";

		$backupBuildOutput = $this->execShell->executeShell($firstBuildBackup);
                $this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteBackup',$backupBuildOutput);
	}


	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getWebsite() {
		return $this->newWebsite;
	}

	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getNewWebsiteName() {
		return $this->newWebsiteName;
	}

	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getSitePathDirectory() {
		return $this->sitePathDirectory;
	}

	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getSitePathBuildDirectory() {
		return $this->sitePathBuildDirectory;
	}

	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getBuildType() {
		return $this->buildType;
	}

	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getInstallationType() {
		return $this->installationType;
	}

	/**
	 * [getWebsite description]
	 * @return object
	 */
	public function getLoginUrl() {
		return $this->loginUrl;
	}

	/**
	 *  @return string
	 */
	public function __toString() {
		return "Trace\Model\BuildAWebsite";
	}

}
