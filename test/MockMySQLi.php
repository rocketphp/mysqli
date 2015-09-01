<?php namespace RocketPHPTest\MySQLi;
use RocketPHP\MySQLi\MySQLi;
/** 
 * Mock MySQLi
 *
 */ 
class MockMySQLi
extends MySQLi
{
    public function getProtectedProperty($name)
    {
        return $this->{$name};
    }
}