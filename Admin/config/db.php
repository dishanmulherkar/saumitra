<?php

$con = mysqli_connect(
    "localhost",
    "root",
    "",
    "saumi"
);

if(!$con){
    die("Connection Failed");
}
date_default_timezone_set('Asia/Kolkata');

define('BASE_URL', '/saumitra/Admin/');

$base =  '/saumitra/Admin/';


function url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}