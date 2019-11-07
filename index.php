<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');
require_once('data.php');


if (!$link) {
    $error = mysqli_connect_error();
    $content = include_template('error.php', ['error' => $error]);
} else {
    $sqlErrors[] = null;
    // Запрос на получение списка категорий
    $sqlCategories = 'SELECT `char_code`, `name` FROM categories';
    $categories = selectData($sqlCategories, $link);
    if ($categories === null) {
        $sqlErrors[] = mysqli_error($link);
    }

    // Запрос на получение списка лотов
    $sqlLots = <<<SQL
    SELECT lots.name, lots.first_price, lots.img, lots.expiry_date, categories.name as category,
       CASE
            WHEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id) > 0 THEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id)
            ELSE lots.first_price
       END AS price
       FROM lots JOIN categories ON lots.category_id = categories.id
       WHERE lots.expiry_date > CURDATE() ORDER BY lots.expiry_date DESC;
SQL;
    $lots = selectData($sqlLots, $link);
    if ($lots === null) {
        $sqlErrors[]= mysqli_error($link);
    }

    $page_content = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);

    if (($categories === null) || ($lots === null)) {
        $page_content = include_template('error.php', ['error' => $sqlErrors]);
    }
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Главная страница',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
