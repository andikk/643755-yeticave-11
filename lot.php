<?php
require_once('init.php');
require_once('helpers.php');
require_once('data.php');
$errors = [];
$categories = getCategories($link);
$lotId = filter_input(INPUT_GET, 'id');

$sqlLot = <<<SQL
SELECT lots.id, lots.name, lots.first_price, lots.img, lots.expiry_date, lots.step, categories.name AS category,
   CASE
        WHEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id) > 0 THEN (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id)
        ELSE lots.first_price
   END AS price
   FROM lots JOIN categories ON lots.category_id = categories.id
   WHERE lots.id = '%s';
SQL;

$sqlLot = sprintf($sqlLot, $lotId);
$result = mysqli_query($link, $sqlLot);

if ($result) {
    if (!mysqli_num_rows($result)) {
        http_response_code(404);
        $page_content = include_template('error.php', ['error' => 'Даннай лот не найден']);
        $page_title = "Даннай лот не найден";
    } else {
        $lot = mysqli_fetch_all($result, MYSQLI_ASSOC)[0];
        $minPrice = $lot['price'];
        $step = $lot['step'];
        $bets = [];

        $sqlBets = "SELECT users.name AS user, bets.price AS price, bets.dt_add AS time, users.id AS user_id FROM bets JOIN users ON bets.user_id = users.id WHERE bets.lot_id = $lotId ORDER BY bets.dt_add DESC";
        $sqlBetsResult = mysqli_query($link, $sqlBets);

        if ($sqlBetsResult) {
           if (mysqli_num_rows($sqlBetsResult)) {
                $bets = mysqli_fetch_all($sqlBetsResult, MYSQLI_ASSOC);
           }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($is_auth)) {
            $required = ['cost'];
            $form = $_POST;

            $rules = [
                'cost' => function($value) use ($minPrice, $step) {
                    return validateCost($value, $minPrice, $step);
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

        $lotIsOpen = (date_create($lot['expiry_date']) > date_create("now"));
        $lastBetAddedByCurrentUser = ($bets[0]['user_id'] === $user_id);
        $lotOfCurrentUser = ($lot['user_id'] === $user_id);

        $showBetBlock = ($is_auth && $lotIsOpen && !$lotOfCurrentUser && !$lastBetAddedByCurrentUser);

        $page_content = include_template('lot.php', ['categories' => $categories,
                                                            'lot' => $lot,
                                                            'expiryTime' => get_dt_range($lot['expiry_date']),
                                                            'is_auth' => $is_auth,
                                                            'showBetBlock' => $showBetBlock,
                                                            'errors' => $errors,
                                                            'bets' => $bets]);
    }
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => $lot['name'],
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'isMain' => false,
]);

print($layout_content);
