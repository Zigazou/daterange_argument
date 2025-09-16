<?php

namespace Drupal\daterange_argument\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\Date;
use Drupal\views\Attribute\ViewsArgument;

/**
 * Argument handler for date ranges.
 *
 * @ingroup views_argument_handlers
 */
#[ViewsArgument(
  id: 'daterange_argument',
)]
class DateRange extends Date {

  /**
   * Overrides Drupal\views\Plugin\views\HandlerBase\getDateField().
   */
  public function getDateField() {
    // Assume date fields are ISO format (i.e. 'YYYY-MM-DD HH:MM:SS') unless
    // they are known timestamps.
    $is_iso = (substr($this->field, 0, 7) != 'changed')
           && (substr($this->field, 0, 7) != 'created')
           && ($this->field != 'login')
           && ($this->field != 'access');

    return $this->query->getDateField(
      "$this->tableAlias.$this->realField",
      $is_iso
    );
  }

  /**
   * Overrides Drupal\views\Plugin\views\argument\Date::query().
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();

    // Determine the start field name.
    $start_field = "$this->tableAlias.$this->realField";

    // Replace '_value' at the end of $start_field with '_end_value' using
    // substr to determine the corresponding end field name.
    if (substr($start_field, -6) === '_value') {
      $end_field = substr($start_field, 0, -6) . '_end_value';
    }
    else {
      $end_field = $start_field;
    }

    // Add where conditions for the date range.
    $this->query->addWhere(0, $start_field, $this->argument, '<=');

    // Support empty end field: match if end field is >= argument OR end field
    // is NULL/empty.
    $or = $this->query->query->orConditionGroup()
      ->condition($end_field, $this->argument, '>=')
      ->isNull($end_field);
    $this->query->addWhere(0, $or);
  }

}
