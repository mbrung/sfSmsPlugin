<?php

/**
 * SmsPlugin configuration.
 *
 * @package     SmsPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class sfSmsPluginConfiguration extends sfPluginConfiguration
{
	const VERSION = '1.0.0-DEV';

	public function setup() // loads handler if needed
	{
		if ($this->configuration instanceof sfApplicationConfiguration)
		{
			$configCache = $this->configuration->getConfigCache();
			$configCache->registerConfigHandler('config/sms.yml', 'sfDefineEnvironmentConfigHandler',
					array('prefix' => 'sms_'));
			$configCache->checkConfig('config/sms.yml');
		}
	}

	public function initialize() // loads the actual config file
	{
		if ($this->configuration instanceof sfApplicationConfiguration)
		{
			$configCache = $this->configuration->getConfigCache();
			include($configCache->checkConfig('config/sms.yml'));
		}
	}
}
