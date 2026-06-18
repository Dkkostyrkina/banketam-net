CREATE DATABASE IF NOT EXISTS banket CHARACTER SET utf8mb4;
USE banket;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(100) NOT NULL,
  role VARCHAR(10) NOT NULL DEFAULT 'user'
);

-- Добавляем администратора Admin26 / Demo20
INSERT INTO users (login, password_hash, name, phone, email, role)
VALUES ('Admin26', '$2y$10$YourHashHere', 'Администратор', '8(000)000-00-00', 'admin@banket.ru', 'admin');
-- Пример: password_hash нужно заменить на hash от 'Demo20'
-- В PHP: echo password_hash('Demo20', PASSWORD_DEFAULT);

CREATE TABLE halls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  capacity INT DEFAULT 0,
  price INT DEFAULT 0,
  image VARCHAR(100) DEFAULT 'unnamed.jpg'
);

INSERT INTO halls (title, description, capacity, price, image) VALUES
  ('Банкетный зал', 'Просторный банкетный зал с классическим интерьером. Идеально для торжественных мероприятий.', 100, 15000, '1a37a34a4d9e6a51c8e87b6af9ab69d6.jpg'),
  ('Ресторан', 'Уютный ресторан с живой музыкой и изысканным меню. Атмосфера роскоши и комфорта.', 60, 20000, 'unnamed (1).jpg'),
  ('Летняя веранда', 'Открытая летняя веранда с панорамным видом на сад. Подходит для сезонных банкетов.', 50, 12000, '1671649122_idei-club-p-veranda-.jpg'),
  ('Закрытая веранда', 'Уютная закрытая веранда с естественным освещением. Комфорт в любую погоду.', 40, 10000, '1686676944_elles-top-p-letnyaya-ploshcha.jpg');

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  hall_id INT NULL,
  start_date VARCHAR(10) NOT NULL,
  payment VARCHAR(20) NOT NULL,
  status VARCHAR(30) DEFAULT 'Новая',
  review TEXT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE SET NULL
);
