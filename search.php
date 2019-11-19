<?php

require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$lots = [];
$search = $_GET['search'] ?? '';

if ($search) {
    $sql = "SELECT * FROM lots WHERE MATCH(name, description) AGAINST(?)";
    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

}
$page_content = include_template('search.php', ['categories' => $categories,
                                                       'search' => $search,
                                                       'lots' => $lots]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Результаты поиска',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
