<?php

namespace Drupal\ddd_fixtures\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\InstallStorage;
use Drupal\Core\Extension\ModuleHandlerInterface;
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
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   */
  public function __construct(ConfigFactoryInterface $configFactory, ModuleHandlerInterface $moduleHandler) {
    $this->configFactory = $configFactory;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   */
  public function onMigratePostImport(MigrateImportEvent $event) {
    if ('basic_block' === $event->getMigration()->get('id')) {
      $files = [
        'block.block.callforpaper.yml',
        'block.block.featuredfirst.yml',
        'block.block.featuredsecond.yml',
        'block.block.featuredthird.yml',
        'block.block.mentorship.yml',
        'block.block.training.yml',
      ];

      foreach ($files as $file) {
        $base_path = $this->moduleHandler->getModule('ddd_fixtures')->getPath();
        $contents = @file_get_contents($base_path . '/sources/block_configs/' . $file);
        $config_name = basename('sources/block_configs/' . $file, '.yml');
        $data = (new InstallStorage())->decode($contents);

        $this->configFactory->getEditable($config_name)->setData($data)->save();
      }
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
