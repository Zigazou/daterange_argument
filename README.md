# Daterange argument

Daterange Argument is a Drupal module that provides a custom Views argument
handler for filtering date ranges on a specific date. This module allows users
to pass a date argument in the URL, enabling flexible filtering of entities
based on date range fields.

## Features

- Adds a daterange argument handler for Views.
- Supports filtering by start and end dates.
- Works with core and contributed date fields.

## Installation

1. Download and place the module in your `modules` directory.
2. Enable the module via the Drupal admin interface or with Drush:
    ```bash
    drush en daterange_argument
    ```

## Usage

1. Create or edit a View.
2. Add a contextual filter for your date field.
3. Select "Daterange argument" as the handler.
4. Configure the argument settings as needed.

## Example

To filter date ranges that include a specific date, use a URL like:
```
/your-view-path/2024-01-01
```

## Requirements

- Drupal 11.
- Date fields on the target entities.

## Maintainers

- zigazou@protonmail.com

## License

This project is licensed under the GPL v2 or later.
