<?php 
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/mysqli
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHPTest\MySQLi;

/**
 * @group RocketPHP_MySQLi
 */ 
class MySQLi_UnitTest
extends MySQLiTestCase
{

    public function testConstructor()
    {
        $mock = new MockMySQLi($this->config); 
        $this->assertInstanceOf('RocketPHP\\MySQLi\\MySQLi', $mock);
    }

    public function testConstructorSetsConfig()
    { 
        $mock = new MockMySQLi($this->config); 
        $this->assertSame($mock->getProtectedProperty('_config')['hostname'], $this->config['hostname']);
    }

    /**
     * @dataProvider             badConfigValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected string for hostname
     */
    public function testConstructorInvalidHostname($badValue)
    {  
        $mock = new MockMySQLi([
            'hostname' => $badValue,
            'username' => 'root',
            'password' => '',
            'database' => 'rocketphp_mysqli_test_db',
            'port'     => null
        ]);
    }

    /**
     * @dataProvider             badConfigValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected string for username
     */
    public function testConstructorInvalidUsername($badValue)
    {  
        $mock = new MockMySQLi([
            'hostname' => 'localhost',
            'username' => $badValue,
            'password' => '',
            'database' => 'rocketphp_mysqli_test_db',
            'port'     => null
        ]);
    }

    /**
     * @dataProvider             badConfigValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected string for password
     */
    public function testConstructorInvalidPassword($badValue)
    {  
        $mock = new MockMySQLi([
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => $badValue,
            'database' => 'rocketphp_mysqli_test_db',
            'port'     => null
        ]);
    }

    /**
     * @dataProvider             badConfigPortValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected null|(positive)int for port
     */
    public function testConstructorInvalidPort($badValue)
    {  
        $mock = new MockMySQLi([
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'rocketphp_mysqli_test_db',
            'port'     => $badValue
        ]);
    }
}