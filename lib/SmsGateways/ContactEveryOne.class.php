<?php 
class SmsGateways_ContactEveryOne extends SmsDeliveryGateway
{
	private function getSendProfile($firstName,$lastName,$recipient)
	{
		return sprintf(<<<EOF
<?xml version="1.0" encoding="ISO-8859-1"?> 
<PROFILE_LIST>
	<PROFILE>
		<DEST_NAME>%s</DEST_NAME>
		<DEST_FORENAME>%s</DEST_FORENAME>
		<DEST_ID>ID_%s</DEST_ID>
		<TERMINAL_GROUP>
			<TERMINAL>
				<TERMINAL_NAME>mobile1</TERMINAL_NAME>
				<TERMINAL_ADDR>%s</TERMINAL_ADDR>
				<MEDIA_TYPE_GROUP>
					<MEDIA_TYPE>sms</MEDIA_TYPE>
				</MEDIA_TYPE_GROUP>
			</TERMINAL>
		</TERMINAL_GROUP>
	</PROFILE>
</PROFILE_LIST>		
EOF
, $firstName
, $lastName
, $recipient
, $recipient);

	}

	protected function doPerformDelivery() {
		$custId = sfConfig::get("sms_gateway_customer_id");
		$passphrase = sfConfig::get("sms_gateway_passphrase");

		$apiUrl =  sfConfig::get("sms_gateway_api_url");
		$wsdlUrl = "$apiUrl?wsdl";

		$client = new SoapClient($wsdlUrl, array(
			"local_cert"=>sprintf("%s/config/%s", sfConfig::get("sf_root_dir"), sfConfig::get("sms_gateway_local_cert")), 
			"passphrase"=>$passphrase)
		);

		$message = array(
			"fullContenu"=>true,
			"content"=>$this->sms->body,
			"subject"=>$this->sms->body,
			"resumeContent"=>$this->sms->body,
			"custId"=>$custId,
			"sendProfiles"=>$this->getSendProfile($this->sms->lastName,$this->sms->firstName,$this->sms->recipient),
			"strategy"=>"sms",
		);

		$this->info(sprintf("Processing message for '%s'\n", $this->sms->recipient);			

		$object = $client->sendMessage(array("wsMessage"=>$message));
		if (isset($object->sendMessageReturn))
		{
			$this->info(sprintf("Message to '%s' was processed.  Message id: '%d'\n", $this->sms->recipient, $object->sendMessageReturn->msgId));			
		}
	}
	
}?>