<?php
/**
 *
 * @package    
 * @category 
 * @author    
 * @license   
 */
namespace Protection;


class Protection
{
   
  /**
   * 
   */
   function __construct() 
   {
   
   }
   
  /**
   *  Test array values for XSS attacks
   *
   *  @param array $array variables that 
   *  need XSS testing
   *
   *  @return array that has XSS tested
   */
   function getXSSTest($testArray = array())
   {
     $returnArray = array();
     
     foreach ($testArray as $testValue)
     {
       $returnArray[] = $app->escape($testValue);
     }
     
     return $returnArray;
   }
   
  /**
   *  
   *  @return string
   */
   public function __toString()
   {
     return "You have access Protection\Protection";
   }
}
