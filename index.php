<?php

require_once('FedexConection.php');

// Usage.
$methods = [
    'FEDEX1DAYFREIGHT' => 'FEDEX_1_DAY_FREIGHT',
    'FEDEXEXPRESSSAVER' => 'FEDEX_EXPRESS_SAVER',
    'FEDEXGROUND' => 'FEDEX_GROUND',
    'FIRSTOVERNIGHT' => 'FIRST_OVERNIGHT',
    'PRIORITYOVERNIGHT' => 'PRIORITY_OVERNIGHT',
    //'SMARTPOST' => 'SMART_POST',
    'STANDARDOVERNIGHT' => 'STANDARD_OVERNIGHT',
    'FEDEXFREIGHT' => 'FEDEX_FREIGHT',
];

$dropoff = [
    'REGULARPICKUP' => 'REGULAR_PICKUP',
    'REQUESTCOURIER' => 'REQUEST_COURIER',
    'DROPBOX' => 'DROP_BOX',
    'BUSINESSSERVICECENTER' => 'BUSINESS_SERVICE_CENTER',
    'STATION' => 'STATION',
];

$packaging = [
    'FEDEXENVELOPE' => 'FEDEX_ENVELOPE',
    'FEDEXPAK' => 'FEDEX_PAK',
    'FEDEXBOX' => 'FEDEX_BOX',
    'FEDEXTUBE' => 'FEDEX_TUBE',
    'FEDEX10KGBOX' => 'FEDEX_10KG_BOX',
    'FEDEX25KGBOX' => 'FEDEX_25KG_BOX',
    'YOURPACKAGING' => 'YOUR_PACKAGING',
];

$request = [
    'dropoff' => 'REGULAR_PICKUP',
    'packaging' => 'YOUR_PACKAGING',

    'origin_name' => 'Paulina',
    'origin_phone' => '3317823042',
    'origin_email' => 'paulina@gmail.com',
    'origin_street' => 'Hignacio Espinoza',
    'origin_city' => 'Zapopan',
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
print_r($obj->collectRates($request));

