<?php

namespace Drush\Commands\phapp_env_mode;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Site\Settings;
use Drush\Commands\DrushCommands;

/**
 * Defines command for dealing with environment modes.
 */
class EnvModeCommands extends DrushCommands {

  /**
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $configStorage;

  /**
   * @var \Drupal\Core\Extension\ModuleInstaller
   */
  protected $moduleInstaller;

  /**
   * Initializes depdenencies.
   *
   * @todo: Use dependency injection once https://github.com/drush-ops/drush/issues/3938
   *   is resolved.
   */
  protected function initDependencies() {
    $this->configStorage = \Drupal::service('config.storage');
    $this->moduleInstaller = \Drupal::service('module_installer');
  }

  /**
   * Applies the environment mode as set by PHAPP_ENV_MODE.
   *
   * @bootstrap full
   * @command phapp:apply-env-mode
   */
  public function applyEnvironmentMode() {
    $this->initDependencies();

    // Ensure config_split is installed - not that this is ignored if already
    // installed.
    $this->moduleInstaller->install(['config_split']);
    $env_mode = getenv('PHAPP_ENV_MODE');

    if (!isset($env_mode)) {
      $this->logger()->warning(dt('Environment mode variable not set, skipping.'));
    }
    else {
      $filename = "config_split.config_split.$env_mode";
      $path = Settings::get('config_sync_directory');
      if (!file_exists($path . '/' . $filename)) {
        $this->logger()->warning(dt('Config split configuration file not found, skipping.'));
      }
      else {
        $config_dir = new FileStorage($path);
        $this->configStorage->write($filename, $config_dir->read($filename));
        $this->logger()->success(dt('Environment mode @mode applied.', ['@mode' => $env_mode]));
      }
    }
  }

}
