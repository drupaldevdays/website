<?php

/**
 * @file
 * Contains \Drupal\ddd_attendees\EventbriteService.
 */

namespace Drupal\ddd_attendees;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ddd_attendees\Model\Attendee;
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
    $config = $this->config_factory->get('ddd_attendees.settings');
    $authorization = $config->get('authorization');
    $event = $config->get('event');

    $response = $this->http_client->get(
      "https://www.eventbriteapi.com/v3/events/{$event}/attendees", [
      'headers' => [
        'Authorization' => "Bearer {$authorization}",
      ]
    ]
    );

    $data = json_decode($response->getBody()->getContents());
    $attendees = $this->extractAttendeesFromData($data->attendees);

    return $attendees;
  }

  /**
   * @param $data
   *
   * @return \Drupal\ddd_attendees\Model\Attendee[]
   */
  private function extractAttendeesFromData($data) {
    $attendees = [];
    foreach($data as $item) {
      if(!$item->cancelled) {
        $answers = $this->extractAnswers($item);
        $attendees[] = new Attendee($item->profile->name, $item->profile->email, $answers);
      }
    }

    return $attendees;
  }

  /**
   * @param $item
   *
   * @return array
   */
  private function extractAnswers($item) {
    $answers = [];
    foreach($item->answers as $answer) {
      $answers[$answer->question] = $answer->answer;
    }

    return $answers;
  }
}
