<?php

namespace LeapQueue\Traits;

use PDO;

trait FactoryTrait
{
    use DatabaseTrait;
    
    /**
     * Retrieves a single record from the database based on conditions
     * defined in assoc array with key (column) and value (value to match).
     * 
     * @param array $conditions ['field' => 'value']
     * @param ?static
     */
    public static function find( array $conditions ) : ?static
    {                   
        $whereClauses = [];

        foreach( $conditions as $field => $value ) {
                        
            $whereClauses[] = "{$field} = :{$field}";
            
        }

        $sql = "
            SELECT * 
            FROM " . static::getTableName() . "
            WHERE " . implode(' AND ', $whereClauses) . "
            LIMIT 0,1
        ";
        $stmt = static::$db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);

        $stmt->execute($conditions);

        return $stmt->fetch() ?: null;
    }

}