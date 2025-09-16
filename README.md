# Daterange argument

Daterange Argument is a Drupal module that provides a custom Views argument
handler for filtering date ranges on a specific date. This module allows users
to pass a date argument in the URL and retrieve all date ranges that include
that particular date.

It is based on the Contextual Range Filter (8.x-2.x)
https://www.drupal.org/project/contextual_range_filter. This explains the
particular settings page to make it work.

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
3. Go to the "Date Range Argument" settings page
   (/admin/config/content/daterange-argument) to indicate this newly created
   filter should use the Date Range Argument handler.
4. Configure the argument settings as needed.

## Requirements

- Drupal 11.
- Date fields on the target entities.

## Maintainers

- zigazou@protonmail.com

## License

This project is licensed under the GPL v2 or later.
