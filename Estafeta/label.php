<?php

require_once('EstafetaConection.php');


$request = [
    'valid' => 'False',
    'customerNumber' => '0000000', //Número de Cliente Estafeta
    'quadrant' => '0', //Cuadrante de inicio de impresión de guías. 1-4 – impresora láser. Solo aplica cuando paperType sea 3.
    'paperType' => '1',
    'labelDescriptionListCount' => '1',
    'aditionalInfo' => 'OPERACION5', //Información adicional sobre el envío
    'content' => 'JOYAS',
    'contentDescription' => 'ORO',
    'costCenter' => '12345', //Centro de Costos del cliente al que pertenece el envío
    'deliveryToEstafetaOffice' => 'False', //Si el valor es “True”, el envío es “Entrega Ocurre” es decir se entregará en una oficina Estafeta en lugar del domicilio del destinatario
    'destinationCountryId' => 'MX',
    'destination_address1' => 'MAIZALES',
    'destination_address2' => '35',
    'destination_cellPhone' => '4444444',
    'destination_city' => 'COYOACAN',
    'destination_contactName' => 'JAVIER SANCHEZ',
    'destination_corporateName' => 'CHICOLOAPAN SA DE CV<',
    'destination_customerNumber' => '0000000',
    'destination_neighborhood' => 'CENTRO', //Colonia
    'destination_phoneNumber' => '777777',
    'destination_state' => 'ESTADO  DE MEXICO',
    'destination_zipCode' => '02130',
    'numberOfLabels' => '1',
    'officeNum' => '130',
    'origin_address1' => 'CALLE 5',
    'origin_address2' => '29',
    'origin_cellPhone' => '888888',
    'origin_city' => 'TLALPAN',
    'origin_contactName' => 'JANET OIDOR',
    'origin_corporateName' => 'ALTAS SA DE CV',
    'origin_customerNumber' => '0000000',
    'origin_neighborhood' => 'CENTRO', // //Colonia
    'origin_phoneNumber' => '9999999',
    'origin_state' => 'DF',
    'origin_zipCode' => '02300',
    'parcelTypeId' => '4', //Tipo de envío 1 Sobre  4 paquete
    'reference' => 'FRENTE AL SANBORNS',
    'returnDocument' => 'False',
    'serviceTypeId' => '70', //Identificador de tipo de Servicio Estafeta para la impresión de guías
    'weight' => '5'
];


$obj = new EstafetaConection(true);

$response = $obj->getLabel($request);

//$data =  $response['labelPDF'];
header('Content-Type: application/pdf');
print_r($response['labelPDF']);