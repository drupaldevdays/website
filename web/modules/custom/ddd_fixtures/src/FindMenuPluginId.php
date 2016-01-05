<?php

namespace Drupal\ddd_fixtures;

use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Class FindMenuPluginId
 *
 * Maps Menu id to Menu plugin id.
 */
class FindMenuPluginId {

  /**
   * @param $value
   *   Menu id.
   *
   * @return string
   *   Menu plugin id.
   */
  public function find($value) {
    /** @var MenuLinkContent $menu */
    $menu = \Drupal::entityTypeManager()->getStorage('menu_link_content')->load($value);
    if($menu) {
      return $menu->getPluginId();
    } else {
      return $value;
    }
  }
}
