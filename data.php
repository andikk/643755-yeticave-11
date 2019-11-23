<?php
$is_auth = isset($_SESSION['user']);
$user_name = (isset($_SESSION['user'])) ? $_SESSION['user']['name'] : '';
$user_id = (isset($_SESSION['user'])) ? $_SESSION['user']['id'] : '';
