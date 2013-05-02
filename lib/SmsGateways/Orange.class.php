<?php 
class SmsGateways_Orange extends SmsDeliveryGateway
{
	protected function doPerformDelivery() {
		// votre API Access Key///  TEST API ORANGE
		$accesskey = sfConfig::get("sms_gateway_api_key");
	
		$adresse = "http://run.orangeapi.com/sms/sendSMS.xml";
	
		// shortcode d'émission : 23045 (Orange France), 38100 (Multi-opérateur France), 967482 (Orange UK) ou +447797805210 (international)
		$from = "38100";
		// numéro de téléphone du destinataire (au format international)
	
		$tb1 = array(".","/"," ","-");
		$tb2 = array("","","","");
		$telephone = str_replace($tb1, $tb2, $this->sms->getRecipientAsInternationalNumber());
	
		$to = $telephone;
	
		// activation du SMS long
		$long_text = "true";
		// limite du nombre de SMS à concaténer
		$max_sms = "3";
		// activation de l'accusé de réception
		$ack = "true";
	
		$content = urlencode($this->sms->body);
	
		$destinataire = explode(";", $to);
	
		$this->info("Envoi de '".$content."'");
	
		foreach($destinataire as $number_line){
	
			$number_line = trim($number_line);
	
			$url_sms = "http://run.orangeapi.com/sms/sendSMS.xml?id=" . $accesskey . "&from=" . $from . "&to=" . $number_line . "&content=" . $content;
	
			$output = file_get_contents($url_sms);
	
			$xml = simplexml_load_string($output);
	
			$this->info(sprintf("Statut %s pour envoi au %s avec réponse '%s'", implode($xml->xpath('//status_code')), $number_line, implode($xml->xpath('//status_msg'))));
		}
	}
	
}?>