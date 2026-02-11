<?php

namespace LeapQueue\Migrations;

use PDO;
use Exception;

class Migrator
{            
    /**
     * We use the new property assignment syntax, where the arguments
     * to the constructor are automatically assigned to properties with the same name.
     * 
     * @param PDO $db
     * @return void
     */
    public function __construct( protected PDO $db, protected array $config = [] )
    {

    }   
    
    /**
     * Determines the database driver (mysql, sqlite, pgsql) and runs the corresponding SQL migration files, 
     * in the order they are defined in the directory if we sort ascending (001, 002, etc.).
     * 
     * @param array $config
     * @return void    
     */
    public function run()
    {
        $migrations = $this->getRawMigrationStatements();

        // Map configuration variables into the search and replace array for the SQL statements, e.g. {{prefix}} => 'vq_'
        $searchAndReplace = $this->configToSearchAndReplace($this->config);

        foreach( $migrations as $filename => $rawStatement ) {

            try {
                $sql = $this->prepareMigrationStatement($rawStatement, $searchAndReplace);

                $this->db->exec($sql);   

            } catch (Exception $e) {
                throw new Exception("Error executing migration $filename: " . $e->getMessage());
            }

        }
    }

    /**
     * Return path to the migrations directory for the current database driver
     * configured in the PDO connection (mysql, sqlite, pgsql).
     * 
     * @return string
     */
    protected function getMigrationsDir(): string
    {
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

        $path = __DIR__ . '/' . $driver . '/';

        if( file_exists($path) ) {
            return $path;
        }

        throw new Exception('Database driver not supported by LeapQueue.');
    }

    /**
     * Get the corresponding migrations files for the current database driver, sorted in ascending order (001, 002, etc.).
     * 
     * Return array with filename in key and content as values.
     * 
     * @return array    
     */
    protected function getRawMigrationStatements(): array
    {
        $dir = $this->getMigrationsDir();
        $files = glob($dir . '*.sql');

        sort($files);

        $migrations = [];

        foreach( $files as $file ) {

            $migrations[basename($file)] = file_get_contents($file);

        }

        return $migrations;
    }

    /**
     * Run replacement codes in the SQL migration files, such as {{prefix}} for the table prefix,
     * and return the final SQL statement ready to be executed.
     * 
     * @param string $sql
     * @param array $searchAndReplace
     * @return string
     */
    protected function prepareMigrationStatement( string $sql, array $searchAndReplace = [] ): string
    {
        return strtr(trim($sql), $searchAndReplace);        
    }

    /**
     * Convert the database configuration array into a search and replace 
     * array for the SQL migration files, where the keys are wrapped in {{}} and the values are cast to string.
     * 
     * @param array $config
     * @return array
     */
    protected function configToSearchAndReplace( array $config ): array
    {
        $searchAndReplace = [];

        foreach( $config as $key => $value ) {

            if( is_scalar($value) ) {
                
                $replaceKey = '{{' . trim($key) . '}}';

                $searchAndReplace[$replaceKey] = (string) $value;

            }            

        }

        return $searchAndReplace;
    }

}