<?php
/**
 * Collects feedback from application and formats it into a multidimensional
 * array to be processed later
 *  
 * @package    **Trace**
 * @category   Trace Model
 * @author     Purencool Website Development
 * @license    GPL3
 *
 */

namespace Trace\Model;

class Feedback {

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
	 * @param Object Injection of all the applications objects
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->feedBack[] = $app['request']->getRequestUri();
	}

	/**
	 * 
	 * @param string $class Gets class name 
	 * @param string $method Gets method name
	 * @param array  $feedback This recieves the output being executed in the method
	 * @return array of output
	 */
	public function feedback($class, $method, $feedback) {
		$localFeedback = array();
		//-- Prepare feedback from execshell
		foreach ($feedback as $shellOutputVal) {
			$this->feedBack[$class][$method][] = $shellOutputVal;
			$localFeedback[] = $shellOutputVal;
		}

		return $localFeedback;
	}

	/**
	 * 
	 * @return array returns application feedback
	 */
	public function getFeedback() {

		return $this->feedBack;
	}

	/**
	 *  @return string
	 */
	public function __toString() {
		return "Model\Feedback";
	}

}
