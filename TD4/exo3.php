<?php

// Lire le fichier
$ligneFichier = file("table.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$erreurs = [];

for ($i = 1; $i <= 10; $i++) {
    // Les valeurs de la ligne (on enlève le premier chiffre qui est l'index)
    $valeurs = preg_split('/\s+/', $ligneFichier[$i]); 
    array_shift($valeurs); // enlève le premier élément (ligne i)
    
    for ($j = 1; $j <= 10; $j++) {
        if ((int)$valeurs[$j - 1] !== $i * $j) {
            $erreurs[] = "{$i}x{$j}";
        }
    }
}

echo "Les erreurs sont : " . implode(", ", $erreurs);

?>
