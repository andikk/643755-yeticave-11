<?php

require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

$categories = getCategories($link);

$lotId = filter_input(INPUT_GET, 'id');
$sqlLot = "SELECT lots.name, lots.img, lots.description, expiry_date, first_price, step, categories.name FROM lots JOIN categories ON lots.category_id = categories.id WHERE lots.id = '%s'";
$sqlLot = sprintf($sqlLot, $lotId);

$lot = findOne($sqlLot, $link);

$page_content = include_template('lot.php', ['categories' => $categories,
                                                    'lot' => $lot,
                                                    'expiryTime' => get_dt_range($lot['expiry_date'])]);
if ($lot === null) {
    $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
}

if (!mysqli_num_rows(mysqli_query($link, $sqlLot))) {
    http_response_code(404);
    $page_content = include_template('error.php', ['error' => 'Даннай лот не найден']);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Главная страница',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
