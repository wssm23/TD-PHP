<?php

// PGCD version 1 : soustraction

function pgcd_1($a, $b) {
    $a = abs($a);
    $b = abs($b);

    while ($a != $b) {
        if ($a > $b) {
            $a -= $b;
        } else {
            $b -= $a;
        }
    }
    return $a;
}


// PGCD version 2 : modulo

function pgcd_2($a, $b) {
    $a = abs($a);
    $b = abs($b);

    while ($b != 0) {
        $tmp = $b;
        $b = $a % $b;
        $a = $tmp;
    }
    return $a;
}


// PGCD version 3 : récursive

function pgcd_3($a, $b) {
    $a = abs($a);
    $b = abs($b);

    if ($b == 0) {
        return $a;
    }
    return pgcd_3($b, $a % $b);
}


// Test des 3 fonctions

$a = 48;
$b = 18;

echo "PGCD de $a et $b avec pgcd_1 : " . pgcd_1($a, $b) . "\n";
echo "PGCD de $a et $b avec pgcd_2 : " . pgcd_2($a, $b) . "\n";
echo "PGCD de $a et $b avec pgcd_3 : " . pgcd_3($a, $b) . "\n";
?>
