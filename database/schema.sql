CREATE TABLE users (
  id INT AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('guest', 'user', 'admin') NOT NULL DEFAULT 'guest',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_email (email),
  INDEX idx_username (username)
);

CREATE TABLE venues (
  id INT AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  address VARCHAR(255) NOT NULL,
  capacity INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_name (name)
);

CREATE TABLE events (
  id INT AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  venue_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (venue_id) REFERENCES venues(id),
  INDEX idx_title (title),
  INDEX idx_start_date (start_date),
  INDEX idx_end_date (end_date)
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  venue_id INT NOT NULL,
  booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (event_id) REFERENCES events(id),
  FOREIGN KEY (venue_id) REFERENCES venues(id),
  INDEX idx_user_id (user_id),
  INDEX idx_event_id (event_id),
  INDEX idx_venue_id (venue_id)
);

INSERT INTO users (username, email, password, role) VALUES
  ('admin', 'admin@example.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin'),
  ('user1', 'user1@example.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'user'),
  ('guest1', 'guest1@example.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'guest');

INSERT INTO venues (name, address, capacity) VALUES
  ('Venue 1', '123 Main St', 100),
  ('Venue 2', '456 Elm St', 200),
  ('Venue 3', '789 Oak St', 300);

INSERT INTO events (title, description, start_date, end_date, venue_id) VALUES
  ('Event 1', 'This is event 1', '2024-01-01 10:00:00', '2024-01-01 12:00:00', 1),
  ('Event 2', 'This is event 2', '2024-01-02 10:00:00', '2024-01-02 12:00:00', 2),
  ('Event 3', 'This is event 3', '2024-01-03 10:00:00', '2024-01-03 12:00:00', 3);

INSERT INTO bookings (user_id, event_id, venue_id) VALUES
  (1, 1, 1),
  (2, 2, 2),
  (3, 3, 3);