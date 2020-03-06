DROP DATABASE IF EXISTS cdcol;

CREATE DATABASE cdcol
	ENCODING = 'UTF8'
		CONNECTION LIMIT = -1;

DROP TABLE IF EXISTS cds CASCADE;
DROP SEQUENCE IF EXISTS cds_seq CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP SEQUENCE IF EXISTS users_seq CASCADE;

CREATE SEQUENCE cds_seq;

CREATE TABLE cds (
	id INTEGER NOT NULL DEFAULT NEXTVAL ('cds_seq'),
	title VARCHAR(200) NOT NULL,
	interpret VARCHAR(200) NOT NULL,
	year INTEGER DEFAULT 0,
	PRIMARY KEY (id)
);

CREATE INDEX cds_title ON cds (title);
CREATE INDEX cds_interpret ON cds (interpret);
CREATE INDEX cds_year ON cds (year);

CREATE SEQUENCE users_seq;

CREATE TABLE users (
	id INTEGER NOT NULL DEFAULT NEXTVAL ('users_seq'),
	active SMALLINT NOT NULL DEFAULT 1,
	admin SMALLINT NOT NULL DEFAULT 0,
	user_name VARCHAR(50) NOT NULL,
	password_hash VARCHAR(60) NOT NULL,
	full_name VARCHAR(100) NOT NULL,
	permissions VARCHAR(1000) NULL DEFAULT NULL,
	roles VARCHAR(1000) NULL DEFAULT NULL,
	PRIMARY KEY (id)
);

CREATE INDEX users_active ON users (active);
CREATE INDEX users_admin ON users (admin);
CREATE UNIQUE INDEX users_user_name ON users (user_name);

INSERT INTO cds (id, title, interpret, year) VALUES
(1,	'Jump',	'Van Halen',	1984),
(2,	'Hey Boy Hey Girl',	'The Chemical Brothers',	1999),
(3,	'Black Light',	'Groove Armada',	2010),
(4,	'Hotel',	'Moby',	2005),
(5, 'Berlin Calling', 'Paul Kalkbrenner', 2008);

INSERT INTO users (id, active, admin, user_name, full_name, password_hash) VALUES 
(1, 1, 1, 'admin', 'Administrator', '$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u'); -- password is "demo"