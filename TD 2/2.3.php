<?php
function my_str_contains($haystack, $needle) {
    $hayLen = strlen($haystack);  
    $needleLen = strlen($needle); 

    if ($needleLen === 0) {
        return true; 
    }

    
    for ($i = 0; $i <= $hayLen - $needleLen; $i++) {
        $found = true;

        
        for ($j = 0; $j < $needleLen; $j++) {
            if (!isset($haystack[$i + $j]) || $haystack[$i + $j] !== $needle[$j]) {
                $found = false;
                break;
            }
        }

        if ($found) {
            return true; 
        }
    }

    return false; 
}


$texte = "Bonjour le monde";
$souschaine = "le";

if (my_str_contains($texte, $souschaine)) {
    echo "La sous-chaîne '$souschaine' est présente dans '$texte'.";
} else {
    echo "La sous-chaîne '$souschaine' n'est pas présente dans '$texte'.";
}
?>
