<?php namespace RocketPHPTest\MySQLi;
use RocketPHP\MySQLi\MySQLi;
/** 
 * MySQLi Test Case
 *
 */ 
abstract class MySQLiTestCase
extends \PHPUnit_Framework_TestCase
{
	public $config = [
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'rocketphp_mysqli_test_db',
		'port'     => null,
	];

    public function badConfigValues()
    {
		return [
			[null],
			[-1],
			[1],
			[1.5],
			[true],
			[false],
			[array()]
		];
    }

    public function badConfigPortValues()
    {
		return [
			[-1],
			[0],
			[1.5],
			[true],
			[false],
			['string'],
			[array()]
		];
    }

    public function setUp()
    {
    }

    public function tearDown()
    { 
    }
}