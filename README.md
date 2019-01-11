# MvcCore - Example - CD Collection - With Authentication, Multilanguage Routes And Mobile Version

[![Latest Stable Version](https://img.shields.io/badge/Stable-v5.0.0-brightgreen.svg?style=plastic)](https://github.com/mvccore/example-cdcol-multilang-mobile/releases)
[![License](https://img.shields.io/badge/Licence-BSD-brightgreen.svg?style=plastic)](https://github.com/mvccore/example-cdcol-multilang-mobile/blob/master/LICENCE.md)
[![Packager Build](https://img.shields.io/badge/Packager%20Build-passing-brightgreen.svg?style=plastic)](https://github.com/mvccore/packager)
![PHP Version](https://img.shields.io/badge/PHP->=5.4-brightgreen.svg?style=plastic)

[**MvcCore**](https://github.com/mvccore/mvccore) classic CD collection CRUD example with default SQLite database, with authentication, with multilanguage routes and mobile version.

Example uses SQLite database by default, but it contains also MySQL, MSSQL and PostgreSQL database backups, included in `./.databases/` directory.

This example is not a single file project application. If you want to see how to build a single file application with this example,  
run this example first (to see how it works) and then follow steps here on [`mvccore/example-cdcol-portable`](https://github.com/mvccore/example-cdcol-portable).

## Instalation
```shell
# load example
composer create-project mvccore/example-cdcol-multilang-mobile

# go to project development dir
cd example-cdcol-multilang-mobile/development

# update dependencies for app development sources
composer update
```
