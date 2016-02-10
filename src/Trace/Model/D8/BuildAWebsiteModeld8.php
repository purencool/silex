<?php

/**
 *
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Trace\Model;

use Trace\Model\BashExecute;

class BuildAWebsiteModeld8 {

	/**
	 *
	 * @var array Array of objects
	 */
	protected $app;

	/**
	 *
	 * @var object @see Trace\Model\bashExecute()
	 */
	protected $execShell;

	/**
	 *
	 * @var string The new websites name used in the creation of the files
	 */
	private $newWebsiteName;

	/**
	 *
	 * @var string Path to the website directory
	 */
	private $sitePathDirectory = '';

	/**
	 *
	 * @var string Path to the website build directory
	 */
	private $sitePathBuildDirectory = '';

	/**
	 *
	 * @var string This tells the site path where the build directory is
	 */
	private $buildType = '/build ';

	/**
	 *
	 * @var string Name of the website installation bash file
	 */
	private $installationType = '/installationd8';

	/**
	 *
	 * @var string Once the website has been built a url is created
	 */
	private $loginUrl = '';

	/**
	 *
	 * @var string New users email
	 */
	private $editorEmail = '';

	/**
	 *
	 * @var string New users email
	 */
	private $production = 0;

	/**
	 *
	 * @var string when installing adds it to the websites database name
	 */
	private $testPrefix = 'test_';

	/**
	 *
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->execShell = new BashExecute($app);
	}

	public function buildWebsiteStructure() {
		$this->app[feedback]->feedback = "<span>Creating website stucture</span>";

		$buildBashPath = $this->app['trace.config']->bashDirectory . "/buildd8"
			. ' ' . $this->app['trace.config']->websitesDirectory
			. ' ' . $this->app['trace.config']->siteName;

		$siteUrl = $this->app['trace.config']->tempWebsiteUrl;
		$this->app[feedback]->feedback = "<a href='" . $siteUrl . "' target='_blank'>See your new website</a>";

		//-- execute install file.
		$buildOutput = $this->execShell->executeShell($buildBashPath);

		//-- Get website paths and name
		$this->sitePathDirectory = $buildOutput[1];
		$this->sitePathBuildDirectory = $buildOutput[2] . '/build';

		//-- Get site name
		$this->websiteName();

		//-- Install website
		do {
			if (file_exists($this->sitePathBuildDirectory . '/sites/default/default.settings.php')) {
				$this->app[feedback]->feedback = "Read me file exits";

				$this->websiteSetup();
				$this->websiteInstallation();
				$this->websiteHostFile();
				$this->websiteVHostFile();

				break;
			}
		} while (0);


		foreach ($buildOutput as $buildOutputVal) {
			$this->app[feedback]->feedback = $buildOutputVal;
		}
	}

	/**
	 * 
	 */
	public function websiteName() {
		$siteArr = explode('/', $this->sitePathDirectory);
		$this->newWebsiteName = end($siteArr);
		$this->app[feedback]->feedback = "<span>The site name is $this->newWebsiteName</span>";
	}

	/**
	 *  Website setup finds the directory that has been allocated 
	 *  to the build and then creates the files and directories so that
	 *  drupal can be built using drush.
	 */
	public function websiteSetup() {

		$createFilesDir = $this->sitePathBuildDirectory . '/sites/default/files';
		mkdir($createFilesDir, 0777);
		chmod($createFilesDir, 0775);

		$buildPath = $this->sitePathBuildDirectory;

		$chown = $buildPath;
		$chownExec = 'chmod -Rf 777 ' . $chown;
		$this->execShell->executeShell($chownExec);


		$settings = $buildPath . '/sites/default/settings.php';
		$settingsDefault = $buildPath . '/sites/default/default.settings.php';
		copy($settingsDefault, $settings);
		chmod($settings, 0775); //-- needs to be set for d8 to install

		$settings = $buildPath . '/sites/default/services.yml';
		$settingsDefault = $buildPath . '/sites/default/default.services.yml';
		copy($settingsDefault, $settings);
		chmod($settings, 0775); //-- needs to be set for d8 to install

		$this->app[feedback]->feedback = "<span>$settings have been created</span>";
	}

	/**
	 * 
	 *  This installs the new website using the bash shell
	 */
	public function websiteInstallation() {

		$websiteType = $this->app['trace.config']->websiteType;
		$sitename = $this->newWebsiteName;
		$user = $this->app['trace.config']->siteUser;
		$password = $this->app['trace.config']->sitePassword;
		$dbUser = $this->app['trace.config']->databaseUser;
		$dbPass = $this->app['trace.config']->databasePassword;
		$buildPath = $this->sitePathBuildDirectory . '/';
		$email = $this->app['trace.config']->siteEmail;

		if ((int) $this->production === 0) {
			$sitename = $this->testPrefix . $sitename;
		}

		$siteVars = " $websiteType $sitename $user $password $dbUser $dbPass $buildPath $email";
		$install = $this->app['trace.config']->bashDirectory . $this->installationType . $siteVars;
		$this->app[feedback]->feedback('BuildAWebsiteD8Model', 'websiteInstallationCom', array($install));
		$execOutput = $this->execShell->executeShell($install);
		$this->app[feedback]->feedback('BuildAWebsiteD8Model', 'websiteInstallation', $execOutput);
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
			'database-name'=>'$SITENAME',
			);
		";
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
		$data = "<VirtualHost *:80>
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

		$firstBackup = $this->app['trace.config']->bashDirectory . "/dev/backup $SITENAME $DATABASEUSER $DATABASEPASSWORD  " . $SITEPATH . "databases/production/";

		//-- execute install file.
		$backupOutput = $this->execShell->executeShell($firstBackup);

		foreach ($backupOutput as $backupOutputVal) {
			$this->app[feedback]->feedback = $backupOutputVal;
		}

		$firstBuildBackup = $this->app['trace.config']->bashDirectory . "/backup-build $SITENAME $SITEPATHBUILD " . $SITEPATH . "backup-build/";

		//-- execute install file.
		$backupBuildOutput = $this->execShell->executeShell($firstBuildBackup);

		foreach ($backupBuildOutput as $backupBuildOutputVal) {
			$this->app[feedback]->feedback = $backupBuildOutputVal;
		}
		chmod($backupBuildOutput[1], 0775);
	}

	/**
	 * 
	 * Gets all the new users information and creates a new user in the build. Then adds them 
	 * to the role of editor and then creates a url so the user can access their new
	 * website and add their own password
	 */
	public function websiteEditor() {
		if ($this->editorEmail == '') {
			$USEREDITOR = $this->app['trace.config']->userEditor;
			$EMAILEDITOR = $this->app['trace.config']->emailEditor;
		} else {
			$userName = explode('@', $this->editorEmail);
			$USEREDITOR = $userName[0];
			$EMAILEDITOR = $this->editorEmail;
		}

		$PASSWORDEDITOR = $this->app['trace.config']->passwordEditor;
		$SITEPATH = $this->sitePathBuildDirectory . '/';


		$editor = $this->app['trace.config']->bashDirectory
			. '/editor ' . " $USEREDITOR $PASSWORDEDITOR $EMAILEDITOR $SITEPATH";

		$output = $this->execShell->executeShell($editor);
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteEditor', $output);
		$url = array_pop($this->app[feedback]->feedback);
		$urlEx = explode('/', $url);
		$this->loginUrl = $urlEx[3] . "/" . $urlEx[4] . "/" . $urlEx[5] . "/" . $urlEx[6] . "/" . $urlEx[7] . "/" . $urlEx[8];
	}

	/**
	 * 
	 * @return string Returns $this->newWebsite when class is extended
	 */
	public function getWebsite() {
		return $this->newWebsite;
	}

	/**
	 * 
	 * @return string Returns $this->newWebsiteName when class is extended
	 */
	public function getNewWebsiteName() {
		return $this->newWebsiteName;
	}

	/**
	 * 
	 * @return string Returns $this->sitePathDirectory when class is extended
	 */
	public function getSitePathDirectory() {
		return $this->sitePathDirectory;
	}

	/**
	 * 
	 * @return string Returns $this->sitePathBuildDirectory when class is extended
	 */
	public function getSitePathBuildDirectory() {
		return $this->sitePathBuildDirectory;
	}

	/**
	 * 
	 * @return string Returns $this->buildType when class is extended
	 */
	public function getBuildType() {
		return $this->buildType;
	}

	/**
	 * 
	 * @return string Returns  $this->installationType when class is extended
	 */
	public function getInstallationType() {
		return $this->installationType;
	}

	/**
	 * 
	 * @return string Returns
	 */
	public function getLoginUrl() {
		return $this->loginUrl;
	
	}

	/**
	 * 
	 * @param type $setProduction
	 */
	public function setProduction($setProduction) {
		$this->production = $setProduction;
	}

	/**
	 * 
	 * @param type $setProductionPrefix
	 */
	public function setProductionPrefix($setProductionPrefix) {
		$this->testPrefix = $setProductionPrefix;
	}

	/**
	 *  @return string
	 */
	public function __toString() {
		return "Trace\Model\BuildAWebsite";
	}

}
