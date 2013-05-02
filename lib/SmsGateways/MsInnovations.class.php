<?php 
class SmsGateways_MsInnovations extends SmsDeliveryGateway
{
	private function getSmsClientCode() {
		return sfConfig::get('sms_clientcode','www.msinnovations.com');
	}
	
	protected function doPerformDelivery() {
		$host = sfConfig::get('sms_host');
		$pass_code = sfConfig::get('sms_passcode');
	
		if (!$fp = fsockopen($host, 80, $errno, $errstr)){
			$Message_Serveur = "Pb d'ouverture de socket sur le serveur d'envoi de SMS";
	
			$this->crit("Impossible de se connecter au serveur d'envoi de SMS.");
	
			return false;
		}
	
		if ($fp = fsockopen ($host, 80, $errno, $errstr)) {
			$args 		= "clientcode=".$this->getSmsClientCode()."&passcode=".$pass_code."&XMLFlow=".urlencode($this->to_xml());
			$size 		= strlen($args);
			$request 	= "POST /smsmod/amsmodule.sendsms.v2.php HTTP/1.1\n".
					"Host: $host\n".
					"Connection: Close\n".
					"Content-type: application/x-www-form-urlencoded\n".
					"Content-length: $size\n\n".
					$args."\n";
	
			$fput = fputs($fp, $request);
	
			$ReturnFlow = "";
	
			while(!feof($fp)){
				$ReturnFlow .= fgets($fp, 128);
			}
			fclose ($fp);
	
			$Message_erreur = $this->extract_param($ReturnFlow,"UnvalidNums");
			$statut = $this->extract_param($ReturnFlow,"StatusText");
			$this->info("Réponse du serveur de SMS : ".$statut);
	
			return (strpos($statut, "envoy")>0);
		}
	}
	
	private function extract_param($string,$value){
		// fonction d'extraction des paramètres retournés par le module http sms
		$res	= "";
		$pos 	= strpos ($string,$value);
		if ($pos !== false)
			$res = substr($string,($pos+strlen($value)+1),(strpos($string,"\n",$pos)-$pos-strlen($value)-1));
		else
			$res = "noresult";
		return $res;
	}
	
	
	private function to_xml() {
	
		function wd_remove_accents($str, $charset='utf-8')
		{
			$str = htmlentities($str, ENT_NOQUOTES, $charset);
	
			$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
			$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
			$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	
			return $str;
		}
	
		return "<DATA>".
				"<CLIENT>".strtoupper($this->getSmsClientCode())."</CLIENT>".
				"<MESSAGE>".stripslashes(wd_remove_accents($this->sms->body))."</MESSAGE>".
				"<CAMPAIGN_NAME>Information Actiroute</CAMPAIGN_NAME>".
				"<MOBILEPHONE>".$this->sms->recipient."</MOBILEPHONE>".
				"</DATA>";
	}
	
}?>