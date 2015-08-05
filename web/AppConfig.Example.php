<?php

/**
 * App config is needed for trace to work
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */
class AppConfig {
	
	/* Web site build information*/
	public $endUrl = 'com';
	public $tempWebsiteUrl = 'http://domain.com/to-sub-dir-test-web-sites';
	public $tempWebsiteUrlProduction = 'http://domain.com/to-sub-dir-production-web-sites/';
	public $websiteType = 'standard';
	public $siteName = 'default-site-name';
	public $url = 'default-site-name';
	public $sitePassword = 'default-admin-password';
	public $siteUser = 'default-admin-user-name';
	public $siteEmail = 'default-admin-email';
	public $slider = 'none';

	
	
	
	/* Database access*/
	public $databaseUser = 'drush-database-access-user-name';
	public $databasePassword = 'drush-database-access-password';

	
	
	
	/* Drupal directory paths */
	public $buildDirectory = '/';
	public $siteDirectory = '/';
	public $bashDirectory = '/realpath-to-trace/build/web/bash';
	public $websitesDirectory = '/realpath-to-trace/test_sites';
	public $websitesDirectoryProduction = '/realpath-to-trace/production_sites';

	
	
	
	/* Editor user role variables*/
	public  $userEditor='editor-role-user-name';
        public  $passwordEditor='editor-role-password';
        public  $emailEditor='editor-role-password-email';
	
	
	
	
	/* Bash configuration */
	public  $browserWindow='- Google Chrome'; //-- Default browser set by developer
	public 	$composerPath='/realpath-to-composer/';
	public  $drushPath='/realpath-to-drush/';
        public  $rsyncPath='/realpath-to-rsync/';
	public  $watchSleep =0; //-- Used in refreshing browsers when compiling compass
	public	$md5 ='md5sum'; //--md5sum for linux  #md5; for mac (Not tested on mac)
	public  $chown='www-data:www-data'; //-- If null will default to system
	public  $permmissions='775'; //-- If null will default to system
}
