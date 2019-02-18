# Phapp environment switch

Provides config and drush commands for applying the Phapp environment mode variable.

## Prerequesites

* A [phapp-cli](https://github.com/drunomics/phapp-cli) enabled project.
* A working dotenv setup that loads PHAPP environment variables, as existing in 
  [drunomics/drupal-project](https://github.com/drunomics/drupal-project)

## Setup

In drunomics/drupal-project, all setup steps are taken care already. For others:


1. Add the following  to settings.php:

       // Set active split configuration.
       $split = getenv('PHAPP_ENV_MODE');
       $config['config_split.config_split.' . $split]['status'] = TRUE;

2. Export some config split configuration that will set further config per environment in your
   config sync directory, name the split like the environment mode, "development", or "production".
   
   You may use drunomics/dsk-config-split for creating suiting config.

3. Run `phapp:apply-env-mode` during deployments right before `drush cim`.
