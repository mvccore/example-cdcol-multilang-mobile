@echo off
@set currentDir=%cd%
::@cd %programfiles(x86)%/PostgreSQL/9.6/bin
cd %programfiles%/PostgreSQL/9.6/bin
set "PGOPTIONS=-c client_min_messages=WARNING"
psql -f %currentDir%/pgsql-backup.sql -q postgres postgres
cd %currentDir%
@pause