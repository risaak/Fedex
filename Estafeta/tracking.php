<?php

require_once('EstafetaConection.php');

$filterType = [
    'DELIVERED' => 'DELIVERED',
    'ON_TRANSIT' => 'ON_TRANSIT',
    'RETURNED' => 'RETURNED',
];

$historyType =  [
    'ALL' => 'ALL', //todos los movimientos
    'ONLY_EXCEPTIONS' => 'ONLY_EXCEPTIONS', ////Sólo los movimientos que tengan excepción
    'LAST_EVENT' => 'LAST_EVENT', //Sólo el último movimiento para envíos que aún no estén confirmados
];

$request =  [
    'waybills' => ['8055241528464720099314','2055241528464720097313','1015205364715620036053'],
    'filterType' => 'DELIVERED',
    'historyType' => 'ALL',
    //Boolean
    'dimensions' => '1',   ///Indica si se desea obtener en la respuesta el grupo de datos que contiene las dimensiones del envío
    'waybillReplaceData' => '0', //Indica si se desea obtener en la respuesta el grupo de datos de la información de guía de reemplazo (para los casos que aplique)
    'returnDocumentData' => '0', //Indica si se desea obtener en la respuesta el grupo de datos de guía de documento de retorno ( para el caso en el que la guía consultada es una guía de documento de retorno)
    'multipleServiceData' => '0', //Indica si se desea obtener en la respuesta el grupo de datos de servicio múltiple (para los casos que aplique)
    'internationalData' => '0', //Indica si se desea obtener en la respuesta el grupo de datos de información internacional (para los casos que aplique)
    'signature' => '0', //Indica si se desea obtener en la respuesta la firma de confirmación del envío si ésta se encuentra almacenada para el envío. 
    'customerInfo' => '1', //Indica si se desea obtener en la respuesta el grupo de datos de información del cliente que incluye la referencia y el centro de costos
];

$obj = new EstafetaConection(true);
echo '<pre>';

print_r($obj->getTracking($request));