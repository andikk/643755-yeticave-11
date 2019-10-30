<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');
require_once('data.php');

$categories = getCategories($link);

// Запрос на получение списка лотов
$sqlLots = <<<SQL
SELECT lots.id, lots.name, lots.first_price, lots.img, lots.expiry_date, categories.name as category,
   CASE
        WHEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id) > 0 THEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id)
        ELSE lots.first_price
   END AS price
   FROM lots JOIN categories ON lots.category_id = categories.id
   WHERE lots.expiry_date > CURDATE() ORDER BY lots.expiry_date DESC;
SQL;
$lots = findAll($sqlLots, $link);

$page_content = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);

if ($lots === null) {
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
