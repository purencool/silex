<?php
/**
 *  Accessing the excute shell
 *
 * @package    Trace
 * @category
 * @author     Purencool Website Development
 * @license    GPL3
 *
 */
namespace Model;


class BashExecute
{
  private $app;
  private $feedBack = array();


  public  function __construct($app)
  {
   $this->app = $app;
  }

 /**
  *  @param $path path to drupal build
  *  @param $command drush command user requested
  *  @return array of what was echo into the shell
  */
  public function drushExecute($path, $command)
  {
    $underScoreCommand = preg_replace('/\s+/', '_', $command);
    $drushCommand = $this->app['trace.config']->bashDirectory."/drush-command $path $underScoreCommand ";
    $drushEsc = escapeshellcmd($drushCommand);
    exec($drushEsc,$drushOutput, $drushReturn);
    foreach($drushOutput as $drushOutputVal){
      $this->feedBack[] = $drushOutputVal;
    }
    return $this->feedBack;
  }


  /**
  *  @param $command drush command user requested
  *  @return array of what was echo into the shell
  */
  public function executeShell($command)
  {

   $shellEsc = escapeshellcmd($command);
   exec($shellEsc,$shellOutput, $shellReturn);

   foreach($shellOutput as $shellOutputVal){
    $this->feedBack[] = $shellOutputVal;
   }
   return $this->feedBack;
  }


  /**
   *  @return string
   */
  public function __toString()
  {
    return "Model\BashExecute";
  }
}
