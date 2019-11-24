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

    $sql = "SELECT * FROM lots WHERE MATCH(name, description) AGAINST(?)";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $items_count =  (mysqli_num_rows($result));
        $pages_count = ceil($items_count / $page_items);
        $offset = ($cur_page - 1) * $page_items;
        $pages = range(1, $pages_count);

        $sqlSearch = 'SELECT * FROM lots WHERE MATCH(name, description) AGAINST(?) LIMIT ' . $page_items . ' OFFSET ' . $offset;
        $stmtSearch = db_get_prepare_stmt($link, $sqlSearch, [$search]);
        mysqli_stmt_execute($stmtSearch);
        $resultSearch = mysqli_stmt_get_result($stmtSearch);

        $lots = mysqli_fetch_all($resultSearch, MYSQLI_ASSOC);

    } else {
        die(mysqli_error($link));
    }
}

$prevPageLink = !($cur_page - 1) ? '#' : $url . '&page=' . ($cur_page - 1);
$nextPageLink = ((int) $cur_page === (int) $pages_count) ? '#' : $url . '&page=' . ($cur_page + 1);

$page_content = include_template('search.php', ['categories' => $categories,
                                                       'search' => $search,
                                                       'lots' => $lots,
                                                       'pages' => $pages,
                                                       'pages_count' => $pages_count,
                                                       'url' => $url,
                                                       'cur_page' => $cur_page,
                                                       'prevPageLink' => $prevPageLink,
                                                       'nextPageLink' => $nextPageLink]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Результаты поиска',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'isMain' => false
]);

print($layout_content);
