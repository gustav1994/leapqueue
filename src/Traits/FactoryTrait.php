<?php

namespace LeapQueue\Traits;

use PDO;

trait FactoryTrait
{
    use DatabaseTrait;
    
    /**
     * Retrieves a single record from the database based on a unique index and returns it as an instance of the class.
     * 
     * @param string $field
     * @param mixed $value
     * @param ?static
     */
    public static function find( string $field, mixed $value ) : ?static
    {
        $sql = "
            SELECT * 
            FROM " . static::getTableName() . "
            WHERE {$field} = :value
            LIMIT 0,1
        ";
        $stmt = static::$db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);

        $stmt->execute(['value' => $value]);

        return $stmt->fetch() ?: null;
    }

}