<?php
/**
 * Test php array so it can be converted to a json array
 *
 * @package    Trace
 * @category   
 * @author     Purencool Website Development
 * @license    GPL3
 */

namespace Tests;

class JsonPhpArrayTest 
{

    /**
     *  This array can be use to test the json 
     *  converstion.
     *  
     *  @return  array
     *  
     */
    public static function getJsonToDoData() {
        /* @array $testPhpArrayData */
        $testPhpArrayData = "";
        include_once "testPhpArrayData.php";
        
        return $testPhpArrayData;
    }
}
