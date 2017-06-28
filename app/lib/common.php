<?php
function isJson($string) {
  return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
}

function responseError($data)
{
	http_response_code(400);
	die(json_encode($data));
	
}


function responseResult($data)
{
	die(json_encode($data));
	
}

function my_autoloader($class) {

    $resource = CONTROLLER_PATH . $class . '/' . $class . '.php';

    if (!file_exists($resource)){
	    echo 'not exist';
	    return false;
    }
    require_once $resource;
    return class_exists($class);
}

function load_config($class){
	$resource = CONTROLLER_PATH . $class . '/' . $class . CONFIG_EXT;

    if (!file_exists($resource)){
	    echo 'not exist';
	    return false;
    }
    require_once $resource;
    return class_exists($class.CONFIG_SUFF);
}


function load_model($class){
    $resource = MODEL_PATH . $class . '/' . $class . '_model.php';

    if (!file_exists($resource)){
	    return false;
    }
    require_once $resource;
    $class = $class.'_model';
    if(class_exists($class)){
		return new $class;   
    }else{
	    return false;
    }
    
}

function load_db(){
	require_once 'pdo.php';
	return new db(
		"mysql:host=".DB_HOST.";dbname=".DB_NAME.';charset=utf8;',
		DB_USER, 
		DB_PASS
	);
}

function load_log(){
	require_once 'logger.php';
	return new log(
                LOG_PATH
                );
}

function check_json($json1, $json2, $string){
	$return = array();
	if (is_array($json1)){
		foreach($json1 as $key=>$value){
			if (isset($json2[$key])){
				$intermediate = check_json($json1[$key], $json2[$key], $string);
			}
			else{
				$intermediate = $string;
			}

			$return[$key] = $intermediate;
		}
	}

	return $return;
}



