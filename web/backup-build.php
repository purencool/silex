<?php
 include './current-site.php';

/*
 $appCurrentDevelopment = array(
         'current-build-path' =>'/var/www/html/test/purencooltests12345678910111213141516171819202122232425/',
         'username'=>'admin',
         'password'=>'q',
         'database-user'=>'root',
         'database-password'=>'qzxcvbnm',
         'database-name'=>'purencooltests12345678910111213141516171819202122232425',
       );


*/
$SITENAME = $appCurrentDevelopment['database-name'];
$SITEPATHBUILD = $appCurrentDevelopment['current-build-path'].'build/';
$SITEPATHBACKUP = $appCurrentDevelopment['current-build-path']."backup-build/";

$firstBuildBackup = "./backup-build $SITENAME $SITEPATHBUILD  $SITEPATHBACKUP";
exec($firstBuildBackup, $backupBuildOutput, $backupBuildReturn);
chmod($backupBuildOutput[1], 0775);
