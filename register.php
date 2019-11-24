<?php
require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;
    $required = ['email', 'password', 'name', 'message'];

    $rules = [
        'email' => function($value) {
            return validateEmail($value);
        }
    ];

    $fields = [
        'email' => 'E-mail',
        'password' => 'Пароль',
        'name' => 'Имя',
        'message' => 'Контактные данные',
    ];

    $errors = validatePostData($form, $rules, $required, $fields);
    $errors = array_filter($errors);

    if (empty($errors)) {
        $email = mysqli_real_escape_string($link, $form['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        } else {
            $password = password_hash($form['password'], PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (email, name, password, contacts) VALUES (?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [$form['email'], $form['name'], $password, $form['message']]);
            $res = mysqli_stmt_execute($stmt);
        }

        if ($res && empty($errors)) {
            header("Location: /login.php");
            exit();
        }
    }
}

$page_content = include_template('reg.php', ['categories' => $categories, 'errors' => $errors,]);

if ($is_auth) {
    http_response_code(403);
    $page_content = include_template('error.php', ['error' => 'Вы уже зарегестрированы']);
    $page_title = "Ошибка";
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Регистрация пользователя',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'is_main' => false
]);

print($layout_content);
