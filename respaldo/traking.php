<?php
require_once('FedexConection.php');

$obj = new FedexConection(true);
echo '<pre>';

print_r($obj->getTracking(['039813852990618','231300687629630']));