<?php

require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$rates = [];

$sqlRates = <<<SQL
SELECT users.contacts, lots.id as lot_id, lots.img as lot_img, lots.expiry_date, lots.name as lot_name, categories.name as lot_category, bets.price, bets.dt_add FROM bets 
JOIN lots ON lots.id = bets.lot_id JOIN categories ON categories.id = lots.category_id JOIN users ON users.id = lots.winner_id 
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

            if ($rate['contacts']) {
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
    'user_name' => $user_name
]);

print($layout_content);
