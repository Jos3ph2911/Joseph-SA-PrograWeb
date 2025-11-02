<?php
function makecoffee($type = "cappuccino"){

    return "Making a cup of $type.\n";
    
}
echo makecoffee(). "<br>";

echo makecoffee(null)."<br>";

echo makecoffee("espresso")
?>