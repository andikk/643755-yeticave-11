<?php
function format_price($price) {
    $formattedPrice = ceil($price);
    if ($formattedPrice >= 1000) {
        $formattedPrice = number_format($formattedPrice, 0, '', ' ');
    }

    return $formattedPrice . ' ₽';
}
