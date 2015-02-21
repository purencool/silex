<?php
/**
 * Automatically creates files that are needed on a virtual host
 * 
 *
 * @package    **Trace**
 * @category   Trace Model
 * @author     Purencool Website Development
 * @license    GPL3
 *
 */

namespace Trace\Model;

class BuildAWebsiteBaseFilesModel {

	/**
	 *
	 * @var array Array of objects
	 */
	private $app;

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
	 * @param Object $app Injection of all the applications shared objects
	 * @param string $newWebsiteName New websites name
	 * @param string $sitePathDirectory Path to the website directory
	 * @param string $sitePathBuildDirectory  Path to the website build directory
	 */
	public function __construct($app, $newWebsiteName, $sitePathDirectory, $sitePathBuildDirectory) {
		$this->app = $app;
		$this->newWebsiteName = $newWebsiteName;
		$this->sitePathDirectory = $sitePathDirectory;
		$this->sitePathBuildDirectory = $sitePathBuildDirectory;
		$this->websiteHostFile();
		$this->websiteSettingsFile();
		$this->websiteVHostFile();
	}

	/**
	 *  
	 *  Creation of host file with host name inside
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
	 *  Creation of all relevant information needed for moving website build
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
	 *  Creation of Apache 2.4 host file
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
	 *  @return string feedback
	 */
	public function __toString() {
		return "Model\BuildAWebsiteBaseFilesModel";
	}
}
