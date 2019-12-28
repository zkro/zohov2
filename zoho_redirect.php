<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require 'vendor/autoload.php';
require 'zoho_execute.php';

$zoho=new ZOHO_Exec();
if(!empty($_REQUEST['code'])){
	$zoho->zoho->generateToken($_REQUEST['code']);
}
?>