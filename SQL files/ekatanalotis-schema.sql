DROP SCHEMA IF EXISTS ekatanalotis;
CREATE SCHEMA ekatanalotis;
USE ekatanalotis;


CREATE TABLE user (
  username VARCHAR(45) NOT NULL,
  password VARCHAR(45) NOT NULL,
  type ENUM('admin','user') NOT NULL,
  t_score INT UNSIGNED DEFAULT 0,
  m_score SMALLINT UNSIGNED DEFAULT 0,
  t_tokens INT UNSIGNED DEFAULT 0,
  m_tokens SMALLINT UNSIGNED DEFAULT 0,
  signup_date DATE NOT NULL,
  PRIMARY KEY  (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE categories (
  cid VARCHAR(45) NOT NULL,
  name VARCHAR(45) NOT NULL,
  PRIMARY KEY  (cid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE subcategories (
  uuid VARCHAR(45) NOT NULL,
  name VARCHAR(45) NOT NULL,
  category VARCHAR(45) NOT NULL,
  PRIMARY KEY  (uuid),
  CONSTRAINT `fk_sub_category` FOREIGN KEY (category) REFERENCES categories (cid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE products (
  id SMALLINT NOT NULL,
  name VARCHAR(45) NOT NULL UNIQUE,
  category VARCHAR(45) NOT NULL,
  subcategory VARCHAR(45) NOT NULL,
  PRIMARY KEY  (id),
  CONSTRAINT `fk_prod_subcategory` FOREIGN KEY (subcategory) REFERENCES subcategories (uuid) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_category` FOREIGN KEY (category) REFERENCES categories (cid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE prices (
  name VARCHAR(45) NOT NULL,
  date DATE NOT NULL,
  price DECIMAL(5,2) NOT NULL,
  PRIMARY KEY  (name, date), 
  CONSTRAINT `fk_prod_name` FOREIGN KEY (name) REFERENCES products (name) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE pois (
  id BIGINT NOT NULL,
  name VARCHAR(45),
  latitude DECIMAL(9,7) NOT NULL,
  longitude DECIMAL(9,7) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE offers (
  id SMALLINT NOT NULL AUTO_INCREMENT,
  username VARCHAR(45) NOT NULL,
  p_id SMALLINT NOT NULL,
  lcount SMALLINT DEFAULT 0,
  dcount SMALLINT DEFAULT 0,
  price DECIMAL(5,2) NOT NULL,
  ful_criteria ENUM('yes','no'),
  sub_date DATE NOT NULL,
  poi_id BIGINT NOT NULL,
  stock ENUM('ναι','όχι'),
  exp_date DATE NOT NULL,
  PRIMARY KEY  (id),
  CONSTRAINT `fk_offer_user` FOREIGN KEY (username) REFERENCES user (username) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_offer_prod` FOREIGN KEY (p_id) REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_offer_poi` FOREIGN KEY (poi_id) REFERENCES pois (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE images (
  id SMALLINT NOT NULL,
  url VARCHAR(45) NOT NULL DEFAULT 'none',
  p_id SMALLINT NOT NULL,
  PRIMARY KEY  (id),
  CONSTRAINT `fk_image_prod` FOREIGN KEY (p_id) REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE ldhistory (
  id SMALLINT NOT NULL AUTO_INCREMENT,
  username VARCHAR(45) NOT NULL,
  type ENUM('Προσθήκη like','Προσθήκη dislike','Αφαίρεση like','Αφαίρεση dislike') NOT NULL,
  offer_id SMALLINT NOT NULL,
  datetime DATETIME NOT NULL,
  PRIMARY KEY  (id),
  CONSTRAINT `fk_hist_username` FOREIGN KEY (username) REFERENCES user (username) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_offer` FOREIGN KEY (offer_id) REFERENCES offers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE tokens (
  id SMALLINT NOT NULL,
  tokens INT NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE lows (
  p_id SMALLINT NOT NULL,
  yesterday_low DECIMAL(5,2) NOT NULL,
  last_week_low DECIMAL(5,2) NOT NULL,
  temp_last_week_low DECIMAL(5,2) NOT NULL,
  PRIMARY KEY  (p_id),
  CONSTRAINT `fk_product` FOREIGN KEY (p_id) REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE total_week_lows (
  p_id SMALLINT NOT NULL,
  week_low DECIMAL(5,2) NOT NULL,
  starting_date DATE NOT NULL,
  ending_date DATE NOT NULL,
  PRIMARY KEY  (p_id, starting_date),
  CONSTRAINT `fk_tw/_product` FOREIGN KEY (p_id) REFERENCES products (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




