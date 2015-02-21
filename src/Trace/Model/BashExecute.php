<?php
/**
 * Allowing the access of Bash commands to the Systems shell so 
 * that the application can complete jobs that have been requested
 * by the user.
 * 
 * An example of this would be a new website needs to be build
 * using **drush install** , exec() could complete this request.
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
    * @var array Array of objects
    */
    private $app;
    

    /**
     * 
     * @param Object $app Injection of all the applications shared objects
     */
    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * 
     *  This allows user choose a Drupal build and 
     *  then execute a Drush command against it
     * 
     *  @example $path /real/path/to/Drupal/build
     *  @example $command **cc all** drush is added in script
     * 
     *  @param  string $path Path to Drupal build
     *  @param  string $command Drush command the user requested to be executed
     *  @return array Array of what was echo into the shell
     */
    public function drushExecute($path, $command) {
       
	//-- Preparing bash command
        $underScoreCommand = preg_replace('/\s+/', '_', $command);
        $drushCommand = $this->app['trace.config']->bashDirectory . "/drushCommand $path $underScoreCommand ";
	
	//-- Executing drush command
	$execOutput = NULL;
        $drushEsc = escapeshellcmd($drushCommand);
        exec($drushEsc, $execOutput);
	
	
        return $this->app[feedback]->feedback('drushExecute', 'drushExecute', $execOutput);
    }

    /**
     * 
     *  Allows users to request the systems shell to 
     *  achieve a cetain bash executable or command to be completed
     * 
     *  @param  string $command Bash command requested to be executed
     *  @return array Array of what was echo into the shell
     */
    public function executeShell($command) {
	    
        //-- Executes bash command 
        $shellEsc = escapeshellcmd($command);	
	$shellOutput = NULL;
        exec($shellEsc, $shellOutput);
	
	return $this->app[feedback]->feedback('drushExecute', 'executeShell', $shellOutput);
	
    }

    /**
     *  @return string
     */
    public function __toString() {
        return "Model\BashExecute";
    }

}
