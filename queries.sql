INSERT INTO categories SET name = 'Доски и лыжи', char_code = 'boards';
INSERT INTO categories SET name = 'Крепления', char_code = 'attachment';
INSERT INTO categories SET name = 'Ботинки', char_code = 'boots';
INSERT INTO categories SET name = 'Одежда', char_code = 'clothing';
INSERT INTO categories SET name = 'Инструменты', char_code = 'tools';
INSERT INTO categories SET name = 'Разное', char_code = 'other';

INSERT INTO users SET email = 'andikk@mail.ru', name = 'Андрей', password = '123', contacts = 'Ставрополь. тел.: 8-906-444-37-37';
INSERT INTO users SET email = 'ivan@mail.ru', name = 'Иван', password = '123', contacts = 'Михайловск. тел.: 8-906-444-37-33';
INSERT INTO users SET email = 'serg@mail.ru', name = 'Сергей', password = '123', contacts = 'Невинномысск. тел.: 8-906-444-37-33';

INSERT INTO lots SET user_id = 1,
                     category_id = 1,
                     winner_id = 0,
                     name = '2014 Rossignol District Snowboard',
                     description = 'Описание товара',
                     img = 'img/lot-1.jpg',
                     first_price = 10999,
                     expiry_date = '2019-11-04',
                     step = 1000;

INSERT INTO lots SET user_id = 1,
                     category_id = 1,
                     winner_id = 0,
                     name = 'DC Ply Mens 2016/2017 Snowboard',
                     description = 'Описание товара',
                     img = 'img/lot-2.jpg',
                     first_price = 159999,
                     expiry_date = '2019-11-05',
                     step = 1000;

INSERT INTO lots SET user_id = 2,
                     category_id = 2,
                     winner_id = 0,
                     name = 'Крепления Union Contact Pro 2015 года размер L/XL',
                     description = 'Описание товара',
                     img = 'img/lot-3.jpg',
                     first_price = 8000,
                     expiry_date = '2019-11-06',
                     step = 1000;

INSERT INTO lots SET user_id = 2,
                     category_id = 3,
                     winner_id = 0,
                     name = 'Ботинки для сноуборда DC Mutiny Charocal',
                     description = 'Описание товара',
                     img = 'img/lot-4.jpg',
                     first_price = 10999,
                     expiry_date = '2019-11-07',
                     step = 500;

INSERT INTO lots SET user_id = 1,
                     category_id = 4,
                     winner_id = 0,
                     name = 'Куртка для сноуборда DC Mutiny Charocal',
                     description = 'Описание товара',
                     img = 'img/lot-5.jpg',
                     first_price = 7500,
                     expiry_date = '2019-11-08',
                     step = 500;

INSERT INTO lots SET user_id = 1,
                     category_id = 6,
                     winner_id = 0,
                     name = 'Маска Oakley Canopy',
                     description = 'Описание товара',
                     img = 'img/lot-6.jpg',
                     first_price = 5400,
                     expiry_date = '2019-11-09',
                     step = 500;

INSERT INTO bets SET user_id = 1, lot_id = 3, price = 9000;
INSERT INTO bets SET user_id = 3, lot_id = 3, price = 10000;
INSERT INTO bets SET user_id = 2, lot_id = 1, price = 11999;

-- получить все категории;
SELECT * FROM categories;

-- получить самые новые, открытые лоты.
-- Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
SELECT lots.name, lots.first_price, lots.img, categories.name FROM lots JOIN categories ON lots.category_id = categories.id WHERE lots.expiry_date > CURDATE() ORDER BY lots.expiry_date DESC;

SELECT lots.name, lots.first_price, lots.img, categories.name,
       (SELECT MAX(price) FROM bets WHERE bets.lot_id = lots.id) AS price
       FROM lots JOIN categories ON lots.category_id = categories.id
       WHERE lots.expiry_date > CURDATE() ORDER BY lots.expiry_date DESC;

-- показать лот по его id. Получите также название категории, к которой принадлежит лот;
SELECT lots.name, categories.name FROM lots JOIN categories ON lots.category_id = categories.id WHERE lots.id = 1

-- обновить название лота по его идентификатору;
UPDATE lots SET name = 'Новое название лота' WHERE id = 1;

-- получить список ставок для лота по его идентификатору с сортировкой по дате.
SELECT * FROM bets WHERE lot_id = 3 ORDER BY dt_add DESC;
