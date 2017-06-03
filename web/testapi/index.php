<?php

include("Rest.php");

$url="http://gestime.dev/app_dev.php/api/login_check";

$apirest=new Rest();

if (! $apirest->ConnectAPI($url)) {
    exit;
}


$response = $apirest->callApi('GET', 'http://gestime.dev/app_dev.php/api/pages');


