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
   * Get people with valid ticket.
   *
   * @return \Drupal\ddd_attendees\Model\Person[]
   */
  public function getPeople();

  /**
   * Get people that will be attended to event.
   *
   * @return \Drupal\ddd_attendees\Model\Person[]
   */
  public function getAttendees();

  /**
   * Get people that are individual sponsor.
   *
   * @return \Drupal\ddd_attendees\Model\Person[]
   */
  public function getIndividualSponsors();
}
