<?php 

/**
 * Applicatoins module class
 *
 * @package munkireport
 * @author tuxudo
 **/
class Applications_controller extends Module_controller
{
	
	/*** Protect methods with auth! ****/
	function __construct()
	{
		// Store module path
		$this->module_path = dirname(__FILE__);
	}

    /**
	 * Default method
	 * @author tuxudo
	 *
	 **/
    function index()
	{
		echo "You've loaded the applications module!";
	}
    
    /**
     * Retrieve data in json format for widget
     *
     **/
    public function get_32_bit_apps()
    {
        $sql = "SELECT COUNT(CASE WHEN name <> '' AND has64bit = 0 THEN 1 END) AS count, name
                FROM applications
                LEFT JOIN reportdata USING (serial_number)
                WHERE has64bit = 0
                ".get_machine_group_filter('AND')."
                GROUP BY name
                ORDER BY count DESC";

        $queryobj = new Applications_model();
        jsonView($queryobj->query($sql));
     }
    
    /**
     * Retrieve data in json format
     *
     **/
    public function get_data($serial_number = '')
    {
        $sql = "SELECT name, path, last_modified, obtained_from, runtime_environment, version, bundle_version, info, signed_by, has64bit
                        FROM applications
                        WHERE serial_number = '$serial_number'";

        $queryobj = new Applications_model();
        jsonView($queryobj->query($sql));
    }
		
} // End class Applications_controller
