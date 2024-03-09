# DB to markdown

[![code-style](https://github.com/cable8mm/db-to-markdown/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/db-to-markdown/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/run-tests.yml)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/db-to-markdown)](https://packagist.org/packages/cable8mm/db-to-markdown)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/db-to-markdown)](https://packagist.org/packages/cable8mm/db-to-markdown/stats)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/db-to-markdown/php)](https://packagist.org/packages/cable8mm/db-to-markdown)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/db-to-markdown/symfony%2Fconsole)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/db-to-markdown)](https://github.com/cable8mm/db-to-markdown/stargazers)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/db-to-markdown)

This tool is specialized in generating Markdown files, particularly designed for creating Jekyll Markdown documents from a database. You can generate your own Markdown, such as Jekyll, Astro, Gatsby, or any other format you prefer.

We have provided the API Documentation on the web. For more information, please visit https://www.palgle.com/db-to-markdown/ ❤️

## Features

- [x] Any schema can generate Markdown in any desired format
- [x] Effortlessly incorporate your custom mapper and command
- [x] Implement callback body and datetime functionality
- [x] Database testing is supported

## Preview

![Preview](https://github.com/cable8mm/cabinet/blob/main/db-to-markdown-preview.gif?raw=true)

## Support & Tested

| Versions  | PHP 8.0.2 | PHP 8.1.\* | PHP 8.2.\* | PHP 8.3.\* |
| :-------: | :-------: | :--------: | :--------: | :--------: |
| Available |    ✅     |     ✅     |     ✅     |     ✅     |

## Installation

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

### How to Develop Custom Commands

1. Please fill in the database connection information in the `.env` file. You can verify it by using the composer test command.
2. You can create a mapping class in the `src/Mappers/` folder for input and a format class in the `src/Formats` for output.
3. Finally, you can create a command of your choice in the `src/Command` folder.

I have already prepared the mapper, format class, and command.

### Formatting

```bash
composer lint
# Modify all files to comply with the PSR-12.

composer inspect
# Inspect all files to ensure compliance with PSR-12.
```

### Test

It uses the built-in SQLite database, not your own database. It will never cause harm to your data. You don't need to worry about that.

```sh
composer test
```

## License

The DB to markdown project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
