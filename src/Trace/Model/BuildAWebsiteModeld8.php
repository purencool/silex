<?php
/**
 * The trace login form allows a user to access the backend
 * of the project to administer the sites needs. This form
 * displays across the entire web site where the user has
 * the ability to login at anytime.
 *
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */
namespace Trace\Model;

use Trace\Model\BashExecute;

class BuildAWebsiteModeld8
{
  private $newWebsiteName;
  private $app;
  private $feedBack = array();
  private $sitePathDirectory ='';
  private $sitePathBuildDirectory ='';
  private $execShell;
  /**
  *
  */
  public  function __construct($app)
  {
    $this->app = $app;
    $this->execShell = new BashExecute($app);
  }


  private function chmod_r($Path) {
   $dp = opendir($Path);
   while($File = readdir($dp)) {
    if($File != "." AND $File != "..") {
     if(is_dir($File)){
      chmod($File, 0755);
      chmod_r($Path."/".$File);
     }else{
      chmod($Path."/".$File, 0655);
     }
    }
   }
   closedir($dp);
  }


  public function buildWebsiteStructure()
  {
    $this->feedBack[] = "<span>Creating website stucture</span>";

    $buildBashPath = $this->app['trace.config']->bashDirectory."/buildd8"
    .' '.$this->app['trace.config']->websitesDirectory
    .' '.$this->app['trace.config']->siteName;

    //-- execute install file.
    $buildOutput = $this->execShell->executeShell($buildBashPath);

    //-- Get website paths and name
    $this->sitePathDirectory = $buildOutput[1];
    $this->sitePathBuildDirectory = $buildOutput[2].'/build';
    
    //-- Get site name
    $this->websiteName();

    //-- Install website
    do {
      if (file_exists($this->sitePathBuildDirectory.'/sites/default/default.settings.php')) {
       //print "testing to see";
       $this->feedBack[] = "Read me file exits";

       $this->websiteSetup();
       $this->websiteInstallation();

       break;
      }
    } while (0);

      //;
      //$this->websiteHostFile();
      //$this->websiteSettingsFile();
      //$this->websiteVHostFile();
      //$this->websiteBackup();


    //-- Output of exec
    foreach($buildOutput as $buildOutputVal){
      $this->feedBack[] = $buildOutputVal;
    }
  }
  
  /**
   * 
   */
  public function websiteName()
  {
    $siteArr = explode('/',$this->sitePathDirectory);
    $this->newWebsiteName = end($siteArr);
    $this->feedBack[] = "<span>The site name is $this->newWebsiteName</span>";
  }
  
  /**
   *  Website setup finds the directory that has been allocated 
   *  to the build and then creates the files and directories so that
   *  drupal can be built using drush.
   */
  public function websiteSetup()
  {

    $createFilesDir = $this->sitePathBuildDirectory.'/sites/default/files';
    mkdir($createFilesDir, 0777);
    chmod($createFilesDir, 0775);

    $buildPath = $this->sitePathBuildDirectory;

    $chown = $buildPath;
    $chownExec = 'chmod -Rf 777 '.$chown;
    $this->execShell->executeShell($chownExec);


    $settings = $buildPath.'/sites/default/settings.php';
    $settingsDefault = $buildPath.'/sites/default/default.settings.php';
    copy($settingsDefault,$settings);


    $settings = $buildPath.'/sites/default/services.yml';
    $settingsDefault = $buildPath.'/sites/default/default.services.yml';
    copy($settingsDefault,$settings);

    $this->feedBack[] = "<span>$settings have been created</span>";
  }
  
  
 /**
  * 
  */
  public function websiteInstallation()
  {

    $ENDURL = $this->app['trace.config']->endUrl;
    $WEBSITETYPE = $this->app['trace.config']->websiteType;
    $SITENAME =  $this->newWebsiteName;
    $USER = $this->app['trace.config']->siteUser;
    $PASSWORD = $this->app['trace.config']->sitePassword;
    $DATABASEUSER = $this->app['trace.config']->databaseUser;
    $DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
    $SITEPATHBUILD = $this->sitePathBuildDirectory.'/';
    $SITEPATH = $this->sitePathDirectory.'/';
    $EMAIL= $this->app['trace.config']->siteEmail;


    $siteInstall = $this->app['trace.config']->bashDirectory."/installationd8
     $WEBSITETYPE $SITENAME $USER $PASSWORD $DATABASEUSER $DATABASEPASSWORD
     $SITEPATHBUILD $EMAIL";

    //-- execute install file.
    foreach($this->execShell->executeShell($siteInstall) as $installVal){
      $this->feedBack[] = $installVal;
    }
  }

/**
 * 
 */
  public function websiteHostFile()
  {

    $ENDURL = $this->app['trace.config']->endUrl;
    $SITENAME =  $this->newWebsiteName;
    $SITEPATH = $this->sitePathDirectory.'/';
    $newHostName =  $SITENAME.'.'.$ENDURL.PHP_EOL;
    file_put_contents($SITEPATH.$newHostName, $newHostName, FILE_APPEND | LOCK_EX);

  }

/**
 * 
 */
  public function websiteSettingsFile()
  {
    $ENDURL = $this->app['trace.config']->endUrl;
    $WEBSITETYPE = $this->app['trace.config']->websiteType;
    $SITENAME =  $this->newWebsiteName;
    $USER = $this->app['trace.config']->siteUser;
    $PASSWORD = $this->app['trace.config']->sitePassword;
    $DATABASEUSER = $this->app['trace.config']->databaseUser;
    $DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
    $SITEPATHBUILD = $this->sitePathBuildDirectory.'/';
    $SITEPATH = $this->sitePathDirectory.'/';
    $EMAIL= $this->app['trace.config']->siteEmail;

    $newConfigName =  $SITENAME.'.php';
    $newConfigData =  "<?php
    \$appCurrentDevelopment = array(
      'current-build-path' =>'$SITEPATHBUILD',
      'username'=>'$USER',
      'password'=>'$PASSWORD',
      'database-user'=>'$DATABASEUSER',
      'database-password'=>'$DATABASEPASSWORD',
      'database-name'=>'$SITENAME',
    );
    ";
    file_put_contents($SITEPATH.$newConfigName, $newConfigData, FILE_APPEND | LOCK_EX);
  }

