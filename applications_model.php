<?php

use CFPropertyList\CFPropertyList;

class Applications_model extends \Model {

    function __construct($serial='')
    {
        parent::__construct('id', 'applications'); //primary key, tablename
        $this->rs['id'] = '';
        $this->rs['serial_number'] = $serial;
        $this->rs['name'] = '';
        $this->rs['path'] = '';
        $this->rs['last_modified'] = null;
        $this->rs['obtained_from'] = '';
        $this->rs['runtime_environment'] = '';
        $this->rs['version'] = '';
        $this->rs['info'] = '';
        $this->rs['signed_by'] = '';
        $this->rs['has64bit'] = 0; // True or False
        $this->rs['bundle_version'] = '';
        $this->rs['bundle_id'] = null;

        $this->serial_number = $serial;
    }
    
    // ------------------------------------------------------------------------

    /**
    * Process data sent by postflight
    *
    * @param string data
    * @author tuxudo
    **/
    function process($plist)
    {
        // Check if we have data
        if ( ! $plist){
            throw new Exception("Error Processing Request: No property list found", 1);
        }

        // Delete previous set        
        $this->deleteWhere('serial_number=?', $this->serial_number);

        $parser = new CFPropertyList();
        $parser->parse($plist, CFPropertyList::FORMAT_XML);
        $myList = $parser->toArray();

        $typeList = array(
            'name' => '',
            'last_modified' => null,
            'obtained_from' => 'unknown',
            'path' => '',
            'runtime_environment' => '',
            'version' => '',
            'info' => '',
            'signed_by' => '',
            'has64bit' => 0, // Yes or No
            'bundle_version' => '',
            'bundle_id' => null
        );

        // List of paths to ignore
        configAppendFile(__DIR__ . '/config.php');

        // List of paths to ignore
        $bundlepath_ignorelist = is_array(conf('apps_bundlepath_ignorelist')) ? conf('apps_bundlepath_ignorelist') : array();
        $path_regex = ':^'.implode('|', $bundlepath_ignorelist).'$:';

        // Process each app
        foreach ($myList as $app) {

            // Check if we have a name
            if( ! array_key_exists("name", $app)){
                continue;
            }

            // Skip path
            if (preg_match($path_regex, $app['path'])) {
                continue;
            }

            // Fix signed_by entries
            if (array_key_exists("signed_by",$app)) {
                $app['signed_by'] = str_replace(array('Developer ID Application: '), array(''), $app['signed_by']);
            }

            // Fix last_modified date
            if (array_key_exists("last_modified",$app)) {
                $temptime = $app['last_modified'];
                $date = new DateTime("@$temptime");
                $app['last_modified'] = $date->format('U');
            }

            // Process each app for saving
            foreach ($typeList as $key => $value) {
                $this->rs[$key] = $value;
                if(array_key_exists($key, $app))
                {
                    $this->rs[$key] = $app[$key];
                }
            }

            // Save application
            $this->id = '';
            $this->save();
        }
    }
}