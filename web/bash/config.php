<?php
/**
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 */

/**
 *  @var Global variable that is set by bash
 */
$globalRequest = $GLOBALS['argv'][1];
if($globalRequest === 'appGlobals') {
	print_r( $GLOBALS['argv']);
}
/**
 *  Accessing the application config object
 */

//$path = getcwd ()."/AppConfig.php";
$path = __DIR__.'/../AppConfig.php';
include $path;
$new = new AppConfig();


/**
 * return requested varible for bash system
 */
if($globalRequest === 'browser') {
	print $new->browserWindow;
} elseif ($globalRequest === 'appConfigPath') {
	print $path;
} elseif ($globalRequest === 'drushPath') {
	echo $new->drushPath;
} elseif ($globalRequest === 'rsyncPath') {
	print $new->rsyncPath;
}  elseif ($globalRequest === 'watchSleep') {
	print $new->watchSleep;
}  elseif ($globalRequest === 'md5') {
	print $new->md5;
}  elseif ($globalRequest === 'chown') {
	print $new->chown;
}  elseif ($globalRequest === 'permmissions') {
	print $new->permmissions;
} else {
	print 'system cannot find obj';
}
