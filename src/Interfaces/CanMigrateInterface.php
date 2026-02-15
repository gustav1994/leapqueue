<?php

namespace LeapQueue\Interfaces;

interface CanMigrateInterface
{

    /**
     * Returns true if all migration operations were successful otherwise false.
     * This will install the database.
     * 
     * @return bool
     */
    public static function migrate() : bool;
    
    /**
     * Returns true if all down operations were successful otherwise false.
     * Consider this a clean-up task in the database.
     * 
     * @return bool
     */
    public static function down() : bool;
}