 /**
  * 
  */
  public function websiteVHostFile()
  {
    $ENDURL = $this->app['trace.config']->endUrl;
    $WEBSITETYPE = $this->app['trace.config']->websiteType;
    $SITENAME =  $this->newWebsiteName;
    $USER = $this->app['trace.config']->siteUser;
    $PASSWORD = $this->app['trace.config']->sitePassword;
    $DATABASEUSER = $this->app['trace.config']->databaseUser;
    $DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
    $SITEPATHBUILD = $this->sitePathBuildDirectory.'/';
    $SITEPATH = $this->sitePathDirectory.'/';
    $EMAIL= $this->app['trace.config']->siteEmail;

    $apacheHostFile = $SITEPATH.$SITENAME.'.'.$ENDURL.'.conf';
    $apacheHostData ="
    <VirtualHost *:80>
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
    </VirtualHost>
    ";
    file_put_contents($apacheHostFile, $apacheHostData, FILE_APPEND | LOCK_EX);
  }

  /**
   * 
   */
  public function websiteBackup()
  {
    $ENDURL = $this->app['trace.config']->endUrl;
    $WEBSITETYPE = $this->app['trace.config']->websiteType;
    $SITENAME =  $this->newWebsiteName;
    $USER = $this->app['trace.config']->siteUser;
    $PASSWORD = $this->app['trace.config']->sitePassword;
    $DATABASEUSER = $this->app['trace.config']->databaseUser;
    $DATABASEPASSWORD = $this->app['trace.config']->databasePassword;
    $SITEPATHBUILD = $this->sitePathBuildDirectory.'/';
    $SITEPATH = $this->sitePathDirectory.'/';
    $EMAIL= $this->app['trace.config']->siteEmail;


    chmod($SITEPATH.'databases',0775);
    chmod($SITEPATH.'databases/production', 0777);
    chmod($SITEPATH.'databases/testing', 0777);
    chmod($SITEPATH.'backup-build', 0777);


    $firstBackup = $this->app['trace.config']->bashDirectory."/dev/backup $SITENAME $DATABASEUSER $DATABASEPASSWORD  ".$SITEPATH."databases/production/";

    //-- execute install file.
    $backupOutput = $this->execShell->executeShell($firstBackup);

    foreach($backupOutput as $backupOutputVal){
      $this->feedBack[] = $backupOutputVal;
    }
    //chmod($backupOutput[0], 0775);
    //chmod($backupOutput[1], 0775);



    $firstBuildBackup = $this->app['trace.config']->bashDirectory."/backup-build $SITENAME $SITEPATHBUILD ".$SITEPATH."backup-build/";

    //-- execute install file.
    $backupBuildOutput = $this->execShell->executeShell($firstBuildBackup);

    foreach($backupBuildOutput as $backupBuildOutputVal){
      $this->feedBack[] = $backupBuildOutputVal;
    }
    chmod($backupBuildOutput[1], 0775);
  }

  /**
   * 
   * @return string
   */
  public function feedBack()
  {
    return $this->feedBack;
  }


  /**
   * @return object
   */
  public function  getWebsite()
  {
     return $this->newWebsite;
  }

  /**
   *  @return string
   */
  public function __toString()
  {
    return "Model\BuildAWebsite";
  }
}
