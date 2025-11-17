<?php
function my_strrev($str) {
    $reversed = "";                
    $length = strlen($str);         
    
    for ($i = $length - 1; $i >= 0; $i--) {
        if (isset($str[$i])) {     
            $reversed .= $str[$i]; 
        }
    }

    return $reversed;
}


$chaine = "Bonjour";
echo my_strrev($chaine);  
?>
