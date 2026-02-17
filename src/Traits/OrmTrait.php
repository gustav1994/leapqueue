<?php

namespace LeapQueue\Traits;

use PDO;
use Exception;
use LeapQueue\Traits\DatabaseTrait;

trait OrmTrait
{
    use DatabaseTrait;

    /**
     * Delete row from the database based on the primary key
     * 
     * @return bool
     */
    public function delete() : bool
    {
        if( empty($this->id) ) {
            throw new Exception('Cannot delete a model that is not persisted yet. No ID found.');
        }

        $sql = "
            DELETE FROM " . static::getTableName() . "
            WHERE id = :id
            LIMIT 1
        ";
        $stmt = static::$db->prepare($sql);

        if( $stmt->execute(['id' => $this->id]) ) {

            $this->fields = [];

            return true;
        }

        return false;
    }

    /**
     * If we have the ID it's an update, else we perform insert.
     *  
     * Performs two operations always, because we want the model to be 100%
     * in-sync with database so after each DML query we need to refresh the memory.
     * 
     * We use a MySQL specific syntax for now where we can do inserts with the SET syntax.
     * 
     * @param bool $refresh
     * @param bool
     */
    public function save() : bool
    {
        if( !isset($this->mutableFields) || !is_array($this->mutableFields) ) {
            throw new Exception('Missing mutableFields property in class ' . static::class);
        }

        $updatableFields = array_intersect_key($this->fields, array_flip($this->mutableFields));

        if( empty($updatableFields) ) {
            throw new Exception('No valid fields to save in class ' . static::class);
        }

        // ['fieldName' => ':fieldName', 'field2' => ':field2', ...]
        $setString = [];

        // [':fieldName' => 'value', ':field2' => 'value2', ...]
        $setArray = [];

        foreach($updatableFields as $field => $value) {
            $field = trim($field);

            $setString[] = "{$field} = :{$field}";
            $setArray[":{$field}"] = $value;

        }
        
        // If we have the primary key (ID) then we update, else insert
        $sql = "INSERT INTO ". static::getTableName() ." SET ". implode(', ', $setString);

        if( !empty($this->fields['id']) ) {

            $setArray[':id'] = (int) $this->fields['id']; 

            $sql = "UPDATE ". static::getTableName() ." SET ". implode(', ', $setString) . " WHERE id = :id";

        }
        
        // Prepare, execute and measure number of affected rows
        $stmt = static::$db->prepare($sql);        
        
        $stmt->execute($setArray);
        
        if( empty($this->id) ) {

            $this->id = (int) static::$db->lastInsertId();

        }

        return $stmt->rowCount() == 1;
    }

    /**
     * Non static method, so we have an object where we need to re-load the fields
     * from database into memory so we know it is fresh. This operation is only doable
     * if the model is persisted and we have the ID (primary key).
     * 
     * @return bool
     */
    public function refresh() : bool
    {
        if( empty($this->id) ) {
            throw new Exception('Cannot refresh a model that is not persisted yet. No ID found.');
        }
        
        $sql = "
            SELECT * 
            FROM " . static::getTableName() . "
            WHERE id = :id
            LIMIT 0,1
        ";
        $stmt = static::$db->prepare($sql);

        $stmt->execute(['id' => $this->id]);

        $this->fields = $stmt->fetch(PDO::FETCH_ASSOC);

        return !empty($this->fields);
    }

}