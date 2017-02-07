<?php 

// check if string contains any html tags
function is_html($string) {
    if ( $string != strip_tags($string) ) {
        return true; // Contains HTML
    }
    return false; // Does not contain HTML
}

?>