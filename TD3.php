<?php

//  nouveaux contacts
$nouveauxContacts = ["Mathieu LOUVEL", "John Doe", "Arya Stark", "Alice Dupont", "Jean Martin", "test"];

// lire les contacts existants
$contactsExistants = file("contact.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// ouvrir le fichier en mode ajout
$fichier = fopen("contact.txt", "a");

// ajouter les contacts seulement s'ils n'existent pas
foreach ($nouveauxContacts as $contact) {
    if (!in_array($contact, $contactsExistants)) {
        fwrite($fichier, $contact . PHP_EOL);
    }
}

// fermer le fichier
fclose($fichier);

echo "Contacts ajoutés avec succès !";

?>
