<?php

class IssueObj extends FactoryObj
{
    public function __construct($issueid=null) {
        parent::__construct("issueid","issues",$issueid);
    }

    /**
     * Get Schema
     * 
     * This returns the schema this class should have in the database.
     * This might differ from get_current_schema() which gets what the database current has.
     * MD5 hashing the schema is used to compare the database and the object schema.
     * 
     * @return  (array)
     */
    public function get_schema() {
        # Schema version f75e0de0221fb5b3a87d543601a5e885
        return array(
            array(
                "Field"     => "issueid",
                "Type"      => "int(255)",
                "Null"      => "NO",
                "Key"       => "PRI",
                "Default"   => NULL,
                "Extra"     => "auto_increment",
            ),
            array(
                "Field"     => "name",
                "Type"      => "varchar(255)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "email",
                "Type"      => "varchar(255)",
                "Null"      => "YES",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "category",
                "Type"      => "varchar(255)",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "description",
                "Type"      => "text",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "status",
                "Type"      => "enum('new','inprogress','done')",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => "new",
                "Extra"     => "",
            ),
            array(
                "Field"     => "comment",
                "Type"      => "text",
                "Null"      => "YES",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "who_commented",
                "Type"      => "varchar(50)",
                "Null"      => "YES",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
            array(
                "Field"     => "date_submitted",
                "Type"      => "datetime",
                "Null"      => "NO",
                "Key"       => "",
                "Default"   => NULL,
                "Extra"     => "",
            ),
        );
    }

    /**
     * Update Schema
     * 
     * The only reason to modify this function is if a column has updated its name or
     * a column has been removed.
     * 
     * @return  (boolean)
     */
    public function upgrade()
    {
        return parent::upgrade();
    }
}
