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

    // Login user.
    $I->amOnPage('/user/login');
    $I->submitForm('#user-login-form', ['name' => 'user1', 'pass' => '123456']);
    $I->seeElement('body.user-logged-in');

    // Fill session submission.
    $I->amOnPage('/node/add/session');
    $node_title = $this->faker->text(30);
    $I->fillField('title[0][value]', $node_title);
    $I->fillCkEditorByName('body[0][value]', $this->faker->text(100));
    $I->fillField('field_author[0][target_id]', 'user1 (2)');
    $I->selectOption('field_exp_level', 0);
    $I->selectOption('field_session_track', 'development');
    $I->makeScreenshot('session_form');
    $I->submitForm('#node-session-form', [], 'op');
    $I->makeScreenshot('session_view');
    $I->see($node_title, '.field--name-title');
  }
}
