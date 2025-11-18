<?php

function my_str_contains($haystack, $needle) {
    $len_h = strlen($haystack);
    $len_n = strlen($needle);

    if ($len_n > $len_h) return false;

    for ($i = 0; $i <= $len_h - $len_n; $i++) {
        $match = true;
        for ($j = 0; $j < $len_n; $j++) {
            if (!isset($haystack[$i + $j]) || $haystack[$i + $j] != $needle[$j]) {
                $match = false;
                break;
            }
        }
        if ($match) return true;
    }

    return false;
}

// Exemples
var_dump(my_str_contains("hello", "hello world")); // false
var_dump(my_str_contains("hello world", "hello")); // true
var_dump(my_str_contains("the hello the world", "the w")); // true
var_dump(my_str_contains("hello the world", "world")); // true
var_dump(my_str_contains("hello the world", "world is big")); // false

?>
