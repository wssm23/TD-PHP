<?php
function generatePattern($n) {
    $result = ""; // variable pour stocker le résultat
    for ($i = 1; $i <= $n; $i++) {
        $result .= str_repeat($i, $i); // répète le chiffre $i exactement $i fois
        if ($i != $n) {
            $result .= "/"; // ajoute "/" entre les motifs, sauf à la fin
        }
    }
    return $result;
}
echo generatePattern(5);  
?>