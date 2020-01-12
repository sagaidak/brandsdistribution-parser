<?php
require_once('DB.php');

function go() {
	$db = new DB;
	$db->connect();
	
	$sql = "DELETE FROM oc_product WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_description WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_image WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_to_category WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_to_layout WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_to_store WHERE product_id > 88";
	$db->query($sql);
	
	echo 'done';
	
}

function go_options() {
	$db = new DB;
	$db->connect();
	
	$sql = "DELETE FROM oc_option_value_description WHERE option_value_id > 47";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_option WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_product_option_value WHERE product_id > 88";
	$db->query($sql);
	
	$sql = "DELETE FROM oc_option_value WHERE option_value_id > 51"; 
	$db->query($sql);
	
	
	
	echo 'done';
	
}

//go(); // products
//go_options();
?>