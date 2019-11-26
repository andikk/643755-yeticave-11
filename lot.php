<?php
require_once('init.php');
require_once('helpers.php');
require_once('data.php');
$errors = [];
$categories = getCategories($link);
$lot_id = filter_input(INPUT_GET, 'id');

$sql_lot = <<<SQL
SELECT lots.id, lots.name, lots.description, lots.user_id, lots.first_price, lots.img, lots.expiry_date, lots.step, categories.name AS category,
   CASE
        WHEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id) > 0 THEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id)
        ELSE lots.first_price
   END AS price
   FROM lots JOIN categories ON lots.category_id = categories.id
   WHERE lots.id = '%s';
SQL;

$sql_lot = sprintf($sql_lot, $lot_id);
$result = mysqli_query($link, $sql_lot);

if (!$result) {
    die(mysqli_error($link));
}

if (!mysqli_num_rows($result)) {
    http_response_code(404);
    $page_content = include_template('error.php', ['error' => 'Даннай лот не найден',
                                                          'categories' => $categories]);
    $title = "Даннай лот не найден";
} else {
    $lot = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
    $min_price = $lot['price'];
    $step = $lot['step'];
    $bets = [];

    $sql_bets = "SELECT users.name AS user, bets.price AS price, bets.dt_add AS time, users.id AS user_id FROM bets JOIN users ON bets.user_id = users.id WHERE bets.lot_id = $lot_id ORDER BY bets.dt_add DESC";
    $sql_bets_result = mysqli_query($link, $sql_bets);

    if ($sql_bets_result && mysqli_num_rows($sql_bets_result)) {
        $bets = mysqli_fetch_all($sql_bets_result, MYSQLI_ASSOC);
        $last_bet_added_by_current_user = ($bets[0]['user_id'] === $user_id);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_auth) {
        $required = ['cost'];
        $form = $_POST;

        $rules = [
            'cost' => function($value) use ($min_price, $step) {
                return validateCost($value, $min_price, $step);
            }
        ];

        $fields = ['cost' => 'Ставка'];
        $errors = validatePostData($form, $rules, $required, $fields);

        if (!$errors['cost']) {
            $bet = [$user_id, $lot['id'], $form['cost']];
            $sql = 'INSERT INTO bets (user_id, lot_id, price) VALUES (?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, $bet);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                $lot_id = mysqli_insert_id($link);
                header("Location: lot.php?id=" . $lot['id']);
            }
        }
    }

    $lot_is_open = (date_create($lot['expiry_date']) > date_create("now"));
    $lot_of_current_user = ($lot['user_id'] === $user_id);
    $show_bet_block = ($is_auth && $lot_is_open && !$lot_of_current_user && !$last_bet_added_by_current_user);

    $page_content = include_template('lot.php', ['categories' => $categories,
                                                        'lot' => $lot,
                                                        'expiryTime' => get_dt_range($lot['expiry_date']),
                                                        'is_auth' => $is_auth,
                                                        'show_bet_block' => $show_bet_block,
                                                        'errors' => $errors,
                                                        'bets' => $bets]);

}

$layout_content = include_template('layout.php', ['content' => $page_content,
    'categories' => $categories,
    'title' => $lot['name'] ?? $title,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'is_main' => false
]);

print($layout_content);


