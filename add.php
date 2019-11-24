<?php
require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);

$cats_ids = [];
$cats_ids = array_column($categories, 'id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['lot-name', 'category-id', 'message', 'lot-img', 'lot-rate', 'lot-step', 'lot-date'];
    $errors = [];

    $rules = [
        'category-id' => function($value) use ($cats_ids) {
            return validateCategory($value, $cats_ids);
        },
        'lot-name' => function($value) {
            return validateLength($value, 10, 200);
        },
        'message' => function($value) {
            return validateLength($value, 10, 3000);
        },
        'lot-date' => function($value) {
            return validateDate($value);
        },
        'lot-rate' => function($value) {
            return validatePrice($value);
        },
        'lot-step' => function($value) {
            return validateStep($value);
        }
    ];
    $lot = filter_input_array(INPUT_POST, ['lot-name' => FILTER_DEFAULT, 'message' => FILTER_DEFAULT,
        'category-id' => FILTER_DEFAULT, 'lot-date' => FILTER_DEFAULT, 'lot-rate' => FILTER_DEFAULT, 'lot-step' => FILTER_DEFAULT], true);

    $fields = [
        'lot-name' => 'Наименование',
        'message' => 'Описание',
        'category-id' => 'Категория',
        'lot-date' => 'Дата окончания торгов ',
        'lot-rate' => 'Начальная цена',
        'lot-step' => 'Шаг ставки',
    ];

    $errors = validatePostData($lot, $rules, $required, $fields);
    $errors = array_filter($errors);

    if (!empty($_FILES['lot-img']['name'])) {
        $tmp_name = $_FILES['lot-img']['tmp_name'];
        $path = $_FILES['lot-img']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/jpeg" && $file_type !== "image/png") {
            $errors['file'] = 'Загрузите картинку в форматах jpg, jpeg, png';
        }
        else {
            if (!count($errors)) {
                move_uploaded_file($tmp_name, 'uploads/' . $filename);
                $lot['path'] = $filename;
            }
        }
    }
    else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    if (count($errors)) {
        $page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'categories' => $categories]);
    }
    else {
        $lot['user_id'] = $_SESSION['user']['id'];
        $sql = 'INSERT INTO lots (name, description, category_id, expiry_date, first_price, step, img, user_id, winner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)';
        $stmt = db_get_prepare_stmt($link, $sql, $lot);

        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($link);

            header("Location: lot.php?id=" . $lot_id);
        };
    }
} else {
    $page_content = include_template('add.php', ['categories' => $categories]);
}

if (!$is_auth) {
    http_response_code(403);
    $page_content = include_template('error.php', ['error' => 'Страница доступна только для авторизованных пользователей']);
    $page_title = "Ошибка";
}


$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Добавление лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'isMain' => false,
]);

print($layout_content);
