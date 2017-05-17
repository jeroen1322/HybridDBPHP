<?php
include('./lib/client.php'); //Include the HybridDB PHP driver

$hdb = new HDB('10.32.97.28', '6969', 'HDBTest'); //('Ip adres of the server', 'port of the server')

print_r($hdb->select('TABLE', 'COLUMN', 'VALUE')); //Query the database with an SELECT statement
