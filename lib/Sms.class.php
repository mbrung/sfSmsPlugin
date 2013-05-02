<?php 
class Sms
{
	public $recipient;
	public $body;
	
	public function __construct($recipient, $body)
	{
		$this->recipient = $recipient;
		$this->body = $body;
	}
	
	private function doNotify()
	{
		sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, "sms.delivery"));
	}
	
	public function getRecipientAsInternationalNumber()
	{
		if (strpos($this->recipient, "33")===false)
		{
			return substr_replace($this->recipient, '33', 0, 1);
		}
		return $this->recipient;
	}
	
	private function doSend()
	{
		SmsDeliveryGateway::getInstance()->setSms($this)->performDelivery();
	}
	
	public function send()
	{
		if (sfConfig::get("sms_delivery_strategy")!="none")
		{
			$this->doSend();	
		}
		$this->doNotify();
	}
	
	
}?>