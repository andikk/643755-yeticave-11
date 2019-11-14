<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Форматирует цену
 *
 * @param string $price цена
 *
 * @return string отформатированная цена
 */
function format_price($price) {
    $formattedPrice = ceil($price);
    if ($formattedPrice >= 1000) {
        $formattedPrice = number_format($formattedPrice, 0, '', ' ');
    }

    return $formattedPrice . ' ₽';
}

/**
 * Возаращает время истечения лота
 *
 * @param string expiry_date дата истечения лота
 *
 * @return array дата и время истечения лота
 */
function get_dt_range($expiry_date) {
    $dt_now = date_create("now");
    $dt_end = date_create($expiry_date);
    $dt_diff = date_diff($dt_end, $dt_now);
    $days_count = date_interval_format($dt_diff, "%a");
    if ($days_count == 0 && $dt_end > $dt_now) {
        $hours_count = date_interval_format($dt_diff, "%H %I");
        return explode(' ', $hours_count);
    }

    return null;
}

/**
 * Возаращает отформатированную строку
 *
 * @param string str строка для форматирования
 *
 * @return string отформатированная строка
 */
function esc($str) {
    $text = htmlspecialchars($str);
    return $text;
}

/**
 * Возаращает список категорий
 *
 * @param string $link строка подключения
 *
 * @return array массив категорий
 */
function getCategories($link) {
    $sqlCategories = 'SELECT `id`, `char_code`, `name` FROM categories';
    $result = mysqli_query($link, $sqlCategories);
    if ($result) {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $categories;
    } else {
        die(mysqli_error($link));
    }
}

/**
 * Функция выборки данных из БД
 *
 * @param string $link строка подключения
 * @param string $sql шаблон запроса
 * @param array $data массив для подстановки значений в запрос
 *
 * @return array результат запроса
 */
function db_fetch_data($link, $sql, $data = []) {
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}

/**
 * Функция добавления данны в БД
 *
 * @param string $link строка подключения
 * @param string $sql шаблон запроса
 * @param array $data массив для подстановки значений в запрос
 *
 * @return bool результат исполнения операции
 */
function db_insert_data($link, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }
    return $result;
}

/**
 * Функция получения значения из параметра пост-запроса
 *
 * @param string $name строка с наименованием параметра пост-запроса
 *
 * @return string значение параметра пост-запроса
 */
function getPostVal($name) {
    return filter_input(INPUT_POST, $name);
}

/**
 * Функция валидации категории
 *
 * @param string $id id переданной категории
 * @param string $allowed_list массив, из которого будут выбираться категории
 *
 * @return string текст ошибки валидации
 */
function validateCategory($id, $allowed_list) {
    if (!in_array($id, $allowed_list)) {
        return "Указана несуществующая категория";
    }

    return null;
}


/**
 * Функция валидации длиный поля
 *
 * @param string $value значения поля
 * @param int $min минимальная длина поля
 * @param int $max максимальная длина поля
 *
 * @return string текст ошибки валидации
 */
function validateLength($value, $min, $max) {
    if ($value) {
        $len = strlen($value);
        if ($len < $min || $len > $max) {
            return "Значение должно быть от $min до $max символов";
        }
    }

    return null;
}

/**
 * Функция валидации цены лота
 *
 * @param string $value значения поля
 *
 * @return string текст ошибки валидации
 */
function validatePrice($value) {
    if ((float) $value < 0) {
        return "Значение должно быть больше 0";
    }

    return null;
}

/**
 * Функция валидации шага лота
 *
 * @param string $value значения поля
 *
 * @return string текст ошибки валидации
 */
function validateStep($value) {
    if ((int) $value < 0) {
        return "Значение должно быть больше 0";
    }

    return null;
}

/**
 * Функция валидации даты истечения лота
 *
 * @param string $value значения поля
 *
 * @return string текст ошибки валидации
 */
function validateDate($value) {
    $future_dt = date('Y-m-d', strtotime("+1 days"));

    if ($value < $future_dt || !is_date_valid($value)) {
        return "Дата должна быть на один день больше текущей даты, а также должна быть в формате ГГГГ-ММ-ДД";
    }

    return null;
}
