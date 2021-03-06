<?php
/**
 * RocketPHP (http://rocketphp.io)
 *
 * @package   RocketPHP
 * @link      https://github.com/rocketphp/mysqli
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace RocketPHP\MySQLi;

use InvalidArgumentException;
use RuntimeException;

/** 
 * MySQLi: Prepared statements - implements MySQLi client.
 *
 * Use MySQLi when you want to perform queries on a MySQL database.
 */
class MySQLi
implements MySQLiInterface
{
    /** 
     * Connection config
     * @access protected
     * @var    array
     */
    protected $_config;  

    /** 
     * Constructor
     *
     * @param array $config Connection config
     */
    public function __construct(array $config)
    {
        $this->_config['hostname'] = isset($config['hostname']) 
                                            ? $config['hostname'] 
                                            : null;
        $this->_config['username'] = isset($config['username']) 
                                            ? $config['username'] 
                                            : null;
        $this->_config['password'] = isset($config['password']) 
                                            ? $config['password'] 
                                            : null;
        $this->_config['database'] = isset($config['database']) 
                                            ? $config['database'] 
                                            : null;
        $this->_config['port']     = isset($config['port']) 
                                            ? $config['port'] 
                                            : null;

        foreach ($this->_config as $key => $value) {
            if ($key == 'port') {
                // check null|(positive)int
                if(!is_null($value) && !is_int($value) 
                    || is_int($value) && $value < 1)
                    throw new InvalidArgumentException(
                        "Expected null|(positive)int for $key.", 
                        1
                    );
            } else {       
                // check string
                if(is_null($value) || !is_string($value))
                    throw new InvalidArgumentException(
                        "Expected string for $key.", 
                        1
                    );
            }
        }
    }

    /**
     * Destructor
     *
     */
    public function __destruct()
    {
    }

    /** 
     * Connect
     *
     * @return \mysqli
     */
    public function connect()
    {
        if (is_null($this->_config['port'])) {
            try {
                $conn = new \mysqli(
                    $this->_config['hostname'], 
                    $this->_config['username'], 
                    $this->_config['password'], 
                    $this->_config['database']
                ); 
            } catch (Exception $e) {
                throw new RuntimeException(
                    "Failed to connect to MySQL:",
                    1
                );    
            }
        } else {
            try {
                $conn = new \mysqli(
                    $this->_config['hostname'], 
                    $this->_config['username'], 
                    $this->_config['password'], 
                    $this->_config['database'], 
                    (int) $this->_config['port']
                );
                
            } catch (Exception $e) {
                throw new RuntimeException(
                    "Failed to connect to MySQL:",
                    1
                );                
            }
        }
        // @codingStandardsIgnoreStart
        if ($conn->connect_errno) { 
            throw new RuntimeException(
                "Failed to connect to MySQL: 
                (" . $conn->connect_errno . ") " . $conn->connect_error,
                1
            );
        }
        // @codingStandardsIgnoreEnd
        return $conn;
    }

    /** 
     * Select row
     *
     * @param  string $query MySQL prepared statement query.
     * @param  array  $values Where values (E.g: ('1', '2')).
     * @param  array  $fmt Where format (E.g: ('%i%', '%s%')).
     * @return array
     */
    public function select($query, $values, $fmt)
    { 
        // connect
        $conn = $this->connect(); 
        // check existing records
        $stmt = $conn->prepare($query); 
        if ($stmt == false) {
            throw new RuntimeException(
                "MySQLi Error: Failed to prepare query: " . $query, 
                1
            );
            return null;
            
        }
        // normalise format
        $fmt = implode('', $fmt);
        $fmt = str_replace('%', '', $fmt);

        array_unshift($values, $fmt); 
        call_user_func_array(
            array($stmt, 'bind_param'), 
            $this->_refValues($values)
        );
        // execute the query
        $stmt->execute(); 
        $row = $this->_bindResultArray($stmt);
        $results = [];
        while ($stmt->fetch()) { 
            $results[] = $this->_getReferences($row);
        }    
        if ($results) {
            $stmt->close(); 
            return $results;
        } else {
            $stmt->close(); 
            return null;
        }   
    }

    /** 
     * Insert row
     *
     * @param  string $table Table.
     * @param  array  $data Data.
     * @param  array  $fmt Format
     *         %d for decimal,
     *         %s for string,
     *         $f for float (%i for integer, 
     *         %d for double, %b for blob).
     * @return bool   True if successful else false.
     */
    public function insert($table, $data, $fmt)
    {
        // Check for $table or $data not set
        if (empty($table) || empty($data)) {
            return false;
        }
        // Connect to the database
        $db = $this->connect();
        // Cast $data and $fmt to arrays
        $data = (array) $data;
        $fmt = (array) $fmt;
        // Build format string
        $fmt = implode('', $fmt);
        $fmt = str_replace('%', '', $fmt);
        list( $fields, $placeholders, $values ) = $this->_mysqlPrepQuery($data);
        // Prepend $fmt onto $values
        array_unshift($values, $fmt);
         
        // Prepary our query for binding
        $stmt = $db->prepare(
            "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})"
        );
         
        // Dynamically bind values
        call_user_func_array([$stmt, 'bind_param'], $this->_refValues($values));
        // Execute the query
        $stmt->execute();
        // Check for successful insertion
        // @codingStandardsIgnoreStart
        if ($stmt->affected_rows) {
            return true;
        }
        // @codingStandardsIgnoreEnd
        return false;
    }

    /** 
     * Update row
     *
     * @param  string $table Table name.
     * @param  array  $data Update data.
     * @param  array  $fmt Format of data.
     * @param  array  $where Where.
     * @param  array  $whereFmt Where format.
     * @return int    Number of rows updated.
     */
    public function update($table, $data, $fmt, $where, $whereFmt)
    {
        // Check for $table or $data not set
        if (empty($table) || empty($data)) {
            return false;
        }
        // connect to the database
        $conn = $this->connect();
        // cast $data and $fmt to arrays
        $data = (array) $data;
        $fmt = (array) $fmt;
        // build format array
        $fmt = implode('', $fmt);
        $fmt = str_replace('%', '', $fmt);
        $whereFmt = implode('', $whereFmt);
        $whereFmt = str_replace('%', '', $whereFmt);
        $fmt .= $whereFmt;
        list(
            $fields, 
            $placeholders, 
            $values) = $this->_mysqlPrepQuery($data, 'update');
        // format where clause
        $whereClause = '';
        $whereValues = '';
        $count = 0;
        foreach ( $where as $field => $value ) {
            if ( $count > 0 ) {
                $whereClause .= ' AND ';
            }
            $whereClause .= $field . '=?';
            $whereValues[] = $value;
            $count++;
        }
         
        // prepend $fmt onto $values
        array_unshift($values, $fmt);
        $values = array_merge($values, $whereValues);
         
        // prepare our query for binding
        $stmt = $conn->prepare(
            "UPDATE {$table} 
            SET {$placeholders} 
            WHERE {$whereClause}"
        );
        // dynamically bind values
        call_user_func_array(
            array($stmt, 'bind_param'),
            $this->_refValues($values)
        );
        // execute the query
        $stmt->execute();
        // check for successful insertion

        // @codingStandardsIgnoreStart
        if ($stmt->affected_rows) {
            return $stmt->affected_rows;
        }
        // @codingStandardsIgnoreEnd
        return 0;
    }

    /** 
     * Update many rows
     *
     * @param  string $table  Table name.
     * @param  string $column Update column.
     * @param  array  $data   Update data.
     *                Column names as keys and new values as values.
     * @param  string $where  Where column. 
     * @return int    Number of rows updated.
     */
    public function updateMany($table, $column, $data, $where) 
    {
        if (empty($data))
            return false;
        // construct query
        $ids = implode(',', array_keys($data));
        $sql = "UPDATE ".$table." SET ".$column." = CASE ".$where." ";
        foreach ($data as $id => $ordinal) {
            $sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
        }
        $sql .= "END WHERE ".$where." IN ($ids)";
        // connect to the database
        $conn = $this->connect(); // mysqli
        $result = mysqli_query($conn, $sql);
        return $result;
    }


    /** 
     * Delete query
     *
     * @param  string $table Table name.
     * @param  array  $where Where.
     */
    public function delete($table, $where)
    {
    }

    /**
     * Utility method to convert fields array to SQL string
     *
     * @param  array  $fields Fields.
     * @return string SQL fields string.
     */
    private static function _mysqlFieldsArrayToStatement($fields)
    {
        return "`".implode("`, `", $fields)."`";
    }

    /**
     * Utility method to prep query
     *
     * @param array  $data Data.
     * @param string $type Type of query.
     */
    private function _mysqlPrepQuery($data, $type='insert')
    { 
        $fields = '';
        $placeholders = '';
        $values = array();
        // data - fields, placeholders and values
        foreach ( $data as $field => $value ) {
            $fields .= "{$field},";
            $values[] = $value;
            if ( $type == 'update') {
                $placeholders .= $field . '=?,';
            } else {
                $placeholders .= '?,';
            }
        }
        $fields = substr($fields, 0, -1);
        $placeholders = substr($placeholders, 0, -1);
        return array( $fields, $placeholders, $values );
    }   

    /**
     * MySQL Bind Result Array
     *
     * @param object MySQL statement.
     * @return array Results as array.
     */
    private function _bindResultArray($stmt)
    {
        $meta = $stmt->result_metadata();
        $result = array();
        while ($field = $meta->fetch_field()) {
            $result[$field->name] = null;
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        return $result;
    }
     
    /**
     * Get References
     *
     * @param object MySQL row.
     * @return array References.
     */
    private function _getReferences($row)
    {
        return array_map(create_function('$a', 'return $a;'), $row);
    }

    /**
     * Ref Values
     *
     * @param  array $array Values.
     * @return array $refs  Reference values.
     */
    private function _refValues($array) 
    {
        $refs = array();
         
        foreach ($array as $key => $value) {
        $refs[$key] = &$array[$key];
        }
         
        return $refs;
    }
}