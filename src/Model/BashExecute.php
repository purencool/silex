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


class BashExecute
{
  private $app;
  private $feedBack = array();


  public  function __construct($app)
  {
   $this->app = $app;
  }



  public function drushExecute($PATH, $COMMAND)
  {

    $drushCommand = $this->app['trace.config']->bashDirectory."/drush-command $PATH '$COMMAND'";


    $installationEsc   =  escapeshellcmd($drushCommand);
    print $installationEsc." <br/>";
    exec($installationEsc,$installOutput, $installReturn);

    foreach($installOutput as $installOutputVal){
      $this->feedBack[] = $installOutputVal;
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
