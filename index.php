<?php
include('./lib/client.php');

$hdb = new HDB('10.32.97.28', '6969');

print_r($hdb->select('DATABASE', 'TABLE', 'COLUMN', 'VALUE'));
