-- USERS & AUTH
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(190) UNIQUE NOT NULL,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','sepuh','moderator','admin') NOT NULL DEFAULT 'user',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  last_login_at DATETIME NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE sessions (
  id CHAR(64) PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id), INDEX (expires_at)
) ENGINE=InnoDB;

CREATE TABLE device_binds (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  device_hash CHAR(64) NOT NULL,
  first_seen_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_seen_at DATETIME NULL,
  UNIQUE KEY uniq_user_device (user_id, device_hash),
  INDEX (device_hash),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bans (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  reason VARCHAR(255) NULL,
  until DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- CONTENT: SUBTESTS & DAILY SETS
CREATE TABLE subtests (
  id SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug VARCHAR(64) UNIQUE NOT NULL,
  name VARCHAR(128) NOT NULL,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- SUBTEST DATA
INSERT INTO subtests (slug, name, description) VALUES
('penalaran-umum', 'Kemampuan Penalaran Umum', 'Mengukur kemampuan memecahkan masalah baru dan bernalar secara abstrak. Terdiri dari penalaran induktif (sebab-akibat, kesesuaian pernyataan), penalaran deduktif (penalaran analitik, simpulan logis), dan penalaran kuantitatif (operasi aritmatika dasar).'),
('pengetahuan-umum', 'Pengetahuan dan Pemahaman Umum', 'Mengukur pemahaman dan kemampuan komunikasi dalam konteks budaya Indonesia, terutama keterampilan berbahasa, penggunaan kata, serta keluasan pengetahuan umum. Materi meliputi sinonim, ide pokok, dan hubungan antar paragraf.'),
('pemahaman-bacaan', 'Kemampuan Memahami Bacaan dan Menulis', 'Mengukur kemampuan memahami wacana tertulis dan menulis cerita. Materi mencakup simpulan, ide pokok, makna kata, ejaan, konjungsi, dan kalimat efektif.'),
('pengetahuan-kuantitatif', 'Pengetahuan Kuantitatif', 'Mengukur kemampuan menggunakan informasi kuantitatif dan memanipulasi simbol angka. Materi meliputi bilangan, geometri, aljabar, fungsi, statistika, dan peluang.'),
('literasi-bahasa-indonesia', 'Literasi dalam Bahasa Indonesia', 'Mengukur kemampuan memahami, menggunakan, mengevaluasi, dan merenungkan berbagai jenis teks dalam Bahasa Indonesia. Fokus pada kompetensi kebahasaan dan strategi kognitif untuk menemukan makna tersurat dan tersirat.'),
('literasi-bahasa-inggris', 'Literasi dalam Bahasa Inggris', 'Mengukur kemampuan memahami, menggunakan, mengevaluasi, dan merenungkan berbagai jenis teks dalam Bahasa Inggris. Fokus pada reading literacy untuk teks umum, sastra, saintek, dan humaniora.'),
('penalaran-matematika', 'Penalaran Matematika', 'Mengukur kemampuan merumuskan, menggunakan, dan menafsirkan masalah kuantitatif dalam konteks dunia nyata. Materi meliputi fungsi, bilangan, himpunan, pola bilangan, geometri, aljabar, peluang, dan statistika.');

CREATE TABLE daily_sets (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  subtest_id SMALLINT UNSIGNED NOT NULL,
  for_date DATE NOT NULL,
  mode ENUM('kejar_waktu','santai') NOT NULL,
  ai_prompt TEXT NOT NULL,
  ai_model VARCHAR(64) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_daily (subtest_id, for_date, mode),
  FOREIGN KEY (subtest_id) REFERENCES subtests(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE questions (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  daily_set_id BIGINT UNSIGNED NOT NULL,
  number TINYINT UNSIGNED NOT NULL, -- 1..5
  stem TEXT NOT NULL,
  image_url VARCHAR(255) NULL,
  explanation TEXT NULL, -- dari AI
  correct_choice CHAR(1) NOT NULL, -- A/B/C/D/E
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_qno (daily_set_id, number),
  FOREIGN KEY (daily_set_id) REFERENCES daily_sets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE choices (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  question_id BIGINT UNSIGNED NOT NULL,
  label CHAR(1) NOT NULL, -- A..E
  content TEXT NOT NULL,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_choice (question_id, label)
) ENGINE=InnoDB;

-- ATTEMPTS
CREATE TABLE attempts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  subtest_id SMALLINT UNSIGNED NOT NULL,
  for_date DATE NOT NULL,
  mode ENUM('kejar_waktu','santai') NOT NULL,
  started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  finished_at DATETIME NULL,
  device_hash CHAR(64) NOT NULL,
  score TINYINT UNSIGNED NULL, -- 0..5
  duration_seconds INT UNSIGNED NULL,
  status ENUM('ongoing','submitted','forfeit') NOT NULL DEFAULT 'ongoing',
  UNIQUE KEY uniq_user_day (user_id, for_date),
  UNIQUE KEY uniq_device_day (device_hash, for_date),
  INDEX (subtest_id, for_date),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (subtest_id) REFERENCES subtests(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attempt_items (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  attempt_id BIGINT UNSIGNED NOT NULL,
  question_id BIGINT UNSIGNED NOT NULL,
  chosen CHAR(1) NULL,
  is_correct TINYINT(1) NULL,
  responded_at DATETIME NULL,
  FOREIGN KEY (attempt_id) REFERENCES attempts(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_attempt_question (attempt_id, question_id)
) ENGINE=InnoDB;

-- BATTLE
CREATE TABLE battle_rooms (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  subtest_id SMALLINT UNSIGNED NOT NULL,
  for_date DATE NOT NULL,
  mode ENUM('kejar_waktu','santai') NOT NULL,
  status ENUM('waiting','active','finished','cancelled') NOT NULL DEFAULT 'waiting',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  FOREIGN KEY (subtest_id) REFERENCES subtests(id) ON DELETE CASCADE,
  INDEX (subtest_id, for_date, status)
) ENGINE=InnoDB;

CREATE TABLE battle_participants (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  battle_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  device_hash CHAR(64) NOT NULL,
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  score TINYINT UNSIGNED NULL,
  duration_seconds INT UNSIGNED NULL,
  is_winner TINYINT(1) NULL,
  FOREIGN KEY (battle_id) REFERENCES battle_rooms(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_battle_user (battle_id, user_id)
) ENGINE=InnoDB;

CREATE TABLE battle_events (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  battle_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  event_type VARCHAR(32) NOT NULL, -- join, answer, submit, finish
  payload JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (battle_id, created_at),
  FOREIGN KEY (battle_id) REFERENCES battle_rooms(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SCOREBOARD CACHE (opsional)
CREATE TABLE scoreboard_cache (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  subtest_id SMALLINT UNSIGNED NOT NULL,
  for_date DATE NOT NULL,
  mode ENUM('kejar_waktu','santai') NOT NULL,
  payload JSON NOT NULL,
  built_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_board (subtest_id, for_date, mode)
) ENGINE=InnoDB;