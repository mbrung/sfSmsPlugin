<?php 
abstract class SmsDeliveryGateway
{
	protected $sms;
	public function setSms($sms)
	{
		$this->sms = $sms;
		
		return $this;
	}

	protected abstract function doPerformDelivery();
		
	public function performDelivery()
	{
		if ($this->sms==NULL)
		{
			return;
		}
		
		try 
		{
			$this->doPerformDelivery();		
		} catch (Exception $e)
		{
			$this->crit($e->getMessage());	
		}
	}
	
	public static function getInstance()
	{
		$gateway_class = sfConfig::get("sms_gateway_class");
		return new $gateway_class;
	}
	
	public function crit($message)
	{
		sfContext::getInstance()->getLogger()->crit(sprintf("{%s} %s", get_class($this), $message));
	}
	public function info($message)
	{
		sfContext::getInstance()->getLogger()->info(sprintf("{%s} %s", get_class($this), $message));
	}
}
?>