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
    // This is a littly iffy... Basically we assume that, unless the field is
    // a known timestamp by the name of 'changed*' or 'created*' or 'login' or
    // or 'access', the field is a Drupal DateTime, which presents itself to
    // MySQL as a string of the format '2020-12-31T23:59:59'.
    // Perhaps a better approach is to have a checkbox on the Contextual Filter
    // form for the user to indicate whether the date is a timestamp or a
    // DateTime (i.e. string).
    $first7chars = substr($this->field, 0, 7);
    $is_string_date = ($first7chars != 'changed')
                   && ($first7chars != 'created')
                   && ($this->field != 'login')
                   && ($this->field != 'access');

    return $this->query->getDateField(
      "$this->tableAlias.$this->realField",
      $is_string_date
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
    $this->query->addWhere(0, $end_field, $this->argument, '>=');
  }

}
