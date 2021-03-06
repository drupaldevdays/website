<?php

/**
 * @file
 * Contains \Drupal\ddd_attendees\EventbriteService.
 */

namespace Drupal\ddd_attendees;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ddd_attendees\Model\Person;
use GuzzleHttp\Client;
use Drupal\Core\Logger\LoggerChannelFactory;

/**
 * Class EventbriteService.
 */
class EventbriteService implements EventbriteServiceInterface {

  /**
   * GuzzleHttp\Client definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected $http_client;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config_factory;

  /**
   * Drupal\Core\Logger\LoggerChannelFactory definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger_factory;

  /**
   * Constructor.
   */
  public function __construct(Client $http_client, ConfigFactoryInterface $config_factory, LoggerChannelFactory $logger_factory) {
    $this->http_client = $http_client;
    $this->config_factory = $config_factory;
    $this->logger_factory = $logger_factory;
  }

  /**
   * {#@inheritdoc}
   */
  public function getAttendees() {
    return array_filter($this->getPeople(), function ($person) {
      return $person->isAttended();
    });
  }

  /**
   * {#@inheritdoc}
   */
  public function getIndividualSponsors() {
    return array_filter($this->getPeople(), function ($person) {
      return $person->isIndividualSponsor();
    });
  }

  /**
   * {#@inheritdoc}
   */
  public function getPeople() {
    $config = $this->config_factory->get('ddd_attendees.settings');
    $authorization = $config->get('authorization');
    $event = $config->get('event');

    $next_page = null;
    $page_count = null;

    $total_attendees = [];

    do {
        $data = $this->getDatafromEventbriteWs($event, $authorization, $next_page);

        $next_page = $data->pagination->page_number + 1;
        $page_count = $data->pagination->page_count;

        $total_attendees = array_merge($total_attendees, $data->attendees);
    } while ($next_page <= $page_count);

    return $this->extractAttendeesFromData($total_attendees);
  }

  /**
   * Extract attendees from Eventbrite API.
   *
   * @param  string  $event         Event ID code.
   * @param  [type]  $authorization Baerer authorization code
   * @param  integer $page          Page to get
   *
   * @return object                 Data from API.
   *
   * @see https://www.eventbrite.com/developer/v3/endpoints/events/#ebapi-get-events-id-attendees
   */
  private function getDatafromEventbriteWs($event, $authorization, $page = 0) {
    $response = $this->http_client->get(
      "https://www.eventbriteapi.com/v3/events/{$event}/attendees?page=$page", [
        'headers' => [
          'Authorization' => "Bearer {$authorization}",
        ]
      ]
    );

    return json_decode($response->getBody()->getContents());
  }

  /**
   * @param array $data
   *
   * @return \Drupal\ddd_attendees\Model\Person[]
   */
  private function extractAttendeesFromData($data) {
    $people = [];

    foreach ($data as $item) {
      if (!$item->cancelled) {
        $people[] = new Person(
          $item->profile->name,
          $item->profile->email,
          $this->extractAnswers($item),
          $this->isAttendeeTicket($item->ticket_class_id),
          $this->isIndividualSponsorTicket($item->ticket_class_id)
        );
      }
    }

    return $people;
  }

  /**
   * @param object $item
   *
   * @return array
   */
  private function extractAnswers($item) {
    $answers = [];

    foreach ($item->answers as $answer) {
      $answers[$answer->question] = property_exists($answer, 'answer') ? $answer->answer : NULL;
    }

    return $answers;
  }

  /**
   * @param string $ticket_class_id
   *
   * @return bool
   */
  private function isAttendeeTicket($ticket_class_id) {
    switch($ticket_class_id) {
      case '42485291': // early bird
      case '43993570': // early bird + sponsor
      case '43993568': // standard
      case '43993571': // standard + sponsor
      case '43993569': // late
      case '43993572': // late + sponsor
        return TRUE;
        break;
      case '44478481': // sponsor only
        return FALSE;
    }

    return FALSE;
  }

  /**
   * @param string $ticket_class_id
   *
   * @return bool
   */
  private function isIndividualSponsorTicket($ticket_class_id) {
    switch ($ticket_class_id) {
      case '42485291': // early bird
      case '43993568': // standard
      case '43993569': // late
        return FALSE;
        break;
      case '44478481': // sponsor only
      case '43993570': // early bird + sponsor
      case '43993571': // standard + sponsor
      case '43993572': // late + sponsor
        return TRUE;
        break;
    }

    return FALSE;
  }
}
