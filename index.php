<?php
require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);

$sqlLots = <<<SQL
SELECT lots.id, lots.name, lots.first_price as price, lots.img, lots.expiry_date, categories.name as category
   FROM lots JOIN categories ON lots.category_id = categories.id
   WHERE lots.expiry_date > CURDATE() ORDER BY lots.expiry_date DESC;
SQL;

$result = mysqli_query($link, $sqlLots);

if ($result) {
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $page_content = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);
} else {
    $page_content = include_template('error.php', ['error' => mysqli_error($link)]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'isMain' => true,
    'title' => 'Главная страница',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
