<?php

/**
 * @file
 * daterange_argument.views.inc
 *
 * Implementation of hook_views_data_alter().
 */

/**
 * Implements hook_views_data_alter().
 *
 * Replaces contextual date argument plugins by daterange_argument plugins.
 *
 * This function finds all field and node property argument handlers. If
 * they're listed on the Date Range Argument configuration page as "to be
 * converted", it swaps in the corresponding date range plugin.
 */
function daterange_argument_views_data_alter(&$data) {

  $config = \Drupal::config('daterange_argument.settings');

  $date_field_names = array_filter($config->get('date_field_names') ?: []);

  if (!empty($date_field_names)) {

    foreach ($data as $table_name => $table_data) {
      foreach ($table_data as $field_name => $field_data) {
        if (!isset($field_data['argument']['id'])) {
          continue;
        }

        // If listed on the configuration page, replace this contextual filter
        // plugin by its corresponding contexutal RANGE filter plugin.
        $full_name = "$table_name:$field_name";
        if (in_array($full_name, $date_field_names)) {
          $data[$table_name][$field_name]['argument']['id'] = 'daterange_argument';
        }
      }
    }
  }
}
