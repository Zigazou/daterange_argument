<?php

namespace Drupal\views\Plugin\views\argument;

use Drupal\views\Attribute\ViewsArgument;
use Drupal\views\Plugin\views\argument\Date;

/**
 * Argument handler for date ranges.
 *
 * @ingroup views_argument_handlers
 */
#[ViewsArgument(
  id: 'daterange',
)]
class DateRange extends Date {

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();

    // Determine the start and end field names.
    $start_field = "$this->tableAlias.$this->realField";
    $end_field = preg_replace('/_value$/', '_end_value', $start_field);

    // Add where conditions for the date range.
    $this->query->addWhere(0, $start_field, $this->argument, '>=');
    $this->query->addWhere(0, $end_field, $this->argument, '<=');
  }

}
