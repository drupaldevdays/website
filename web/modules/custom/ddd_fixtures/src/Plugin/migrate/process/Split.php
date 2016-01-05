<?php

/**
 * @file
 * Contains \Drupal\ddd_fixtures\Plugin\migrate\process\Split.
 */

namespace Drupal\ddd_fixtures\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin splits source values.
 *
 * @MigrateProcessPlugin(
 *   id = "split"
 * )
 */
class Split extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // @todo check if $destination_property is a multi-value field.
    $separator = $this->configuration['separator'];
    return explode($separator, $value);
  }
}
