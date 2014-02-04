<?php
/**
 * Property Manager
 * 
 * Manages several Properties at once. Currently only loads specific Properties. Can be
 * extended to update, save, or delete multiple properties. This is the place to do any
 * multiple Property changes.
 * 
 * @author      Ryan Carney-Mogan
 * @category    Core_Classes
 * @version     1.0.2
 * @copyright   Copyright (c) 2013 University of Colorado Boulder (http://colorado.edu)
 * 
 */

class PropertyManager
{
    /**
     * Load Property
     * 
     * Loads all the property with the set status. Defaults to Properties with "posted" statuses.
     * 
     * @param   (string)    $status     	Which status Properties to load
	 * @return	(object[])					Returns array of Property objects
     */
    public function load_property($status="posted") 
    {
        $property = array();
        $conn = Yii::app()->db;
        $query = "
            SELECT          propertyid
            FROM            {{property}}
            WHERE           status = :status
            ORDER BY        date_updated DESC;
        ";
        $command = $conn->createCommand($query);
        $command->bindParam(":status",$status);
        $result = $command->queryAll();
        
        if(!$result or empty($result)) {
            return array();
        }
        
        foreach($result as $row) {
            $property[] = new PropertyObj($row["propertyid"]);
        }
        return $property;
    }
    
    /**
     * Load My Property
     * 
     * Loads all the property of the user.
     * 
     * @param   (string)    $username       Username of property to load.
     * @return  (object[])                  Returns array of Property objects
     */
    public function load_my_property()
    {
        # Currently empty, needs to be filled
    }
}
