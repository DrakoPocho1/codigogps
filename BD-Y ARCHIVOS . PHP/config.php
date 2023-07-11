<?php 
define('DB_HOST', 'localhost'); 
define('DB_USERNAME', 'id21003468_jhurtadop1'); 
define('DB_PASSWORD', 'jhurtadop1@Drakopocho'); 
define('DB_NAME', 'id21003468_000111943');

define('GOOGLE_MAP_API_KEY', 'AIzaSyA_dnhX0g7Xr16ltsoC2VUU6xxaeJMnt_A');

//ESP32_API_KEY is the exact duplicate of, ESP32_API_KEY in ESP32 sketch file
//Both values must be same
define('ESP32_API_KEY', 'Ad5F10jkBM0');

//http://www.example.com/gpsdata.php
define('POST_DATA_URL', 'https://josuehurtado.000webhostapp.com/gpsdata.php');

date_default_timezone_set('Asia/Karachi');

// Connect with the database 
$db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME); 
 
// Display error if failed to connect 
if ($db->connect_errno) { 
    echo "Connection to database is failed: ".$db->connect_error;
    exit();
}