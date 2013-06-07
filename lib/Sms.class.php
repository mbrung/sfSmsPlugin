<?php 
class Sms
{
	public $recipient;
	public $body;
	public $firstName;
	public $lastName;

	/**
	 * Used to hold the message identifier returned by the gateway
	 */
	public $messageId = null;

	private $isInternationalFormat = false;
	
	public function __construct($recipient, $body)
	{
		$this->recipient = $recipient;
		$this->body = $body;
	}
	
	public function setIsInternationalFormat($isInternationalFormat)
	{
		$this->isInternationalFormat = $isInternationalFormat;
	}

	public function isInternationalFormat()
	{
		return $this->isInternationalFormat;
	}

	private function doNotify()
	{
		sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, "sms.delivery"));
	}
	
	public function getRecipientAsInternationalNumber()
	{
		if ($this->isInternationalFormat)
		{
			return $this->recipient;
		}

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