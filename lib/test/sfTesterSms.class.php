<?php 
class sfTesterSms extends sfTester
{
	private $messages = array();
	
	public function __construct(sfTestFunctionalBase $browser, $tester)
	{
		parent::__construct($browser,$tester);

		$browser->addListener("sms.delivery", array($this, "observe"));
	}	
	
	/**
	 * Prepares the tester.
	 */
	public function prepare()
	{
	}
	
	/**
	 * Initializes the tester.
	 */
	public function initialize()
	{
	}
	
	public function reset()
	{
		$this->messages = array();
	}
	
	/**
	 * Ending the tester block empties the SMS list
	 */
	public function end()
	{
		$this->reset();
		return parent::end();
	}

	public function observe($event)
	{
		$this->messages[] = $event->getSubject();
	}
	
	private function checkBody($message, $value)
	{
		$body = $message->body;
		$ok = false;
		$regex = false;
		$mustMatch = true;
		if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $value, $match))
		{
			$regex = $value;
			if ($match[1] == '!')
			{
				$mustMatch = false;
				$regex = substr($value, 1);
			}
		}
		
		if (false !== $regex)
		{
			if ($mustMatch)
			{
				if (preg_match($regex, $body))
				{
					$ok = true;
					$this->tester->pass(sprintf('sms body matches "%s"', $value));
				}
			}
			else
			{
				if (preg_match($regex, $body))
				{
					$ok = true;
					$this->tester->fail(sprintf('sms body does not match "%s"', $value));
				}
			}
		}
		else if ($body == $value)
		{
			$ok = true;
			$this->tester->pass(sprintf('sms body is "%s"', $value));
		}
		
		if (!$ok)
		{
			if (!$mustMatch)
			{
				$this->tester->pass(sprintf('sms body matches "%s"', $value));
			}
			else
			{
				$this->tester->fail(sprintf('sms body matches "%s"', $value));
			}
		}
	}
	
	public function checkMessage($recipient, $body=NULL)
	{
		$found = false;
		foreach($this->messages as $sms)
		{
			if ($sms->recipient==$recipient)
			{
				$this->tester->pass(sprintf("Found SMS sent to %s with body '%s'", $recipient, $body));
				$found = true;
				if ($body!=NULL)
					$this->checkBody($sms, $body);
				break;
			}
		}
		
		if (!$found)
		{
			$this->tester->fail(sprintf("Unable to find SMS for %s with optional body '%s'", $recipient, $body));
		} 
		return $this->getObjectToReturn();
	}
	
	/**
	 * Tests if sms was send and optional how many.
	 *
	 * @param int $nb number of messages
	 *
	 * @return sfTestFunctionalBase|sfTester
	 */
	public function hasSent($nb = null)
	{
		if (null === $nb)
		{
			$this->tester->ok(count($this->messages) > 0, 'some sms were sent.');
		}
		else
		{
			$this->tester->is(count($this->messages), $nb, sprintf('%s sms(s) were sent.', $nb));
		}

		return $this->getObjectToReturn();
	}
}