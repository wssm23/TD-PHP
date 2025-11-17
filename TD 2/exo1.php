<?php
function calcmoy($numbers) {
    if (empty($numbers)) {
        return 0; 
    }

    $sum = array_sum($numbers); 
    $count = count($numbers);   

    return $sum / $count;       
}


$tab = [10, 20, 30, 40, 50];
echo "La moyenne est : " . calcmoy($tab); 
?>
