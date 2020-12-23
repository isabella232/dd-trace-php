<?php

use DDTrace\Tracer;

error_log("Playground start");

// if (class_exists('DDTrace\Tracer', $autoload = false)) {
//     echo "Tracer version: " . Tracer::version() . "\n";
// }

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'httpbin/get?client=curl');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);

error_log("Playground after curl");
