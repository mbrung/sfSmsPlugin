<?php

class smsTestTask extends sfBaseTask
{
	protected function configure()
	{
		// // add your own arguments here
		$this->addArguments(array(
		   new sfCommandArgument('action', sfCommandArgument::REQUIRED, 'send or check status'),
		   new sfCommandArgument('recipient_or_message_id', sfCommandArgument::REQUIRED, 'Mobile number to send SMS to'),
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

		if ($arguments["action"]=="send")
		{
			$sms = new Sms($arguments["recipient_or_message_id"],"Contenu");
			$sms->firstName = "Me";
			$sms->lastName = "Me";
			$sms->send();	
		} else
		{
			$custId = sfConfig::get("sms_gateway_customer_id");
			$passphrase = sfConfig::get("sms_gateway_passphrase");

			$apiUrl =  sfConfig::get("sms_gateway_api_url");
			$wsdlUrl = "$apiUrl?wsdl";

			$client = new SoapClient($wsdlUrl, array(
				"local_cert"=>sprintf("%s/config/%s", sfConfig::get("sf_root_dir"), sfConfig::get("sms_gateway_local_cert")), 
				"passphrase"=>$passphrase)
			);

			var_dump($client->listResults(array("wsFilter"=>array("msgIds"=>array($arguments["recipient_or_message_id"]),"custId"=>$custId))));die();
		}

	

	}
}
