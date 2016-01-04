<?php

$lib="/var/simplesamlphp/lib";

if($_SERVER['SERVER_NAME']=='10.20.0.38'){
	$sp="SPcrono";  // Name of SP defined in config/authsources.php
}elseif ($_SERVER['SERVER_NAME']=='10.20.0.9' || $_SERVER['SERVER_NAME']=='pruebasoas.udistrital.edu.co'){
	$sp="SPpruebas";  // Name of SP defined in config/authsources.php
}elseif ($_SERVER['SERVER_NAME']=='10.20.0.19' || $_SERVER['SERVER_NAME']=='oas.udistrital.edu.co'){
	$sp="SPoas";  // Name of SP defined in config/authsources.php
}

try {
    // Autoload simplesamlphp classes.
    if(!file_exists("{$lib}/_autoload.php")) {
        throw(new Exception("simpleSAMLphp lib loader file does not exist: ".
        "{$lib}/_autoload.php"));
    }
 
    include_once("{$lib}/_autoload.php");
    $as = new SimpleSAML_Auth_Simple($sp);
 	
    // Take the user to IdP and authenticate.
   
   $valid_saml_session = $as->isAuthenticated();
 
} catch (Exception $e) {
    // SimpleSAMLphp is not configured correctly.
    throw(new Exception("SSO authentication failed: ". $e->getMessage()));
    return;
}
 
if ($valid_saml_session) {
    // Not valid session. Redirect a user to Identity Provider
    try {
        //$as = new SimpleSAML_Auth_Simple($sp);

        $as->logout();
        
    } catch (Exception $e) {
        // SimpleSAMLphp is not configured correctly.
        throw(new Exception("SSO authentication failed: ". $e->getMessage()));
        return;
    }
}

?>