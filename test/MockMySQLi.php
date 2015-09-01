<?php 
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/mysqli
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHPTest\MySQLi;

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