<?php 
use App\GFS\GFServiceManager;

require "vendor/autoload.php"; 

$gfsmanager = new GFServiceManager(); 
$results = $gfsmanager->coordinateInGeofences($gfsmanager->createCoordinate(0.31566363212376136, 32.56810143056096));
var_dump($results);