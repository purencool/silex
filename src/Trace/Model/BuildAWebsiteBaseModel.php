<?php
/**
 *  The reason of this class is to create a basic Drupal website using drush 
 *  as the build tool. This class to adds an abstraction that will allow 
 *  users to create a meaningful website with little effort.
 *
 * @package    **Trace**
 * @category   Trace Model
 * @author     Purencool Website Development
 * @license    GPL3
 *
 */

namespace Trace\Model;

class BuildAWebsiteBaseModel {

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
	private $installationType = '/installation ';

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
	 * @param Object $app Injection of all the applications shared objects
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->execShell = new \Trace\Model\BashExecute($app);
	}

	/**
	 * 
	 * Sets up build method
	 * @see BuildAWebsiteBaseModel::websiteSetup()
	 * @see BuildAWebsiteBaseModel::websiteInstallation()
	 * @see BuildAWebsiteBaseFilesModel
	 * @see BuildAWebsiteBaseModel::websiteBackup()
	 */
	protected function buildMethods() {
		$this->websiteSetup();
		$this->websiteInstallation();
		$sF = new BuildAWebsiteBaseFilesModel($this->app, $this->newWebsiteName, $this->sitePathDirectory, $this->sitePathBuildDirectory);
		$sF->__toString(); // @todo removes ide error must be a better way
		$this->websiteBackup();
	}

	/**
	 * 
	 *  Creates the websites name
	 */
	private function websiteName() {
		$sitArr = explode('/', $this->sitePathDirectory);
		$this->newWebsiteName = end($sitArr);
	}

	/**
	 * 
	 *  The collection of information and the building of the website structure
	 *  is needed before we can go to installation. This method does the following,
	 *  1. Receives new user email
	 *  2. If no website name or email is found it will default
	 *  3. Creates and formats strings that will be used throughtout the website
	 *  4. Goes to the bash shell and creates the directory structure
	 *  5. Sets up website name
	 *  6. Tests to see if all files have been downloaded
	 *  7. Starts the installatiom process
	 * 
	 * @param string $websiteName Gets users sitename
	 * @param string $editorEmail Gets new users email
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
			$newsite = strtolower(str_replace("'", '', $newstr));
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
	 *  Installatiom of new website
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
		$siteVars = " $websiteType $sitename $user $password $dbUser $dbPass $buildPath $email";

		$install = $this->app['trace.config']->bashDirectory . $this->installationType . $siteVars;

		$execOutput = $this->execShell->executeShell($install);
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteInstallation', $execOutput);
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
		$feedback = $this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteEditor', $output);
		$url = array_pop($feedback);
		$urlEx = explode('/', $url);
		$this->loginUrl = $urlEx[3] . "/" . $urlEx[4] . "/" . $urlEx[5] . "/" . $urlEx[6] . "/" . $urlEx[7] . "/" . $urlEx[8];
	}

	/**
	 * 
	 * For larger new installations this backups up all the code and database 
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
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteBackup', $backupOutput);


		$firstBuildBackup = $this->app['trace.config']->bashDirectory
			. "/backupBuild $SITENAME $SITEPATHBUILD "
			. $SITEPATH . "backup-build/";

		$backupBuildOutput = $this->execShell->executeShell($firstBuildBackup);
		$this->app[feedback]->feedback('BuildAWebsiteBaseModel', 'websiteBackup', $backupBuildOutput);
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
	 *  @return string BuildAWebsiteBaseModel
	 */
	public function __toString() {
		return "Model\BuildAWebsiteBaseModel";
	}

}
