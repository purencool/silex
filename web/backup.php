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
$SITENAME=$appCurrentDevelopment['database-name'];
$DATABASEUSER=$appCurrentDevelopment['database-user'];
$DATABASEPASSWORD=$appCurrentDevelopment['database-password'];
$SITEPATHBUILD=$appCurrentDevelopment['current-build-path'].'databases/production/';


$firstBackup = "./backup $SITENAME $DATABASEUSER $DATABASEPASSWORD $SITEPATHBUILD";
exec($firstBackup, $backupOutput, $backupReturn);
print_r($backupOutput);
chmod($backupOutput[0], 0775);
