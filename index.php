<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once 'config.php';

if (!isset($_POST['REQUEST_DATA'])) {
    echo '
        <html><body><h1>LUS</h1>
        <p>This is the default web page for this server.</p>
        </body></html>';
    die();
}

$response = array();
$result = array();

header('Content-Type: application/json; charset=utf-8');
$REQUEST_DATA = json_decode($_POST['REQUEST_DATA'], true);

if (!isJson($_POST['REQUEST_DATA'])) {
    responseError(array(
        'RESULT' => 'FAILED',
        'RESULT_DATA' => 'WRONG_JSON',
		));
}

if (!my_autoloader($REQUEST_DATA['CONTEXT'])) {
    responseError(array(
            'RESULT' => 'FAILED',
            'RESULT_DATA' => 'UNKNOWN_CONTEXT',
        ));
}
    
if (!isset($REQUEST_DATA['ACTION'])) {
    responseError(array(
            'RESULT' => 'FAILED',
            'RESULT_DATA' => 'MISSED_ACTION',
        ));
}

$class = new $REQUEST_DATA['CONTEXT'];
$method = $REQUEST_DATA['ACTION'];

if (!method_exists($class, $method)) {
    responseError(array(
            'RESULT' => 'FAILED',
            'RESULT_DATA' => 'UNKNOWN_ACTION',
        ));
}

$r = new ReflectionMethod($class, $method);

$params = $r->getParameters();
$param_data = array();
$missed_params = array();

foreach ($params as $param) {
    $pname = $param->getName();
    if (substr($pname, 0,1) != '_'){
                      
        // tolgo metodi privati nominati con prefisso _
        if (isset($REQUEST_DATA[$pname])){
            //recuperso i parametri mancanti
            $param_data[$pname] = $REQUEST_DATA[$pname];
        }else{
            if (substr($pname, 0,4) == 'opt_') continue;
            $missed_params[] = $pname; 
        }
    }
}
	            
if (!empty($missed_params)){
    responseError(array(
        'RESULT' => 'FAILED',
        'RESULT_DATA' => 'MISSED PARAMS:'.implode(',',$missed_params),
		));
}	


$response = call_user_func_array(array($class, $method), $param_data);


if(isset($response['ERROR'])){
	   responseError(array(
	        'RESULT' => 'FAILED',
	        'RESULT_DATA' => $response['ERROR'],
	    ));
}	    
	    
responseResult(array(
        'RESULT' => 'SUCCESS',
        'RESULT_DATA' => $response,
    ));    