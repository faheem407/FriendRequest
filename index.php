<?php

$view = new stdClass();
// Set page title as Homepage
$view->pageTitle = 'Homepage';

// Include all the required files at once in order
require_once("logincontroller.php");
require_once('friendrequestcontroller.php');
require_once('Views/index.phtml');