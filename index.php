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

    'origin_name' => 'Paulina',
    'origin_phone' => '3317823042',
    'origin_email' => 'paulina@gmail.com',
    'origin_street' => 'Ignazio Espinoza',
    'origin_city' => 'Guadalajara',
    'origin_state_code' => 'JA',
    'origin_country' => 'MX',
    'origin_postcode' => '45189',

    'dest_name' => 'Andrea',
    'dest_phone' => '3317823032',
    'dest_email' => 'andrea@gmail.com',
    'dest_street' => '',
    'dest_city' => 'Aguascalientes',
    'dest_state_code' => 'AG',
    'dest_country' => 'MX',
    'dest_postcode' => '20298',

    'weight' => '2',
    'value' => '150',
];

$obj = new FedexConection(true);
$obj->setAllowedMethods($methods);
echo '<pre>';
//print_r($obj->collectRates($request));

$_request = [
    'dropoff' => 'REGULAR_PICKUP',
    'ServiceType' => 'STANDARD_OVERNIGHT',
    'packaging' => 'YOUR_PACKAGING',
    'origin_name' => 'Paulina',
    'origin_phone' => '3317823042',
    'origin_email' => 'paulina@gmail.com',
    'origin_street' => 'Ignazio Espinoza',
    'origin_city' => 'Guadalajara',
    'origin_state_code' => 'JA',
    'origin_country' => 'MX',
    'origin_postcode' => '45189',
    'dest_name' => 'Andrea',
    'dest_phone' => '3317823032',
    'dest_email' => 'andrea@gmail.com',
    'dest_street' => 'Albuquerque',
    'dest_city' => 'Aguascalientes',
    'dest_state_code' => 'AG',
    'dest_country' => 'MX',
    'dest_postcode' => '20298',

];
print_r($obj->collectShip($_request));

