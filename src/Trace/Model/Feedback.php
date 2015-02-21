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
	 * @var array Array of objects
	 */
	private $app;

	/**
	 *
	 * @var array giving user feedback on what happened during the request.
	 */
	private $feedBack = array();

	/**
	 *  
	 * @param Object $app Injection of all the applications shared objects
	 */
	public function __construct($app) {
		$this->app = $app;
		$this->feedBack[] = $app['request']->getRequestUri();
	}

	/**
	 * 
	 * @param  string $class Class gets the class name the feeback request
	 * @param  string $method Method gets the method name the feeback request
	 * @param  array  $feedback Feedback receives the output being executed in the method
	 * @return array  Array of what was echo into the shell that is formated
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
	 * @return array  Returns the complete array of the applications 
	 * multidimensional feedback
	 */
	public function getFeedback() {

		return $this->feedBack;
	}

	/**
	 *  @return string feedback
	 */
	public function __toString() {
		return "Model\Feedback";
	}

}
