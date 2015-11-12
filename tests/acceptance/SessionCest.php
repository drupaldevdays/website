<?php

use \Faker\Factory;

class SessionCest {

  /**
   * @var \Faker\Generator
   */
  private $faker;

  /**
   * @param \AcceptanceTester $I
   */
  public function _before(AcceptanceTester $I) {
    /** @var \Faker\Generator faker */
    $this->faker = Factory::create();
  }

  /**
   * @param \AcceptanceTester $I
   */
  public function _after(AcceptanceTester $I) {
  }

  /**
   * @param \AcceptanceTester $I
   */
  public function submitASession(AcceptanceTester $I) {
    $I->am('an attendee');
    $I->wantTo('submit a session');
    $I->expect('to be able a submit a new session');

    $I->amOnPage('/user/login');
    $I->submitForm('#user-login-form', ['name' => 'user1', 'pass' => '123456']);
    $I->seeElement('body.user-logged-in');
    $I->amOnPage('/node/add/session');

    $node_title = $this->faker->text(30);
    $user = 'user1 (2)';
    $I->submitForm('#node-session-form', [
      'title[0][value]' => $node_title,
      'body[0][value]' => $this->faker->text(100),
      'field_author[0][target_id]' => $user,
      'field_exp_level' => 0,
      'field_session_track' => 'development',
      'field_room' => '_none',
    ], 'op');
    $I->makeScreenshot('session');
    $I->see($node_title, '.field--name-title');
  }
}
