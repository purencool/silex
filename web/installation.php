<?php
   $websiteDetails = array('name'=>'purencooltests');
   $buildToolsPath = "./bash/";
   $buildBashPath = $buildToolsPath.'build '.$websiteDetails['name'];
   print $buildBashPath;
   $buildEsc   =  escapeshellcmd($buildBashPath);
   exec($buildEsc,$buildOutput, $buildReturn);
   print "<h1>Build</h1>";
   print_r($buildOutput);
   $siteNameAvailable = explode('/',$buildOutput[1]);
   echo "<br/>";
   print_r($siteNameAvailable);

   /**
   * Making sure the installation is complete
   */
  $filename = $buildOutput[2].'/sites/all/themes/mothership/README.txt';
   print "<br/>".$filename."<br/>";
   //while (!file_exists($filename)) sleep(1);

   if(file_exists($filename)){
     print "<h1>Installation</h1>";
     $ENDURL='dev';
     $WEBSITETYPE='standard';
     $SITENAME =  end($siteNameAvailable);
     $USER='admin';
     $PASSWORD='q';
     $DATABASEUSER='root';
     $DATABASEPASSWORD='qzxcvbnm';
     $SITEPATH=$buildOutput[2].'/';
     $SITEPATHBUILD=$buildOutput[1].'/';
     $EMAIL='purencool@gmail.com';


     $createFilesDir = $SITEPATH.'sites/default/files';
     mkdir($createFilesDir, 0777);
     chmod($createFilesDir, 0775);


     $settings = $SITEPATH.'sites/default/settings.php';
     $settingsDefault = $SITEPATH.'sites/default/default.settings.php';
     copy($settingsDefault,$settings);
     chmod($settings ,0775);
     print $settings.'<br/>';


     $siteInstallation = $buildToolsPath."installation $WEBSITETYPE $SITENAME $USER $PASSWORD $DATABASEUSER $DATABASEPASSWORD $SITEPATH $EMAIL";
     echo $siteInstallation."<br/>";
     $installationEsc   =  escapeshellcmd($siteInstallation);
     exec($installationEsc,$installOutput, $installReturn);

     print_r($installOutput);



     print "<h1>Create Host File</h1>";
     $newHostName =  $SITENAME.'.'.$ENDURL.PHP_EOL;
     file_put_contents($SITEPATHBUILD.$newHostName, $newHostName, FILE_APPEND | LOCK_EX);

     print "<h1>Create Settings File</h1>";
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
     file_put_contents($SITEPATHBUILD.$newConfigName, $newConfigData, FILE_APPEND | LOCK_EX);

     print "<h1>Create Host File</h1>";
     print $SITENAME.'.'.$ENDURL;
     $apacheHostFile = $SITEPATHBUILD.$SITENAME.'.'.$ENDURL.'.conf';
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


    print "<h1>Creating Git Repo</h1>";
    $git = 'rm -Rf '.$SITEPATH.'.git';
    exec($git, $gitOutput, $gitReturn);
    print_r($gitOutput);


    $gitInit = 'git init '.$SITEPATH;
    exec($gitInit, $gitInitOutput, $gitInitReturn);
    print_r($gitInitOutput);

    $gitAdd = "cd $SITEPATH && git add .";
    print $gitAdd.'<br/>';
    exec($gitAdd, $gitAddOutput, $gitAddReturn);
    print_r($gitAddOutput);

    //--- not working
    //$gitCommit = "cd $SITEPATH && git commit -m 'Inital Commit'";
    //print $gitCommit.'<br/>';
    //exec($gitCommit, $gitCommitOutput, $gitCommitReturn);
    //print_r($gitCommitOutput);


    print "<h1>Creating first backup</h1>";
    chmod($SITEPATHBUILD.'databases',0775);
    chmod($SITEPATHBUILD.'databases/production', 0777);
    chmod($SITEPATHBUILD.'databases/testing', 0777);
    chmod($SITEPATHBUILD.'backup-build', 0777);


    $firstBackup = $buildToolsPath."backup $SITENAME $DATABASEUSER $DATABASEPASSWORD  ".$SITEPATHBUILD."databases/production/";
    exec($firstBackup, $backupOutput, $backupReturn);
    print_r($backupOutput);
    chmod($backupOutput[0], 0775);
    chmod($backupOutput[1], 0775);



    $firstBuildBackup = $buildToolsPath."backup-build $SITENAME $SITEPATH ".$SITEPATHBUILD."backup-build/";
    exec($firstBuildBackup, $backupBuildOutput, $backupBuildReturn);
    print_r($backupBuildOutput);
    chmod($backupBuildOutput[1], 0775);



    print "<h1>Set final permissons</h1>";
    $permissions = "chown -Rf work:www-data  ".$SITEPATHBUILD;
    print $permissions;
    exec($permissions, $permissionsOutput, $permissionReturn);
    print_r($permissionsOutput);

    print "<h1>Uninstalled Modules</h1>";
    $uninstalledModules = $buildToolsPath.'uninstalled-modules '.$SITEPATH;
    print $uninstalledModules;
    exec($uninstalledModules,$uninstalledModulesOutput, $uninstalledModulesReturn);
    print_r($uninstalledModulesOutput);

   } else {
     echo "won't be install it soon<br/>";
   }
 ?>
