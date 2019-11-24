<?php

require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$lots = [];
$search = trim($_GET['search']) ?? '';

if ($search) {
    $url = '/search.php?search=' . $_GET['search'];
    $cur_page = $_GET['page'] ?? 1;
    $page_items = 9;

    $sql = "SELECT * FROM lots WHERE MATCH(name, description) AGAINST(?) AND lots.expiry_date > CURDATE() AND lots.winner_id = 0";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $items_count =  (mysqli_num_rows($result));
        $pages_count = ceil($items_count / $page_items);
        $offset = ($cur_page - 1) * $page_items;
        $pages = range(1, $pages_count);

        $sql_search = 'SELECT * FROM lots WHERE MATCH(name, description) AGAINST(?) AND lots.expiry_date > CURDATE() AND lots.winner_id = 0 LIMIT ' . $page_items . ' OFFSET ' . $offset;
        $stmt_search = db_get_prepare_stmt($link, $sql_search, [$search]);
        mysqli_stmt_execute($stmt_search);
        $result_search = mysqli_stmt_get_result($stmt_search);

        $lots = mysqli_fetch_all($result_search, MYSQLI_ASSOC);
    } else {
        die(mysqli_error($link));
    }
}

$prev_page_link = !($cur_page - 1) ? '#' : $url . '&page=' . ($cur_page - 1);
$next_page_link = ((int) $cur_page === (int) $pages_count) ? '#' : $url . '&page=' . ($cur_page + 1);

$page_content = include_template('search.php', ['categories' => $categories,
                                                       'search' => $search,
                                                       'lots' => $lots,
                                                       'pages' => $pages,
                                                       'pages_count' => $pages_count,
                                                       'url' => $url,
                                                       'cur_page' => $cur_page,
                                                       'prev_page_link' => $prev_page_link,
                                                       'next_page_link' => $next_page_link]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Результаты поиска по запросу ' . $search,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'is_main' => false
]);

print($layout_content);
