<?php

require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$rates = [];

$sqlRates = <<<SQL
SELECT lots.winner_id, (SELECT users.contacts FROM users WHERE users.id = lots.winner_id) AS contacts, lots.id AS lot_id, lots.img AS lot_img, lots.expiry_date, lots.name AS lot_name, categories.name AS lot_category, bets.price, bets.dt_add FROM bets 
JOIN lots ON lots.id = bets.lot_id JOIN categories ON categories.id = lots.category_id  
WHERE bets.user_id = $user_id ORDER BY bets.dt_add DESC
SQL;

$result = mysqli_query($link, $sqlRates);

if ($result) {
    if (!mysqli_num_rows($result)) {
        $page_content = include_template('error.php', ['error' => 'Ставок нет']);
    } else {
        $rates = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($rates as $key => $rate) {
            $expiryTime = get_dt_range($rate['expiry_date']);

            $rates[$key]['timer_class'] = '';
            $rates[$key]['timer_message'] = date_format( date_create($rate['expiry_date']), 'd.m.Y в H:i');

            if ((int) $expiryTime[0] === 0 && !empty($expiryTime)) {
                $rates[$key]['timer_class'] = 'timer--finishing';
                $rates[$key]['timer_message'] = implode(':', $expiryTime);
            }

            if (date_create("now") > date_create($rate['expiry_date'])) {
                $rates[$key]['timer_class'] = 'timer--end';
                $rates[$key]['timer_message'] = 'Торги окончены';
                $rates[$key]['rate_class'] = 'rates__item--end';
            }

            if ($rate['winner_id']) {
                $rates[$key]['timer_class'] = 'timer--win';
                $rates[$key]['timer_message'] = 'Ставка выиграла';
                $rates[$key]['rate_class'] = 'rates__item--win';
            }
        }

        $page_content = include_template('my-bets.php', ['categories' => $categories,
                                                                'rates' => $rates]);
    }
} else {
    $page_content = include_template('error.php', ['error' => 'Ошибка запроса']);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Мои лоты',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'is_main' => false
]);

print($layout_content);
