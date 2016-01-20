<?php

/**
 * @file
 * Contains \Drupal\ddd_attendees\EventbriteServiceInterface.
 */

namespace Drupal\ddd_attendees;

/**
 * Interface EventbriteServiceInterface.
 */
interface EventbriteServiceInterface {

  /**
   * @return \Drupal\ddd_attendees\Model\Attendee[]
   */
  public function getAttendees();
}
