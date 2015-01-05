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
namespace Model;

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Exec;

class BuildAWebsiteModeld8
{
  private $newWebsiteName;
  private $app;
  private $feedBack = array();
  private $sitePathDirectory ='';
  private $sitePathBuildDirectory ='';
  /**
  *
  */
  public  function __construct($app)
  {
   $this->app = $app;
  }

  public function testShell()
  {
    $shell = new Exec();
    $command = new Command('echo');
    $command->addParam(new Param('Hello World'));
    $this->feedBack[] = $shell->run($command);
  }


  public function buildWebsiteStructure()
  {
    //$bashBuildScript = $this->app['trace.config']->bashDirectory."/build";
    //$this->feedBack[] = $bashBuildScript;
    //$siteDirectory =  $this->app['trace.config']->siteName;
    //$this->feedBack[] = $siteDirectory;
    //$websitesPathDirectory =  $this->app['trace.config']->websitesDirectory;
    //$this->feedBack[] = $websitesPathDirectory;
    //$buildBashPath = $bashBuildScript.' '.$websitesPathDirectory.' '.$siteDirectory;
    //$this->feedBack[] = $buildBashPath;
    $this->feedBack[] = "<span>Creating website stucture</span>";

    $buildBashPath = $this->app['trace.config']->bashDirectory."/buildd8"
    .' '.$this->app['trace.config']->websitesDirectory
    .' '.$this->app['trace.config']->siteName;

    $buildEsc   =  escapeshellcmd($buildBashPath);
    exec($buildEsc,$buildOutput, $buildReturn);

    //-- Get website paths and name
    $this->sitePathDirectory = $buildOutput[1];
    $this->sitePathBuildDirectory = $buildOutput[2];


    //-- Get site name
    $this->websiteName();
    $this->websiteSetup();
    $this->websiteInstallation();

    //-- Install website
    /*
    if(file_exists($this->sitePathBuildDirectory.'/sites/all/themes/mothership/README.txt')){
      $this->feedBack[] = "Read me file exits";
      $this->websiteInstallation();
      $this->websiteHostFile();
      $this->websiteSettingsFile();
      $this->websiteVHostFile();
      $this->websiteBackup();

    } else {
      $this->feedBack[] = "Read me file does not exit";
    }
*/

    foreach($buildOutput as $buildOutputVal){
      $this->feedBack[] = $buildOutputVal;
    }
  }

  public function websiteName()
  {
    $sitArr = explode('/',$this->sitePathDirectory);
    $this->newWebsiteName = end($sitArr);
    $this->feedBack[] = "<span>The site name is $this->newWebsiteName</span>";
  }

  public function websiteSetup()
  {
   //-- Files directory
   $createFilesDir = $this->sitePathBuildDirectory.'/sites/default/files';
   mkdir($createFilesDir, 0777);
   chmod($createFilesDir, 0775);

   $this->feedBack[] = "<span>$createFilesDir have been created</span>";


    //-- Services yml file
    $settingsYaml = $this->sitePathBuildDirectory.'/sites/default/services.yml';
    $settingsDefaultYaml = $this->sitePathBuildDirectory.'/sites/default/default.services.yml';
    copy($settingsDefaultYaml,$settingsYaml);
    chmod($settingsYaml ,0775);

    $this->feedBack[] = "<span>$settingsYaml have been created</span>";


    //-- Settings file
    $settings = $this->sitePathBuildDirectory.'/sites/default/settings.php';
    $settingsDefault = $this->sitePathBuildDirectory.'/sites/default/default.settings.php';
    copy($settingsDefault,$settings);
    chmod($settings ,0775);

    //$rmGit = $this->sitePathBuildDirectory."/.git";
    //rmdir($rmGit)

     chmod($this->sitePathBuildDirectory.'/modules', 0775);

    $this->feedBack[] = "<span>$settings have been created</span>";
  }

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


    $siteInstallation = $this->app['trace.config']->bashDirectory."/installation-drupal-eight $WEBSITETYPE $SITENAME $USER $PASSWORD $DATABASEUSER $DATABASEPASSWORD $SITEPATHBUILD $EMAIL";
    $installationEsc   =  escapeshellcmd($siteInstallation);
    exec($installationEsc,$installOutput, $installReturn);

    foreach($installOutput as $installOutputVal){
      $this->feedBack[] = $installOutputVal;
    }
  }


  public function websiteHostFile()
  {

    $ENDURL = $this->app['trace.config']->endUrl;
    $SITENAME =  $this->newWebsiteName;
    $SITEPATH = $this->sitePathDirectory.'/';
    $newHostName =  $SITENAME.'.'.$ENDURL.PHP_EOL;
    file_put_contents($SITEPATH.$newHostName, $newHostName, FILE_APPEND | LOCK_EX);

  }


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
    exec($firstBackup, $backupOutput, $backupReturn);
    foreach($backupOutput as $backupOutputVal){
      $this->feedBack[] = $backupOutputVal;
    }
    //chmod($backupOutput[0], 0775);
    //chmod($backupOutput[1], 0775);



    $firstBuildBackup = $this->app['trace.config']->bashDirectory."/backup-build $SITENAME $SITEPATHBUILD ".$SITEPATH."backup-build/";
    exec($firstBuildBackup, $backupBuildOutput, $backupBuildReturn);
    foreach($backupBuildOutput as $backupBuildOutputVal){
      $this->feedBack[] = $backupBuildOutputVal;
    }
    chmod($backupBuildOutput[1], 0775);
  }

  public function feedBack()
  {
    return $this->feedBack;
  }


  /**
   * [getWebsite description]
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
