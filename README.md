# DB to markdown

[![code-style](https://github.com/cable8mm/db-to-markdown/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/db-to-markdown/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/run-tests.yml)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/db-to-markdown)](https://packagist.org/packages/cable8mm/db-to-markdown)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/db-to-markdown)](https://packagist.org/packages/cable8mm/db-to-markdown/stats)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/db-to-markdown/php)](https://packagist.org/packages/cable8mm/db-to-markdown)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/db-to-markdown/symfony%2Fconsole)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/db-to-markdown)](https://github.com/cable8mm/db-to-markdown/stargazers)
[![Packagist License](https://img.shields.io/packagist/l/cable8mm/db-to-markdown)](https://github.com/cable8mm/db-to-markdown/blob/main/LICENSE.md)

This tool is specifically designed for generating markdown files.

## Install

```sh
composer create-project cable8mm/db-to-markdown
```

## Usage

Configure `.env` to connect to your own database. If `.env` configuration is not provided, SQLite connection is established.

```sh
bin/console seeding
# If `.env` configuration is not provided, seeding must be performed.
```

Database connection is established,

```sh
bin/console create-md
# Convert the database to markdown files in the dist folder.

bin/console create-jekyll
# Convert the database to markdown files suitable for Jekyll in the dist folder.

bin/console clean
# Clear the contents of the dist folder.
```

## How to Develop Custom Commands

### Step one

Please fill in the database connection information in the `.env` file. You can verify it by using the composer test command.

### Step two

You can create a mapping class in the `src/Mappers/` folder for input and a format class in the `src/Formats` for output.

### Step three

Finally, you can create a command of your choice in the `src/Command` folder.

I have already prepared the mapper, format class, and command.

## Test

It uses the built-in SQLite database, not your own database. It will never cause harm to your data. You don't need to worry about that.

```sh
composer test
```

## License

The DB to markdown project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
