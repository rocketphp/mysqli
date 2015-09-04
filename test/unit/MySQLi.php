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
        $mock = new MySQLiMock($this->config); 
        $this->assertInstanceOf('RocketPHP\\MySQLi\\MySQLi', $mock);
    }

    public function testConstructorSetsConfig()
    { 
        $mock = new MySQLiMock($this->config); 
        $this->assertSame($mock->getProtectedProperty('_config')['hostname'], $this->config['hostname']);
    }

    /**
     * @dataProvider             badConfigValues
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Expected string for hostname
     */
    public function testConstructorThrowsExceptionIfInvalidHostname($badValue)
    {  
        $mock = new MySQLiMock([
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
    public function testConstructorThrowsExceptionIfInvalidUsername($badValue)
    {  
        $mock = new MySQLiMock([
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
    public function testConstructorThrowsExceptionIfInvalidPassword($badValue)
    {  
        $mock = new MySQLiMock([
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
    public function testConstructorThrowsExceptionIfInvalidPort($badValue)
    {  
        $mock = new MySQLiMock([
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'rocketphp_mysqli_test_db',
            'port'     => $badValue
        ]);
    }
}