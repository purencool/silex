<?php
/**
 * Allowing the access of the systems shell directly or using  drush
 * so that the application can complete jobs that have been requested
 * by the user.
 * 
 * An example of this would be a new website needs to be build
 * using drush install through the exec()  could complete this
 * job 
 *
 * @package    **Trace**
 * @category   Trace Model
 * @author     Purencool Website Development
 * @license    GPL3
 *
 */

namespace Trace\Model;

class BashExecute {
	
   /**
    *
    * @var array of objects
    */
    private $app;
    
    /**
     *
     * @var array giving user feedback on what happened.
     */
    private $feedBack = array();

    /**
     * 
     * @param array $app injection of all the applications 
     * objects
     */
    public function __construct($app) {
        $this->app = $app;
    }

    /**
     *  This allows user chose a Drupal build and 
     *  then execute a drush command against it
     * 
     *  @example $path /real/path/to/Drupal/build
     *  @example $command **cc all** drush is added in script
     * 
     *  @param $path path to drupal build
     *  @param $command drush command user requested
     *  @return array of what was echo into the shell
     */
    public function drushExecute($path, $command) {
        
	//-- Preparing bash command
        $underScoreCommand = preg_replace('/\s+/', '_', $command);
        $drushCommand = $this->app['trace.config']->bashDirectory . "/drushCommand $path $underScoreCommand ";
	
	//-- Executing drush command
	$execOutput = NULL;
        $drushEsc = escapeshellcmd($drushCommand);
        exec($drushEsc, $execOutput);
	
	//-- Prepare feedback from execshell
        foreach ($execOutput as $drushFeedback) {
            $this->feedBack[] = $drushFeedback;
        }
	
        return $this->feedBack;
    }

    /**
     *  Allows users to request the systems shell to 
     *  achieve a cetain job request
     * 
     *  @param $command drush command user requested
     *  @return array of what was echo into the shell
     */
    public function executeShell($command) {
	    
        //-- Executes bash command 
        $shellEsc = escapeshellcmd($command);	
	$shellOutput = NULL;
        exec($shellEsc, $shellOutput);
	
	//-- Prepare feedback from execshell
        foreach ($shellOutput as $shellOutputVal) {
            $this->feedBack[] = $shellOutputVal;
        }
	
        return $this->feedBack;
    }

    /**
     *  @return string
     */
    public function __toString() {
        return "Model\BashExecute";
    }

}
