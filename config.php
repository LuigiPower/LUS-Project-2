<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

date_default_timezone_set('Europe/Rome');

define('DB_HOST', "127.0.0.1");
define('DB_USER', "root");
define('DB_PASS', "");
define('DB_NAME', "moviedb");

define('DOCUMENT_ROOT', '/www/html/lus/');
define('CONTROLLER_PATH', DOCUMENT_ROOT . '/app/controller/');
define('MODEL_PATH',DOCUMENT_ROOT . '/app/model/');
define('LIB_PATH',DOCUMENT_ROOT . '/app/lib/');
define('DATA_PATH','/tmp/');
define('LOG_PATH', DOCUMENT_ROOT);

define('WS_NAME','LUS');
define('CONFIG_SUFF','_config');
define('CONFIG_EXT','.config');

define('BASE_URL','http://lus.it/');

// configure paths
define ('classifier', LIB_PATH.'models/MAP.fst');
define ('cilex', LIB_PATH.'models/classifier.lex');
define ('colex', LIB_PATH.'models/classifier.lex');
define ('lm', LIB_PATH.'models/myslu.lm');
define ('wfst', LIB_PATH.'models/mywfst.fst');
define ('sluilex', LIB_PATH.'models/mylexicon.lex');
define ('sluolex', LIB_PATH.'models/mylexicon.lex');
define ('unk', '<unk>');

// thresholds
define ('n_best', '3');
define ('concept_acceptance', '0.87');
define ('intent_acceptance', '0.93');

//final keys
define('concepts_key', "concepts");
define('intents_key', "intents");
define('sentence_key', "sentence");
define('final_intent', "final_intent");
define('final_concept', "final_concept");
define('partial_concept1', "partial_concept1");
define('partial_concept2', "partial_concept2");


define('EXPIRE', '1800');

require_once LIB_PATH.'common.php';
// for SLU processing
require_once LIB_PATH.'FstClassifier.php';
require_once LIB_PATH.'FstSlu.php';
require_once LIB_PATH.'SluResults.php';

// for DB
require_once LIB_PATH.'Slu2DB.php';
require_once LIB_PATH.'QueryDB.php';
