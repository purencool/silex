<?php 
/**
 * Access Json arrays for the rest api
 *
 * @package    Trace
 * @category   
 * @author     Purencool Website Development
 * @license    GPL3
 */
namespace Controller;


class JsonController
{
  /**
   *  This controller gets a php array and returns a 
   *  json array that is routed to /todo-json.
   *
   *  @return json array 
   */
    public function getTodoJsonAction()
    {
     return json_encode(\Tests\JsonPhpArrayTest::getJsonToDoData());
    }
 }
