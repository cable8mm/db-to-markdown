# DB to markdown

[![code-style](https://github.com/cable8mm/db-to-markdown/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/db-to-markdown/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/db-to-markdown/actions/workflows/run-tests.yml)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/db-to-markdown)](https://packagist.org/packages/cable8mm/db-to-markdown)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/db-to-markdown)](https://packagist.org/packages/cable8mm/db-to-markdown/stats)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/db-to-markdown/php)](https://packagist.org/packages/cable8mm/db-to-markdown)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/db-to-markdown/symfony%2Fconsole)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/db-to-markdown)](https://github.com/cable8mm/db-to-markdown/stargazers)
![Packagist License](https://img.shields.io/packagist/l/cable8mm/db-to-markdown)

This tool generates Markdown files from a database table. It supports multiple output formats including Jekyll, Astro, Gatsby, or any custom format you create.

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

After installation, run the `init` command to create the required configuration files:

```sh
php bin/console init
# Creates .env and db-to-markdown.json from their example templates
```

## Configuration

### 1. Database Connection (`.env`)

Copy `.env.example` to `.env` and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=password
DB_PORT=3306
```

If `.env` is not provided, SQLite is used by default with `database/database.sqlite`.

### 2. Table Mapping (`db-to-markdown.json`)

Configure your table structure and field mapping in `db-to-markdown.json`:

```json
{
  "table": "posts",
  "author": "Your Name",
  "category_labels": {
    "1": "Category One",
    "2": "Category Two"
  },
  "map": {
    "title": ["title", "subtitle"],
    "slug": "id",
    "categories": "category_id",
    "body": "content",
    "published_at": "published_at"
  }
}
```

**Field Descriptions:**

- `table`: Your database table name
- `author`: Default author name (used if author field is empty)
- `category_labels`: Maps category IDs to display names (optional)
- `map.title`: Column(s) for the article title (string or array)
- `map.slug`: Column for the URL slug
- `map.categories`: Column for category mapping
- `map.body`: Column containing HTML content (converted to Markdown)
- `map.published_at`: Published date column

## Usage

### Quick Start (SQLite Demo)

```sh
# Initialize configuration files
php bin/console init

# Seed demo data into the built-in SQLite database
php bin/console seeding

# Generate Markdown files
php bin/console create-md
# or for Jekyll format:
php bin/console create-jekyll
```

### Available Commands

```sh
# Generate standard Markdown files in dist/
php bin/console create-md

# Generate Jekyll-compatible Markdown with YAML front matter
php bin/console create-jekyll

# Clear all generated files in dist/
php bin/console clean

# Initialize configuration files from examples
php bin/console init

# Seed demo data for testing
php bin/console seeding
```

### Command Options

```sh
php bin/console create-md --dry-run
# Shows how many files would be generated without writing them

php bin/console create-md --limit=5
# Process only 5 records
```

## How to Develop Custom Commands

1. **Run init** to create configuration files, or create them manually:
   - `.env` for database credentials
   - `db-to-markdown.json` for table/field mapping

2. **Create a mapper** in `src/Mappers/` to define your table field mapping (see existing examples).

3. **Create a format** in `src/Formats/` to define how output files are rendered (Markdown, Jekyll, etc.).

4. **Create a command** in `src/Command/` extending `BaseCommand` or implementing your own logic.

5. **Register your command** in `bin/console`.

## Formatting

```bash
composer lint
# Modify all files to comply with the PSR-12.

composer inspect
# Inspect all files to ensure compliance with PSR-12.
```

## Test

It uses the built-in SQLite database, not your own database. It will never cause harm to your data.

```sh
composer test
```

## License

The DB to markdown project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
