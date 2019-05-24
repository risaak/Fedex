<?php

require_once('EstafetaConection.php');


$request = [
    'frecuencia' => 'true', //esFrecuencia=true, devolverá solo frecuencia de servicios. esfrecuencia=false, devolverá frecuencia + cotización
    'paquete' => 'true',
    'largo' => '10', 
    'peso' => '10', 
    'alto' => '10', 
    'ancho' => '10',
    'cpOrigen' => '45168',
    'cpDestino' => '45189',
];


$obj = new EstafetaConection(true);
echo '<pre>';

print_r($obj->getRates($request));