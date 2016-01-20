<?php

namespace Drupal\ddd_attendees\Model;

/**
 * Class Attendee
 */
class Attendee {

  /**
   * @var
   */
  private $name;

  /**
   * @var
   */
  private $nick;

  /**
   * @var bool
   */
  private $individualSponsor;

  /**
   * Attendee constructor.
   *
   * @param $name
   * @param $nick
   * @param bool $individualSponsor
   */
  public function __construct($name, $nick, $individualSponsor = FALSE) {
    $this->name = $name;
    $this->nick = $nick;
    $this->individualSponsor = $individualSponsor;
  }

  /**
   * @return mixed
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return mixed
   */
  public function getNick() {
    return $this->nick;
  }

  /**
   * @return boolean
   */
  public function isIndividualSponsor() {
    return $this->individualSponsor;
  }
}
