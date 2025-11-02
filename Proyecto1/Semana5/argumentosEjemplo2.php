<?php

function addTwo(&$y) {
$y = $y + 2;
echo $y."\n";
}
$x = 10;
$mayor = 5;
addTwo($x) ;
addTwo($mayor);
echo $x ."\n";

echo $mayor;
?>