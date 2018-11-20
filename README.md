```bash
For tasks instructions check ./docs/task/README.md
```
# Appointment API

Appointment API is going to be used as backend service for scheduling appointments in our calendar.

## Development

### Installing dependencies
```bash
composer install
```

#### Creating database
```bash
php ./bin/console doctrine:database:create --env=dev   # Create database
php ./bin/console doctrine:schema:create --env=dev     # Create schema
php ./bin/console doctrine:fixtures:load --env=dev     # Load fixtures
```

### Running locally
```bash
php ./bin/console server:start
```

### Configuration

- Environment variables: `.env`
- General configuration: `./config`
    - [Doctrine](https://www.doctrine-project.org/): `./config/packages/doctrine.yaml`

### Database Migrations

```bash
# generate blank migration for manual adjustment
php ./bin/console doctrine:migrations:generate

# generate migration based on difference between database structure and entities
php ./bin/console doctrine:migrations:diff

# migrate to latest versions
php ./bin/console doctrine:migrations:migrate
```

### Tests

#### Preparing test database
```bash
php ./bin/console doctrine:database:create --env=test   # Create database
php ./bin/console doctrine:schema:create --env=test     # Create schema
php ./bin/console doctrine:fixtures:load --env=test     # Load fixtures
```
#### Running tests
```bash
php ./bin/phpunit
```

### Code Style & Linters

We stick to [PSR-2](https://www.php-fig.org/psr/psr-2/) and use [EditorConfig](https://editorconfig.org/) for improved consistency and readability.

```bash
./vendor/bin/phpcs --standard=./phpcs.xml.dist
./vendor/bin/phpmd --exclude src/Kernel.php src,tests text phpmd.xml.dist
```