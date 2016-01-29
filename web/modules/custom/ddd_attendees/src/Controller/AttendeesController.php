<?php

/**
 * @file
 * Contains \Drupal\ddd_attendees\Controller\AttendeesController.
 */

namespace Drupal\ddd_attendees\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ddd_attendees\EventbriteServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AttendeesController.
 */
class AttendeesController extends ControllerBase {

  /**
   * @var \Drupal\ddd_attendees\EventbriteServiceInterface
   */
  private $eventbriteService;

  /**
   * @param \Drupal\ddd_attendees\EventbriteServiceInterface $eventbriteService
   */
  public function __construct(EventbriteServiceInterface $eventbriteService) {
    $this->eventbriteService = $eventbriteService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('ddd_attendees.eventbrite')
    );
  }

  /**
   * @return array
   */
  public function view() {
    $attendees = $this->eventbriteService->getAttendees();

    return [
      '#theme' => 'ddd_attendees_view',
      '#attendees' => $attendees,
      '#cache' => [
        'max-age ' => 60 * 60 * 2, // two hours
      ],
    ];
  }
}
