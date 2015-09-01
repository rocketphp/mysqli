<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/mysqli
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHP\MySQLi;

/** 
 * Interface for MySQLi objects
 */
interface MySQLiInterface
{
    public function connect();
    public function select($query, $whereValues, $whereFormat);
    public function update($table, $data, $format, $where, $whereFormat);
    public function updateMany($table, $column, $where, $data);
}
