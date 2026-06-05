<?php

$conn = mysqli_connect(

    "sql213.iceiy.com",

    "icei_42007704",

    "EIqtL6Dymk3O",

    "icei_42007704_trustfund",

  

);

if(!$conn){

    die("Database connection failed: " . mysqli_connect_error());

}

?>