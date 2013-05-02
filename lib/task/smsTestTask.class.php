<?php

class smsTestTask extends sfBaseTask
{
	protected function configure()
	{
		// // add your own arguments here
		$this->addArguments(array(
		   new sfCommandArgument('recipient', sfCommandArgument::REQUIRED, 'Mobile number to send SMS to'),
		 ));

		$this->addOptions(array(
			new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
			new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'test'),
			new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
			new sfCommandOption('gateway', null, sfCommandOption::PARAMETER_OPTIONAL, 'The gateway name', 'Orange'),
			// add your own options here
		));

		$this->namespace        = 'sms';
		$this->name             = 'test';
		$this->briefDescription = '';
		$this->detailedDescription = <<<EOF
The sms:test|INFO] task checks whether a given gateway is able to deliver SMS messages to the specified repicient.
Call it with:

  [php symfony sms:test|INFO]
EOF;
	}
	
	protected function execute($arguments = array(), $options = array())
	{
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		$connection = $databaseManager->getDatabase($options['connection'])->getConnection();

		$configuration = ProjectConfiguration::getApplicationConfiguration($options["application"], $options["env"], true);
		sfContext::createInstance($configuration);
		
		sfConfig::set("sms_delivery_method", "realtime");
		sfConfig::set("sms_gateway_class", "SmsGateways_".$options["gateway"]);
		sfConfig::set("sms_gateway_api_key", "");
		
		$sms = new Sms($arguments["recipient"],"Contenu");
		$sms->send();
		
		$this->log("Envoi de SMS : succès");
		
	}
}
