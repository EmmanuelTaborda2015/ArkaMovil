<?php

$lib="/var/simplesamlphp/lib";

if($_SERVER['SERVER_NAME']=='10.20.0.38'){
	$sp="SPcrono";  // Name of SP defined in config/authsources.php	
}elseif ($_SERVER['SERVER_NAME']=='10.20.0.9' || $_SERVER['SERVER_NAME']=='pruebasoas.udistrital.edu.co'){
	$sp="SPpruebas";  // Name of SP defined in config/authsources.php	
}elseif ($_SERVER['SERVER_NAME']=='10.20.0.19' || $_SERVER['SERVER_NAME']=='oas.udistrital.edu.co'){
	$sp="SPoas";  // Name of SP defined in config/authsources.php	
}

$url="http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']; 
$dispositivo = $_REQUEST['dispositivo'];

try {
    // Autoload simplesamlphp classes.
    if(!file_exists("{$lib}/_autoload.php")) {
        throw(new Exception("simpleSAMLphp lib loader file does not exist: ".
        "{$lib}/_autoload.php"));
    }
 
    include_once("{$lib}/_autoload.php");
    $as = new SimpleSAML_Auth_Simple($sp);
 	
    // Take the user to IdP and authenticate.
   $as->requireAuth();	
   $valid_saml_session = $as->isAuthenticated();
 
} catch (Exception $e) {
    // SimpleSAMLphp is not configured correctly.
    throw(new Exception("SSO authentication failed: ". $e->getMessage()));
    return;
}
 
if (!$valid_saml_session) {
    // Not valid session. Redirect a user to Identity Provider
    try {
        $as = new SimpleSAML_Auth_Simple($sp);

        $as->requireAuth();
    } catch (Exception $e) {
        // SimpleSAMLphp is not configured correctly.
        throw(new Exception("SSO authentication failed: ". $e->getMessage()));
        return;
    }
}
 

// At this point, the user is authenticated by the Identity Provider, and has access
// to the attributes received with SAML assertion.
$attributes = $as->getAttributes();

$usuario = $attributes['usuario'][0];
$perfil = $attributes['perfil'][0];

 echo	"<script type='text/javascript'>
		
		var usuario='$usuario';
		var perfil = '$perfil';
		
		setParam();

		function setParam() {
			Android.setParam(usuario, perfil);			
    	}
    	
	</script>";
 
 $_Request['ruta']= 'http://localhost/arkamovil/webservice/funcionario/servicio/servicio.php';
 
 $cliente = new SoapClient(null, array(  'location' => $_Request['ruta'], // Ruta del servidor
 		'uri'    => 'urn:arka', // Nombre que se le ha dado al URI del servidor
 		'trace'    => 1
 )
 );
 
 $cliente->login($usuario, $dispositivo);

?>

