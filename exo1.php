<?php
function niveauscolaire($age) {
    if ($age < 3) {
        return "creche";
    } elseif ($age < 6) {
        return "maternelle";
    } elseif ($age < 11) {
        return "primaire";
    } elseif ($age < 16) {
        return "collège";
    } elseif ($age < 18) {
        return "lycée";
    } else {
        return "rien";
    }
}
echo niveauscolaire(15);
?>