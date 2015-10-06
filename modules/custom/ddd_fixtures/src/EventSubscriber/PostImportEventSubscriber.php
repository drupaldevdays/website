<?php

namespace Drupal\ddd_fixtures\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\migrate\Event\MigrateEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\migrate\Event\MigrateImportEvent;

/**
 * Class PostImportEventSubscriber
 */
class PostImportEventSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  private $entityManager;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerFactory;

  /**
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   * @param \Drupal\Core\Entity\EntityManagerInterface $entityManager
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   */
  public function __construct(ConfigFactoryInterface $configFactory, EntityManagerInterface $entityManager, LoggerChannelFactoryInterface $loggerFactory) {
    $this->configFactory = $configFactory;
    $this->entityManager = $entityManager;
    $this->loggerFactory = $loggerFactory;
  }

  /**
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   */
  public function onMigratePostImport(MigrateImportEvent $event) {
    $this->loggerFactory->get('ddd_fixtures')->info('onMigratePostImport');

    if ('main_menu' === $event->getMigration()->get('id')) {
      $this->loggerFactory->get('ddd_fixtures')->info('main_menu');

      /** @var \Drupal\menu_link_content\Entity\MenuLinkContent $menu */
      $menu = $this->entityManager
        ->getStorage('menu_link_content')
        ->load(14);

      $this->configFactory->getEditable('views.view.attendees')
        ->set('display.page_1.display_options.menu.parent', $menu->getPluginId())
        ->save(TRUE);

      $this->loggerFactory->get('ddd_fixtures')->info($menu->getPluginId());
    }
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents() {
    return [
      MigrateEvents::POST_IMPORT => ['onMigratePostImport', 0],
    ];
  }
}
