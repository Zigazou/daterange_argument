<?php

namespace Drupal\daterange_argument\Plugin\views\argument;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\views\Plugin\views\argument\Date;
use Drupal\views\Plugin\views\query\Sql;

/**
 * Contextual filter that checks whether a given date falls within a Date Range.
 *
 * @ViewsArgument("date_range_contains")
 */
class DateRange extends Date {

  /**
   * Converts the argument to a normalized UTC date object.
   *
   * Like the core Date plugin, supports:
   * - timestamps
   * - 'now', 'today'
   * - formats ISO (YYYY-MM-DD, YYYY-MM-DDTHH:MM:SS)
   * Returns NULL if invalid.
   */
  protected function argumentToDateTime(?string $argument): ?DrupalDateTime {
    if ($argument === NULL || $argument === '') {
      return NULL;
    }

    // Practical keywords.
    $lower = strtolower($argument);
    if (in_array($lower, ['now', 'today'], TRUE)) {
      $dt = new DrupalDateTime('now', new \DateTimeZone('UTC'));
      if ($lower === 'today') {
        // Normalize to the beginning of the UTC day (00:00:00).
        $dt->setTime(0, 0, 0);
      }
      return $dt;
    }

    // Numerical timestamp?
    if (ctype_digit($argument)) {
      $dt = DrupalDateTime::createFromTimestamp((int) $argument, new \DateTimeZone('UTC'));
      return $dt ?: NULL;
    }

    // ISO-like / dates libraries.
    try {
      // Let PHP parse; force to UTC to match field storage.
      $dt = new DrupalDateTime($argument, new \DateTimeZone('UTC'));
      if ($dt && !$dt->hasErrors()) {
        return $dt;
      }
    }
    catch (\Exception $e) {
      // Ignore.
    }

    return NULL;
  }

  /**
   * Builds the WHERE condition for the Views SQL query plugin.
   */
  public function query($group_by = FALSE) {
    /** @var \Drupal\views\Plugin\views\query\Sql $query */
    $query = $this->query;
    if (!$query instanceof Sql) {
      // If not a SQL query, do nothing.
      return;
    }

    $dt = $this->argumentToDateTime($this->argument);

    // If the argument is invalid, filter to empty (no results) to avoid
    // surprises.
    if (!$dt) {
      $query->addWhereExpression(0, '1 = 0');
      return;
    }

    // Normalize to the storage format of date fields (UTC).
    // Drupal's storage for datetime (and daterange) uses "Y-m-d\TH:i:s".
    $value = $dt->format('Y-m-d\TH:i:s');

    // Table alias + columns.
    $table_alias = $this->ensureMyTable();

    // $this->realField corresponds to the field selected in Views.
    // For a Date Range, Views normally points to the "value" column.
    $start_col = "$table_alias.$this->realField";

    // Deduce the end_value column by replacing the _value suffix with
    // _end_value. (This is the Drupal storage convention for Date Range)
    $end_col = preg_replace('/_value$/', '_end_value', $start_col);

    // Use COALESCE(end, start) to support open ranges or end NULL.
    $placeholder = $query->placeholder(':date_arg');

    // WHERE :date_arg BETWEEN start AND COALESCE(end, start)
    $expression = "$placeholder BETWEEN $start_col AND COALESCE($end_col, $start_col)";

    $query->addWhereExpression(0, $expression, [$placeholder => $value]);
  }

  /**
   * Help text in the UI.
   */
  public function adminSummary() {
    return $this->t('Contains date (Date Range): checks if the URL date is within start/end.');
  }

}
