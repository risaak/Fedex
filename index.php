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
    'origin_street' => 'Ignacio Espinoza',
    'origin_city' => 'Guadalajara',
    'origin_state_code' => 'JA',
    'origin_country' => 'MX',
    'origin_postcode' => '45189',
    'dest_name' => 'Andrea',
    'dest_phone' => '3317823032',
    'dest_email' => 'andrea@gmail.com',
    'dest_street' => 'Los  alamos',
    'dest_city' => 'Zapopan',
    'dest_state_code' => 'JA',
    'dest_country' => 'MX',
    'dest_postcode' => '45160',

];
//header('Content-Type: application/pdf');

$_reqShip = [
    'shipper_contact_person_name' => 'Paulina',
    'shipper_contact_company_name' => 'None',
    'shipper_contact_phone_number' => '3317823042',
    'shipper_address_street' => 'Hignacio Espinoza',
    'shipper_address_city' => 'Guadalajara',
    'shipper_address_state_or_province_code' => 'JA',
    'shipper_address_postal_code' => '45189',
    'shipper_address_country_code' => 'MX',
    'recipient_contact_person_name' => 'Andrea',
    'recipient_contact_company_name' => 'None',
    'recipient_contact_phone_number' => '4490985634',
    'recipient_address_street' => 'Los  alamos',
    'recipient_address_city' => 'Zapopan',
    'recipient_address_state_or_province_code' => 'JA',
    'recipient_address_postal_code' => '45160',
    'recipient_address_country_code' => 'MX',
    'reference_data' => 'Order#110 P123',
    'package_params' => [
        'customs_value' => 120,
        'height' => 10,
        'width' => 4,
        'length' => 6,
        'dimension_units' => 'CM'
    ],
    'package_weight' => 1,
    'package_items' => [
        [
            'id' => '1',
            'price' => 100,
            'qty' => 3,
            'name' => 'funda para celular'
        ],
        [
            'id' => '2',
            'price' => 100,
            'qty' => 2,
            'name' => 'funda para celular 2'
        ]
    ],
    'shipping_method' => $methods['STANDARDOVERNIGHT'],
    'dropoff' => $dropoff['REGULARPICKUP'],
    'packaging_type' => $packaging['YOURPACKAGING'],
    'smartpost_hubid' => '', //Solo si es smartpost
];

print_r($obj->collectShip($_reqShip));

