<?php

require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$lots = [];
$category_id = filter_input(INPUT_GET, 'id');

$url = '/category.php?id=' . $category_id;
$cur_page = $_GET['page'] ?? 1;
$page_items = 9;

$sql = <<<SQL
SELECT lots.*, categories.name AS category_name FROM lots JOIN categories ON  lots.category_id = categories.id
WHERE lots.category_id = '%s' AND lots.expiry_date > CURDATE() AND lots.winner_id = '0'
SQL;

$sql = sprintf($sql, $category_id);
$result = mysqli_query($link, $sql);

if (!$result) {
    die(mysqli_error($link));
}

if (mysqli_num_rows($result)) {
    $items_count =  (mysqli_num_rows($result));
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
    $pages = range(1, $pages_count);

    $sql_lots = <<<SQL
SELECT lots.*, categories.name AS category FROM lots JOIN categories ON  lots.category_id = categories.id
WHERE lots.category_id = '%s' AND lots.expiry_date > CURDATE() AND lots.winner_id = '0' LIMIT  $page_items  OFFSET $offset;
SQL;
    $sql_lots = sprintf($sql_lots, $category_id);
    $result_lots = mysqli_query($link, $sql_lots);
    $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

    $prev_page_link = !($cur_page - 1) ? '#' : $url . '&page=' . ($cur_page - 1);
    $next_page_link = ((int) $cur_page === (int) $pages_count) ? '#' : $url . '&page=' . ($cur_page + 1);
    $title = 'Все лоты в категории ' . empty($lots) ? '' : $lots[0]['category'];
    $page_content = include_template('category.php', ['categories' => $categories,
        'category_name' => $lots[0]['category'] ?? '',
        'lots' => $lots,
        'pages' => $pages,
        'pages_count' => $pages_count,
        'url' => $url,
        'cur_page' => $cur_page,
        'prev_page_link' => $prev_page_link,
        'next_page_link' => $next_page_link]);
} else {
    $page_content = include_template('error.php', ['error' => 'В данной категории нет лотов или категория не существует',
                                                          'categories' => $categories]);
    $title = "В данной категории нет лотов";
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'is_main' => false
]);

print($layout_content);
