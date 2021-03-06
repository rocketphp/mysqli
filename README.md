# mysqli

[![Build Status](https://travis-ci.org/rocketphp/mysqli.svg?branch=master)](https://travis-ci.org/rocketphp/mysqli)
[![Coverage Status](https://coveralls.io/repos/rocketphp/mysqli/badge.svg?branch=master&service=github)](https://coveralls.io/github/rocketphp/mysqli?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/55e5f2f68c0f62001c0004ce/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55e5f2f68c0f62001c0004ce)

[![Latest Stable Version](https://poser.pugx.org/rocketphp/mysqli/v/stable)](https://packagist.org/packages/rocketphp/mysqli)
[![License](https://poser.pugx.org/rocketphp/mysqli/license)](https://packagist.org/packages/rocketphp/mysqli)

`RocketPHP\MySQLi` uses the MySQL Improved Extension to perform queries on a MySQL database via prepared statements.

**_To perform a query on a database_** – start with an instance of MySQLi and use the select, insert, update, update many and delete query methods.

```php
use RocketPHP\MySQLi\MySQLi;

$mysqli = new MySQLi([
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'example_db',
    'port'     => null,
]);

$sql = "SELECT * FROM table_name
        WHERE column_name = ?";
$values = array('column_value');
$fmt = array('%s');
$result = $mysqli->select($sql, 
                          $values,
                          $fmt);
```

- File issues at https://github.com/rocketphp/mysqli/issues
- Documentation is at http://rocketphp.io/mysqli
