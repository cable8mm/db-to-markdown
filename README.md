# About

[![Tests](https://github.com/cable8mm/db-to-markdown/actions/workflows/tests.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/tests.yml)
[![Coding style PHP](https://github.com/cable8mm/db-to-markdown/actions/workflows/coding-style-php.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/coding-style-php.yml)
[![Latest Stable Version](http://poser.pugx.org/cable8mm/db-to-markdown/v)](https://packagist.org/packages/cable8mm/db-to-markdown)
[![Total Downloads](http://poser.pugx.org/cable8mm/db-to-markdown/downloads)](https://packagist.org/packages/cable8mm/db-to-markdown)
[![release date](https://img.shields.io/github/release-date/cable8mm/db-to-markdown)](https://github.com/cable8mm/db-to-markdown/releases)
[![minimum PHP version](https://img.shields.io/badge/php-%3E%3D_8.0.0-8892BF.svg?logo=php)](https://github.com/cable8mm/db-to-markdown)
![GitHub License](https://img.shields.io/github/license/cable8mm/db-to-markdown)
![Static Badge](https://img.shields.io/badge/Symfony-Console-black?logo=symfony)

This is a file generator specifically designed for generating markdown files.

## Install

```sh
composer create-project cable8mm/db-to-markdown
```

## Usage

```sh
bin/console create-md
# Convert the database to markdown files in the dist folder.

bin/console create-jekyll
# Convert the database to markdown files suitable for Jekyll in the dist folder.

bin/console clean
# Clear the contents of the dist folder.
```

## How to create your own commands

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

The Import from DB to Jekyll project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
