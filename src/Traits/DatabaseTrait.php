<?php

namespace LeapQueue\Traits;

use PDO;
use Exception;

trait DatabaseTrait
{
    /**
     * @var object PDO
     */
    protected static PDO $db;

    /**
     * @var array
     */
    protected static array $dbConfig = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * If PDO connection is provided we will use it, else we will initiate a new connection
     * using the provided configuration using the format:
     * 
     * $dbConfig = [
     *    'dsn' => 'mysql:host=localhost;dbname=leapqueue',
     *    'driver' => '',
     *    'host' => '',
     *    'port' => '',
     *    'dbname' => '',
     *    'username' => '',
     *    'password' => '',
     *    'table_prefix' => '',
     *    'options' => []
     * ];
     * 
     * The DSN will be prioritized over the specific values driver, host, port, dbname.
     * 
     * table_prefix is standalone that works with self-provided DSN or PDO connection .
     *
     * @param array $dbConfig Database configuration array.
     * @param PDO|null $db Optional PDO connection. If not provided, a new connection
     * @return void 
     */
    public static function setConnection( array $dbConfig, ?PDO $db = null ) : void
    {
        static::$dbConfig = $dbConfig;

        // If a working PDO connection is provided we just use that.
        if( $db instanceof PDO ) {

            static::$db = $db;            
            return;

        }

        // If we should create the PDO connection, then we at least need the username
        if( empty($dbConfig['username']) ) {

            throw new Exception('Cannot create LeapQueue database connection without username');

        }
                
        // Now we have a DSN string that we can try connecting to. If it fails we throw exception to parent, 
        $dsn = $dbConfig['dsn'] ?? self::buildDsn($dbConfig);

        try {

            static::$db = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'] ?? '',
                $dbConfig['options'] ?? []
            );

        } catch (Exception $e) {
            throw new Exception('Failed to connect to the database for LeapQueue: ' . $e->getMessage());
        }
        
    }

    /**
     * Takes the databaes details array as it is defined in the phpdoc for setConnection()
     * and converts into a DSN string that can be used to create a PDO connection.
     * The driver, host and dbname are required, while port is optional.
     * 
     * @param array $config
     * @return string    
     */
    private static function buildDsn( array $config ) : string
    {
        if( empty($config['driver']) || empty($config['host']) || empty($config['dbname']) ) {

            throw new Exception('LeapQueue needs at least driver, host, dbname to build a DSN string for database connection.');

        }

        return $config['driver']
            . ':host=' . $config['host']
            . ';dbname='
            . $config['dbname']
            . (empty($config['port']) ? '' : ';port=' . $config['port'])
        ;        
    }

    /**
     * Prefixes the table name with the table_prefix if provided in the database configuration.
     * 
     * @param string|null $tableName
     * @return string
     */
    public static function getTableName( ?string $tableName = null ) : string
    {
        if( !$tableName && !static::$tableName ) {

            throw new Exception('Table name not provided and static::$table is not defined in the class using DatabaseTrait.');

        }
        
        return (static::$dbConfig['table_prefix'] ?? '') . ($tableName ?? static::$tableName);
    }   

    /**
     * Se we can set properties into the $fields array,
     * which is where we hold the queue fields from the database.
     * 
     * @param string $name The name of the property being set.
     * @param mixed $value The value being assigned to the property.
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->fields[$name] = $value;
    }

}