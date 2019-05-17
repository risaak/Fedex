<?php

$dsn = 'mysql:dbname=inalarm_mage377;host=138.128.178.98';
$usuario = 'inalarm_mage377';
$contraseña = '7Sa@6Gp[I6';

try {
    $gbd = new PDO($dsn, $usuario, $contraseña);
	echo 'Susses';
} catch (PDOException $e) {
    echo 'Falló la conexión: ' . $e->getMessage();
}



?>