<?php
//session_start();

$_SESSION['dbPath'] = "/vagrant/";

function timestamp(){
    $t = microtime(true);
    $milli = sprintf("%03d",($t - floor($t)) * 1000000);
    $timestamp = new DateTime( date('Y-m-d H:i:s.'.$milli,$t));
    $timestamp = $timestamp->format('Y-m-d H:i:s').'.'.substr($milli,0,3);
    return $timestamp;
}

?>