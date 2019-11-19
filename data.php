<?php
$is_auth = isset($_SESSION['user']);
$user_name = (isset($_SESSION['user'])) ? $_SESSION['user']['name'] : '';
