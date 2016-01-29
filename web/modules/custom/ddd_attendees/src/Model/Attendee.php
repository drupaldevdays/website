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
   * @var array
   */
  private $answers;

  /**
   * @var bool
   */
  private $individualSponsor;

  /**
   * Attendee constructor.
   *
   * @param string $name
   * @param string $nick
   * @param array $answers
   * @param bool $individualSponsor
   */
  public function __construct($name, $nick, array $answers, $individualSponsor = FALSE) {
    $this->name = $name;
    $this->nick = $nick;
    $this->answers = $answers;
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
   * @return mixed
   */
  public function getAvatar() {
    return 'https://www.drupal.org/files/styles/grid-2/public/user-pictures/picture-138068-1401372159.jpg?itok=aAAYdPAj';
  }

  /**
   * @return array
   */
  public function getAnswers() {
    return $this->answers;
  }

  /**
   * @return array
   */
  public function getAnswer($question) {
    return isset($this->answers[$question]) ? $this->answers[$question] : '';
  }

  /**
   * @return boolean
   */
  public function isIndividualSponsor() {
    return $this->individualSponsor;
  }
}
