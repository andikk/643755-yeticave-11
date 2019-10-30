<?php

function format_price($price) {
    $formattedPrice = ceil($price);
    if ($formattedPrice >= 1000) {
        $formattedPrice = number_format($formattedPrice, 0, '', ' ');
    }

    return $formattedPrice . ' ₽';
}

function get_dt_range($expiry_date)
{
    $dt_now = date_create("now");
    $dt_end = date_create($expiry_date);
    $dt_diff = date_diff($dt_end, $dt_now);
    $days_count = date_interval_format($dt_diff, "%a");
    if ($days_count == 0 && $dt_end > $dt_now) {
        $hours_count = date_interval_format($dt_diff, "%H %I");
        return explode(' ', $hours_count);
    }

    return null;
}

function esc($str) {
    $text = htmlspecialchars($str);
    return $text;
}

function findAll($sql, $link) {
    $result = mysqli_query($link, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;
}

function findOne($sql, $link) {
    $result = mysqli_query($link, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC)[0] : null;
}

function getCategories($link) {
    $sqlCategories = 'SELECT `char_code`, `name` FROM categories';
    $categories = findAll($sqlCategories, $link);
    if ($categories === null) {
        die(mysqli_error($link));
    }
    return $categories;
}

