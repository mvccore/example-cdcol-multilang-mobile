USE master;
GO

CREATE DATABASE cdcol COLLATE Latin1_General_CS_AS;
GO

USE cdcol;
GO

CREATE TABLE dbo.cds (
	id INT IDENTITY(1,1) NOT NULL,
	title NVARCHAR(200) NOT NULL,
	interpret NVARCHAR(200) NOT NULL,
	[year] INT NULL DEFAULT 0,
	CONSTRAINT PK_cds PRIMARY KEY CLUSTERED (id ASC)
);
GO

CREATE TABLE dbo.users (
	id INT IDENTITY(1,1) NOT NULL,
	active TINYINT NOT NULL DEFAULT 1,
	[admin] TINYINT NOT NULL DEFAULT 0,
	[user_name] VARCHAR(50) NOT NULL,
	password_hash VARCHAR(60) NOT NULL,
	full_name NVARCHAR(100) NOT NULL,
	[permissions] VARCHAR(1000) NULL,
	roles VARCHAR(1000) NULL,
	CONSTRAINT PK_user PRIMARY KEY CLUSTERED (id ASC)
);
GO

SET IDENTITY_INSERT dbo.cds ON;
INSERT dbo.cds (id, title, interpret, [year]) VALUES 
	(1, N'Jump', N'Van Halen', 1984),
	(2, N'Hey Boy Hey Girl', N'The Chemical Brothers', 1999),
	(3, N'Black Light', N'Groove Armada', 2010),
	(4, N'Hotel', N'Moby', 2005),
	(5, N'Berlin Calling', N'Paul Kalkbrenner', 2008);
SET IDENTITY_INSERT dbo.cds OFF;
GO

SET IDENTITY_INSERT dbo.users ON
INSERT dbo.users (id, active, [admin], [user_name], full_name, password_hash) VALUES 
(1, 1, 1, 'admin', 'Administrator', '$2y$10$s9E56/QH6.a69sJML9aS6enCczRCZcEPrbFh7BYTSrnrn4H9QMF6u') -- password is "demo"
SET IDENTITY_INSERT dbo.users OFF
GO

CREATE NONCLUSTERED INDEX IX_cds_interpret ON dbo.cds (interpret ASC);
CREATE NONCLUSTERED INDEX IX_cds_title ON dbo.cds (title ASC);
CREATE NONCLUSTERED INDEX IX_cds_year ON dbo.cds ([year] ASC);
GO

ALTER TABLE dbo.users ADD CONSTRAINT IX_users_user_name UNIQUE NONCLUSTERED ([user_name] ASC);
CREATE NONCLUSTERED INDEX IX_users_active ON dbo.users (active ASC);
CREATE NONCLUSTERED INDEX IX_users_admin ON dbo.users ([admin] ASC);
GO