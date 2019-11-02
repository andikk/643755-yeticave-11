CREATE DATABASE yeticave DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  char_code VARCHAR(255) NOT NULL UNIQUE
);

CREATE UNIQUE INDEX name_categories ON categories(name);
CREATE UNIQUE INDEX char_code_categories ON categories(char_code);

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(255) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  contacts TEXT NOT NULL
);

CREATE UNIQUE INDEX email_users ON users(email);
CREATE INDEX name_users ON users(name);

CREATE TABLE lots (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  winner_id INT UNSIGNED NOT NULL,
  dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  img VARCHAR(255) NOT NULL,
  first_price INT UNSIGNED NOT NULL,
  expiry_date DATETIME NOT NULL,
  step INT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (winner_id) REFERENCES users(id),
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE INDEX user_id_lots ON lots(user_id);
CREATE INDEX category_id_lots ON lots(category_id);
CREATE INDEX winner_id_lots ON lots(winner_id);
CREATE INDEX dt_add_lots ON lots(dt_add);
CREATE INDEX name_lots ON lots(name);
CREATE INDEX first_price_lots ON lots(first_price);
CREATE INDEX expiry_date_lots ON lots(expiry_date);
CREATE INDEX step_lots ON lots(step);

CREATE TABLE bets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  lot_id INT UNSIGNED NOT NULL,
  dt_add DATETIME DEFAULT CURRENT_TIMESTAMP,
  price INT UNSIGNED NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (lot_id) REFERENCES lots(id)
);

CREATE INDEX user_id_bets ON bets(user_id);
CREATE INDEX lot_id_bets ON bets(lot_id);
CREATE INDEX dt_add_bets ON bets(dt_add);
CREATE INDEX price_bets ON bets(price);
