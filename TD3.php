<?php


$nouveauxContacts = ["Mathieu LOUVEL", "John Doe", "Arya Stark", "Alice Dupont", "Jean Martin", "test"];

$contactsExistants = file("contact.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$fichier = fopen("contact.txt", "a");

foreach ($nouveauxContacts as $contact) {
    if (!in_array($contact, $contactsExistants)) {
        fwrite($fichier, $contact . PHP_EOL);
    }
}

fclose($fichier);

echo "Contacts ajoutés avec succès !";

?>

