<?php

namespace Drush\Commands;

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Extension\ModuleInstaller;

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
   * EnvModeCommands constructor.
   *
   * @param \Drupal\Core\Config\StorageInterface $configStorage
   * @param \Drupal\Core\Extension\ModuleInstaller $moduleInstaller
   */
  public function __construct(StorageInterface $configStorage, ModuleInstaller $moduleInstaller) {
    $this->configStorage = $configStorage;
    $this->moduleInstaller = $moduleInstaller;
  }

  /**
   * Applies the environment mode as set by PHAPP_ENV_MODE.
   *
   * @bootstrap full
   * @command drunomics:apply-environment-mode
   * @aliases daem
   */
  public function applyEnvironmentMode() {
    // Ensure config_split is installed - not that this is ignored if already
    // installed.
    $this->moduleInstaller->install(['config_split']);
    $env_mode = getenv('PHAPP_ENV_MODE');

    if (!isset($env_mode)) {
      $this->logger()->warning(dt('Environment mode variable not set, skipping.'));
    }
    else {
      $filename = "config_split.config_split.$env_mode";
      $config_dir = new FileStorage(config_get_config_directory(CONFIG_SYNC_DIRECTORY));
      $this->configStorage->write($filename, $config_dir->read($filename));
      $this->logger()->success(dt('Environment mode @mode applied.', ['@mode' => $env_mode]));
    }
  }

}
