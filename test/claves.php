<?php


$clave1 = "haras";
$clave2 = "veterinario";



var_dump(password_hash($clave1, PASSWORD_BCRYPT));
echo "<hr>";
var_dump(password_hash($clave2, PASSWORD_BCRYPT));
echo "<hr>";
