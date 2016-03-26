<?php

/**
 * @file
 * Contains \Drupal\ddd_map\Plugin\Block\MapBlock.
 */

namespace Drupal\ddd_map\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ddd_attendees\EventbriteService;

/**
 * Provides a 'MapBlock' block.
 *
 * @Block(
 *  id = "map_block",
 *  admin_label = @Translation("Map"),
 * )
 */
class MapBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'ddd_map_map',
      '#attached' => [
        'library' => [
          'ddd_map/map',
        ],
      ],
    ];
  }

}
