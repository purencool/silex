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
	private $feedBack = array();
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
		$this->websiteHostFile();
		$this->websiteSettingsFile();
		$this->websiteVHostFile();
		$this->websiteBackup();
	}

	/**
	 * 
	 * @param type $websiteName
	 * @param type $editorEmail
	 */
	public function buildWebsiteStructure($websiteName = NULL, $editorEmail = NULL) {
		$this->editorEmail = $editorEmail;
		$this->feedBack[] = "<span>Creating website stucture</span>";
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

		$siteUrl = $this->app['trace.config']->tempWebsiteUrl;
		$this->feedBack[] = "<a href='" . $siteUrl . "' target='_blank'>See your new website</a>";

		$buildOutput = $this->execShell->executeShell($buildBashPath);

		//-- Get website paths and name
		$this->sitePathDirectory = $buildOutput[1];
		$this->sitePathBuildDirectory = $buildOutput[2];
		$this->websiteName();

		//-- Install website
		if (file_exists($this->sitePathBuildDirectory . '/sites/all/themes/mothership/README.txt')) {
			$this->feedBack[] = "Read me file exits";
			$this->buildMethods();
		} else {
			$this->feedBack[] = "Read me file does not exit";
		}

		foreach ($buildOutput as $buildOutputVal) {
			$this->feedBack[] = $buildOutputVal;
		}
	}

	/**
	 * 
	 */
	public function websiteName() {
		$sitArr = explode('/', $this->sitePathDirectory);
		$this->newWebsiteName = end($sitArr);
		$this->feedBack[] = "<span>The site name is $this->newWebsiteName</span>";
	}

	/**
	 * 
	 */
	public function websiteSetup() {

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files';
		mkdir($createFilesDir, 0777);
		chmod($createFilesDir, 0775);

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files/tmp';
		mkdir($createFilesDir, 0777);
		chmod($createFilesDir, 0775);

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files/private';
		mkdir($createFilesDir, 0777);
		chmod($createFilesDir, 0775);


		$settings = $this->sitePathBuildDirectory . '/sites/default/settings.php';
		$settingsDefault = $this->sitePathBuildDirectory . '/sites/default/default.settings.php';
		copy($settingsDefault, $settings);
		chmod($settings, 0775);

		$this->feedBack[] = "<span>$settings have been created</span>";
	}

	/**
	 * 
	 */
	public function websiteInstallation() {
		$WEBSITETYPE = $this->app['trace.config']->websiteType;
		$SITENAME = $this->newWebsiteName;
		$USER = $this->app['trace.config']->siteUser;
		$PASSWORD = $this->app['trace.config']->sitePassword;
		$DATABASEUSER = $this->app['trace.config']->databaseUser;
		$DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
		$SITEPATHBUILD = $this->sitePathBuildDirectory . '/';
		$EMAIL = $this->app['trace.config']->siteEmail;


		$siteInstall = $this->app['trace.config']->bashDirectory
			. $this->installationType . " $WEBSITETYPE $SITENAME $USER $PASSWORD $DATABASEUSER $DATABASEPASSWORD $SITEPATHBUILD $EMAIL";

		//-- execute install file.
		foreach ($this->execShell->executeShell($siteInstall) as $installVal) {
			$this->feedBack[] = $installVal;
		}
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

		//-- execute install file.
		foreach ($this->execShell->executeShell($editor) as $installVal) {
			$this->feedBack[] = $installVal;
		}
		$url = array_pop($this->feedBack);
		$urlEx = explode('/', $url);
		$this->loginUrl = $urlEx[3] . "/" . $urlEx[4] . "/" . $urlEx[5] . "/" . $urlEx[6] . "/" . $urlEx[7] . "/" . $urlEx[8];
	}

	/**
	 * 
	 */
	public function websiteHostFile() {

		$ENDURL = $this->app['trace.config']->endUrl;
		$SITENAME = $this->newWebsiteName;
		$SITEPATH = $this->sitePathDirectory . '/';
		$newHostName = $SITENAME . '.' . $ENDURL . PHP_EOL;
		file_put_contents($SITEPATH . $newHostName, $newHostName, FILE_APPEND | LOCK_EX);
	}

	/**
	 * 
	 */
	public function websiteSettingsFile() {
		$SITENAME = $this->newWebsiteName;
		$USER = $this->app['trace.config']->siteUser;
		$PASSWORD = $this->app['trace.config']->sitePassword;
		$DATABASEUSER = $this->app['trace.config']->databaseUser;
		$DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
		$SITEPATHBUILD = $this->sitePathBuildDirectory . '/';
		$SITEPATH = $this->sitePathDirectory . '/';

		$newConfigName = $SITENAME . '.php';
		$newConfigData = "<?php
                   \$appCurrentDevelopment = array(
                     'current-build-path' =>'$SITEPATHBUILD',
                     'username'=>'$USER',
                     'password'=>'$PASSWORD',
                     'database-user'=>'$DATABASEUSER',
                     'database-password'=>'$DATABASEPASSWORD',
                     'database-name'=>'$SITENAME');";
		file_put_contents($SITEPATH . $newConfigName, $newConfigData, FILE_APPEND | LOCK_EX);
	}

	/**
	 * 
	 */
	public function websiteVHostFile() {
		$ENDURL = $this->app['trace.config']->endUrl;
		$SITENAME = $this->newWebsiteName;
		$SITEPATH = $this->sitePathDirectory . '/';

		$file = $SITEPATH . $SITENAME . '.' . $ENDURL . '.conf';
		$data = " <VirtualHost *:80>
			ServerAdmin webmaster@$SITENAME.$ENDURL
			ServerName $SITENAME.$ENDURL
			ServerAlias $SITENAME.$ENDURL
			DocumentRoot $SITEPATH

			<Directory $SITEPATH>
				Options Indexes FollowSymLinks
				AllowOverride All
				Require all granted
			</Directory>

			ErrorLog \${APACHE_LOG_DIR}/error.log
			CustomLog \${APACHE_LOG_DIR}/access.log combined
		 </VirtualHost>";
		file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
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

		chmod($SITEPATH . 'databases', 0775);
		chmod($SITEPATH . 'backup-build', 0775);


		$firstBackup = $this->app['trace.config']->bashDirectory
			. "/dev/backup $SITENAME $DATABASEUSER $DATABASEPASSWORD  "
			. $SITEPATH . "databases";

		//-- execute install file.
		$backupOutput = $this->execShell->executeShell($firstBackup);

		foreach ($backupOutput as $backupOutputVal) {
			$this->feedBack[] = $backupOutputVal;
		}


		$firstBuildBackup = $this->app['trace.config']->bashDirectory
			. "/backupBuild $SITENAME $SITEPATHBUILD "
			. $SITEPATH . "backup-build/";

		//-- execute install file.
		$backupBuildOutput = $this->execShell->executeShell($firstBuildBackup);

		foreach ($backupBuildOutput as $backupBuildOutputVal) {
			$this->feedBack[] = $backupBuildOutputVal;
		}
		chmod($backupBuildOutput[1], 0775);
	}

	/**
	 * 
	 * @return array 
	 */
	public function findUninstalledModules() {
		$sitePath = $this->sitePathBuildDirectory . '/';

		$bash = $this->app['trace.config']->bashDirectory . "/uninstalledNonCoreModules" . " $sitePath ";

		//-- execute install file.

		$feedback = $this->execShell->executeShell($bash);

		foreach ($feedback as $feedbackVal) {
			$this->feedBack[] = $feedbackVal;
			$return[] = $feedbackVal;
		}

		return $return;
	}

	public function feedBack() {
		return $this->feedBack;
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
