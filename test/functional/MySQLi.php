<?php 
namespace RocketPHPTest\MySQLi;
/**
 * @group RocketPHP_MySQLi
 */ 
class MySQLi_Functional
extends MySQLiTestCase
{

    public function testConstructor()
    {
        $mysqli = new MockMySQLi($this->config); 
        $this->assertInstanceOf('RocketPHP\\MySQLi\\MySQLi', $mysqli);
    }
}