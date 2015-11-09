<?php

use \Symfony\Component\Yaml\Yaml;
use \Symfony\Component\Filesystem\Filesystem;

/**
 * Class RoboFile
 */
class RoboFile extends \Robo\Tasks {

  use \Boedah\Robo\Task\Drush\loadTasks;

  /**
   * Build local environment.
   *
   * @param bool $interactive
   *
   * @throws \Robo\Exception\TaskException
   */
  public function buildLocal($interactive = FALSE) {
    $properties = Yaml::parse(file_get_contents('build.loc.yml'));

    $this->backupDB($properties);
    $this->setupFilesystem($properties);
    $this->installDrupal($properties);
    $this->installDevModules($properties);
    $this->migrateFixtures($properties);
    $this->migrateDevFixtures($properties);
    $this->enableLocalSettings($properties);
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
    $properties = Yaml::parse(file_get_contents('build.dev.yml'));

    $this->composerInstall($properties);
    $this->setupFilesystem($properties);
    $this->installDrupal($properties);
    $this->installDevModules($properties);
    $this->migrateFixtures($properties);
    $this->migrateDevFixtures($properties);
    $this->protectSite($properties);
    $this->cacheRebuild($properties);
  }

  /**
   * Build prod environment.
   *
   * @throws \Robo\Exception\TaskException
   */
  public function buildProd() {
    $properties = Yaml::parse(file_get_contents('build.prod.yml'));

    $this->composerInstall($properties);
    $this->setupFilesystem($properties);
    $this->installDrupal($properties);
    $this->installProdModules($properties);
    $this->migrateFixtures($properties);
    $this->protectSite($properties);
    $this->cacheRebuild($properties);
  }

  /**
   * Recreate files directory and remove old settings.php file.
   */
  private function setupFilesystem($properties) {
    $this->say('Setup filesystem');
    $this->taskFilesystemStack()
      ->chmod('sites/default', 0777)
      ->remove('sites/default/files')
      ->mkdir('sites/default/files', 777)
      ->remove('sites/default/settings.php')->run();
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
   * Install development only modules.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function installDevModules($properties) {
    $this->say('Install dev modules');
    $this->taskDrushStack($properties['drush'])
      ->exec('pm-enable devel webprofiler config views_ui field_ui dblog ddd_fixtures_dev')
      ->run();
  }

  /**
   * Install production only modules.
   *
   * @throws \Robo\Exception\TaskException
   */
  private function installProdModules($properties) {
    $this->say('Install prod modules');
    $this->taskDrushStack($properties['drush'])
      ->exec('pm-enable page_cache dynamic_page_cache')
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
      ->exec('migrate-import page_node')
      ->exec('migrate-import main_menu')
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
      $this->taskFilesystemStack()->mkdir('backups')->run();

      $dbName = date("Y") . date("m") . date("d") . '_ddd.sql';
      $this->taskDrushStack($properties['drush'])
        ->exec("sql-dump --result-file=backups/{$dbName} --ordered-dump --gzip")
        ->run();
    }
  }

  /**
   * @param $properties
   */
  private function enableLocalSettings($properties) {
    $this->say('Enable local settings');

    $this->taskExec('sed')
      ->arg('-i \'\' -e')
      ->arg('"s/# if (file_exists(__DIR__ . \'\/settings.local.php\')) {/if (file_exists(__DIR__ . \'\/settings.local.php\')) {/"')
      ->arg('sites/default/settings.php')
      ->run();
    $this->taskExec('sed')
      ->arg('-i \'\' -e')
      ->arg('"s/#   include __DIR__ . \'\/settings.local.php\';/  include __DIR__ . \'\/settings.local.php\';/"')
      ->arg('sites/default/settings.php')
      ->run();
    $this->taskExec('sed')
      ->arg('-i \'\' -e')
      ->arg('"s/# }/}/"')
      ->arg('sites/default/settings.php')
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
}
