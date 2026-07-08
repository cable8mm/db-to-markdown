# DB to Markdown - Agent Reference

## Project Structure

```
db-to-markdown/
├── bin/console                 # CLI entry point
├── src/
│   ├── Command/             # Symfony Console commands
│   ├── Formats/             # Output formatters (Markdown, Jekyll)
│   ├── Mappers/             # Field mapper classes
│   ├── Models/              # Article model
│   └── DB.php               # Database connection (Medoo)
├── database/
│   ├── Model.php            # Seeder model (demo SQLite)
│   └── Seeder.php           # Demo data seeder
└── tests/                   # PHPUnit tests
```

## Quick Start Workflow

```bash
composer create-project cable8mm/db-to-markdown
php bin/console init            # Creates .env and db-to-markdown.json
php bin/console seeding         # Seeds 10 demo rows to SQLite
php bin/console create-md       # Generates Markdown files in dist/
```

## Configuration Files

### .env (Database Connection)

- **SQLite (default)**: `DB_CONNECTION=sqlite` + `DB_DATABASE=database/database.sqlite`
- **MySQL**: Set `DB_CONNECTION=mysql` and provide credentials

### db-to-markdown.json (Table Mapping)

```json
{
  "table": "posts",
  "author": "Your Name",
  "category_labels": { "1": "Category One" },
  "map": {
    "title": ["title_col1", "title_col2"],
    "slug": "slug_column",
    "categories": "category_column",
    "body": "content_column",
    "published_at": "date_column"
  }
}
```

## Available Commands

| Command         | Description                                                |
| --------------- | ---------------------------------------------------------- |
| `init`          | Create .env and db-to-markdown.json from examples          |
| `create-md`     | Generate standard Markdown files                           |
| `create-jekyll` | Generate Jekyll-compatible Markdown with YAML front matter |
| `clean`         | Clear all files in dist/ folder                            |
| `seeding`       | Seed demo data to SQLite                                   |

### Command Options

- `--dry-run`: Show count without writing files
- `--limit=N`: Process only N records
- `--offset=M`: Start from record M (pagination)

## Testing

```bash
composer test    # Run PHPUnit (uses /tmp/db-to-markdown-test.sqlite)
composer lint    # Format code with Pint
composer inspect # Check PSR-12 compliance
```

## Key Classes

### Mapper (src/Mappers/Mapper.php)

- `getMap(string $path): array` - Load mapping from JSON
- `fields(): array` - Get all field names
- `where(): array` - Get NOT NULL conditions for select

### Article (src/Models/Article.php)

- `make($map): static` - Static factory
- `in(array $row): static` - Create from DB row
- `setBodyCallback(...$callbacks): static` - Chain HTML processing
- `resolveCategories(?array $categories): static` - Optional category mapping
- `setAddHours(int $hours): static` - Adjust published_at

### BaseCommand (src/Command/BaseCommand.php)

- Extend this for custom commands
- Implement `makeFormat(Article $article): IFormat`
- Override `configureArticle()` to apply callbacks/mappings

## Notes

- `database/database.sqlite` is gitignored - created on demand
- Tests use isolated `/tmp/db-to-markdown-test.sqlite`
- Medoo (SQLite/MySQL) - connections lazy-loaded via `DB::getInstance()`
- Categories not in `category_labels` are kept as original values (no exception)
