<?php

/**
 *
 * @package    
 * @category 
 * @author    
 * @license   
 */

namespace Trace\Protection;

class Protection {

    /**
     * 
     */
    function __construct() {
        
    }

    /**
     *  Test array values for XSS attacks
     *
     *  @param array $testArray variables that 
     *  need XSS testing
     *
     *  @return array that has XSS tested
     */
    function getXSSTest($testArray = array()) {
        $returnArray = array();

        foreach ($testArray as $testValue) {
          //  $returnArray[] = $app->escape($testValue);
        }

        return $returnArray;
    }

    /**
     *  
     *  @return string
     */
    public function __toString() {
        return "Protection\Protection";
    }

}
