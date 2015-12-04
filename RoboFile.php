<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RoboFile
 */
class RoboFile extends \Robo\Tasks {

  use \Boedah\Robo\Task\Drush\loadTasks;

  CONST LOCAL = 'local';
  CONST DEV = 'dev';
  CONST PROD = 'prod';

  /**
   * @var \Twig_Environment
   */
  private $twig;

  /**
   * Class constructor.
   */
  public function __construct() {
    $loader = new Twig_Loader_Filesystem('build/templates');
    $this->twig = new Twig_Environment($loader);
  }

  /**
   * Build local environment.
   *
   * @param bool $interactive
   *
   * @throws \Robo\Exception\TaskException
   */
  public function buildLocal($interactive = FALSE) {
    $properties = $this->loadProperties(RoboFile::LOCAL);

    $this->backupDB($properties);
    $this->setupFilesystem($properties);
    $this->installDrupal($properties);
    $this->installEnvironmentModules($properties);
    $this->migrateFixtures($properties);
    $this->migrateDevFixtures($properties);
    $this->configureSettings($properties, RoboFile::LOCAL);
    $this->protectSite($properties);
    $this->cacheRebuild($properties);

    if ($interactive) {
      $this->taskOpenBrowser($properties['domain'])->run();
    }
  }

  /**
   * Build dev environment.
   *
   * @throws \Robo\Exception\TaskException
   */
  public function buildDev() {
    $properties = $this->loadProperties(RoboFile::DEV);

    $this->setupFilesystem($properties);
    $this->installDrupal($properties);
    $this->installEnvironmentModules($properties);
    $this->migrateFixtures($properties);
    $this->migrateDevFixtures($properties);
    $this->configureSettings($properties, RoboFile::DEV);
    $this->protectSite($properties);
    $this->cacheRebuild($properties);
  }

  /**
   * Build prod environment.
   *
   * @throws \Robo\Exception\TaskException
   */
  public function buildProd() {
    $properties = $this->loadProperties(RoboFile::PROD);

    $this->setupFilesystem($properties);
    $this->installDrupal($properties);
    $this->installEnvironmentModules($properties);
    $this->migrateFixtures($properties);
    $this->protectSite($properties);
    $this->cacheRebuild($properties);
  }

  /**
   *
   */
  public function runTests() {
    $this->say('Run tests');

    $this->taskExec('vendor/bin/codecept')
      ->arg('run')
      ->run();
  }


  /**
   * Recreate files directory and remove old settings.php file.
   */
  private function setupFilesystem($properties) {
    $this->say('Setup filesystem');
    $this->taskFilesystemStack()
      ->chmod('sites/default', 0777)
      ->remove('sites/default/files')
      ->mkdir('sites/default/files', 0777)
      ->remove('sites/default/settings.php')
      ->run();
  }

  /**
   * Install Drupal.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function installDrupal($properties) {
    $this->say('Install Drupal');
    $this->taskDrushStack($properties['drush'])
      ->siteName($properties['site']['name'])
      ->siteMail($properties['site']['mail'])
      ->accountMail($properties['account']['mail'])
      ->accountName($properties['account']['name'])
      ->accountPass($properties['account']['pass'])
      ->mysqlDbUrl($properties['db']['url'])
      ->siteInstall($properties['site']['profile'])
      ->run();
  }

  /**
   * Install environment specific modules.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function installEnvironmentModules($properties) {
    $this->say('Install environment modules');
    $this->taskDrushStack($properties['drush'])
      ->exec('pm-enable ' . $properties['environment_modules'])
      ->run();
  }

  /**
   * Migrate fixtures.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function migrateFixtures($properties) {
    $this->say('Fixture migrations');
    $this->taskDrushStack($properties['drush'])
      ->exec('migrate-import paragraphs')
      ->exec('migrate-import page_node')
      ->exec('migrate-import main_menu')
      ->exec('migrate-import basic_block')
      ->run();
  }

  /**
   * Migrate development fixtures.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function migrateDevFixtures($properties) {
    $this->taskDrushStack($properties['drush'])
      ->exec('migrate-import picture_file')
      ->exec('migrate-import attendee_user')
      ->exec('migrate-import event_node')
      ->exec('migrate-import session_node')
      ->exec('migrate-import bof_node')
      ->exec('migrate-import sponsor_node')
      ->exec('migrate-import news_node')
      ->run();
  }

  /**
   * Setup correct permission for settings.php.
   */
  private function protectSite($properties) {
    $this->say('Protect settings.php');
    $this->taskFilesystemStack()
      ->chmod('sites/default/settings.php', 0755)
      ->chmod('sites/default', 0775)
      ->run();
  }

  /**
   * Rebuild the Drupal cache.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function cacheRebuild($properties) {
    $this->say('Cache rebuild');
    $this->taskDrushStack($properties['drush'])->exec('cache-rebuild')->run();
  }

  /**
   * Backup database.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function backupDB($properties) {
    if ($this->isSiteinstalled($properties)) {

      $dbName = date("Y") . date("m") . date("d") . '_ddd.sql';
      $this->taskDrushStack($properties['drush'])
        ->exec("sql-dump --result-file=build/backups/{$dbName} --ordered-dump --gzip")
        ->run();
    }
  }

  /**
   * @param $properties
   */
  private function configureSettings($properties, $env) {
    $this->say('Configure settings');

    $settingsFilePath = 'sites/default/settings.php';

    $this->taskFilesystemStack()
      ->chmod($settingsFilePath, 0777)
      ->run();

    $localSettings = $this->templateRender('settings.' . $env . '.html.twig', $properties);

    $this->taskWriteToFile($settingsFilePath)
      ->line($localSettings)->append()
      ->run();

    $this->taskFilesystemStack()
      ->chmod($settingsFilePath, 0775)
      ->run();
  }

  /**
   * @param $properties
   *
   * @return bool
   */
  private function isSiteinstalled($properties) {
    $filesystem = new Filesystem();

    return $filesystem->exists('sites/default/settings.php');
  }

  /**
   * Renders a template
   *
   * @param string $template
   * @param array $variables
   *
   * @return string
   */
  private function templateRender($template, $variables) {
    return $this->twig->render($template, $variables);
  }

  /**
   * Loads properties from file.
   *
   * @param $env
   *
   * @return array
   */
  private function loadProperties($env) {
    return Yaml::parse(file_get_contents('build/build.' . $env . '.yml'));
  }
}
