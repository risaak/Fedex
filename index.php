<?php

require_once('FedexConection.php');

// Usage.
$methods = [
    'FEDEX1DAYFREIGHT' => 'FEDEX_1_DAY_FREIGHT',
    'FEDEXEXPRESSSAVER' => 'FEDEX_EXPRESS_SAVER',
    'FEDEXGROUND' => 'FEDEX_GROUND',
    'FIRSTOVERNIGHT' => 'FIRST_OVERNIGHT',
    'PRIORITYOVERNIGHT' => 'PRIORITY_OVERNIGHT',
    'SMARTPOST' => 'SMART_POST',
    'STANDARDOVERNIGHT' => 'STANDARD_OVERNIGHT',
    'FEDEXFREIGHT' => 'FEDEX_FREIGHT',
];

$request = [
    'dropoff' => 'REGULAR_PICKUP',
    'packaging' => 'YOUR_PACKAGING',
    'origin_country' => 'MX',
    'origin_postcode' => '45189',
    'dest_country' => 'MX',
    'dest_postcode' => '20298',
    'dest_city' => 'Aguscalientes',
    'weight' => '1',
    'value' => '150',
];

$obj = new FedexConection(true);
$obj->setAllowedMethods($methods);
echo '<pre>';
print_r($obj->collectRates($request));

