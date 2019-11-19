<?php
require_once('init.php');
require_once('helpers.php');
require_once('data.php');

$categories = getCategories($link);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = $_POST;

    $required = ['email', 'password'];

    $fields = [
        'email' => 'E-mail',
        'password' => 'Пароль'
    ];

    $errors = validatePostData($form, [], $required, $fields);
    $errors = array_filter($errors);

    $email = mysqli_real_escape_string($link, $form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) && $user) {
        if (password_verify($form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        }
        else {
            $errors['password'] = 'Неверный пароль';
        }
    } else {
        if (!count($errors)) {
            $errors['email'] = 'Такой пользователь не найден';
        }
    }

    if (!count($errors)) {
        header("Location: /index.php");
        exit();
    }
} else {
    $page_content = include_template('login.php', []);

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}

$page_content = include_template('login.php', ['categories' => $categories, 'errors' => $errors,]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Регистрация пользователя',
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
