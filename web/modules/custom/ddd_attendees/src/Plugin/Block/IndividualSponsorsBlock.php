<?php

/**
 * @file
 * Contains \Drupal\ddd_attendees\Plugin\Block\IndividualSponsorsBlock.
 */

namespace Drupal\ddd_attendees\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ddd_attendees\EventbriteService;

/**
 * Provides a 'IndividualSponsorsBlock' block.
 *
 * @Block(
 *  id = "individual_sponsors_block",
 *  admin_label = @Translation("Individual sponsors"),
 * )
 */
class IndividualSponsorsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\ddd_attendees\EventbriteService definition.
   *
   * @var Drupal\ddd_attendees\EventbriteService
   */
  protected $eventbriteService;
  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        EventbriteService $eventbrite
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->eventbriteService = $eventbrite;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ddd_attendees.eventbrite')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $individual_sponsors = $this->eventbriteService->getIndividualSponsors();

    return [
      '#theme' => 'ddd_individual_sponsors_view',
      '#individual_sponsors' => $individual_sponsors,
      '#cache' => [
        'max-age ' => 60 * 60 * 1, // 1 hour
      ],
    ];
  }
}
