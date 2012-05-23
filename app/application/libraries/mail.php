<?php
class Mail {

	/**
	* Define mail transport
	*
	* @param  string													  $default
	* @return Swift_MailTransport|Swift_SendmailTransport
	*/
	private static function transport($default = null)
	{
		require path('vendor') . 'Swift/lib/swift_required.php';

		$options = Config::get('application.mail');

		if(is_null($default))
		{
			$default = $options['transport'];
		}

		switch($default)
		{
			case 'sendmail':
				$transport = Swift_SendmailTransport::newInstance($options['sendmail']['path'].' -bs');

				break;
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance($options['smtp']['server'], $options['smtp']['port'])
					->setUsername($options['smtp']['username'])
					->setPassword($options['smtp']['password']);

				break;
			default:
				$transport = Swift_MailTransport::newInstance();

			break;
		}

		return $transport;
	}

	/**
	* Send the requested message
	*
	* @param   string  $message
	* @param   string  $to
	* @param   string  $subject
	* @return  int
	*/
	public static function send_email($message, $to, $subject, $from = NULL) 
	{
		if ($from == NULL) {
			$options = Config::get('application.mail');
		} else {
			$options['from']['email'] = $from['email'];
			$options['from']['name'] = $from['name'];
		}

		$transport = static::transport();

		$mailer = Swift_Mailer::newInstance($transport);

		$message = Swift_Message::newInstance($subject)
			->setFrom(array($options['from']['email'] => $options['from']['name']))
			->setTo(array($to))
			->setBody($message,'text/html');

		$result = $mailer->send($message);

		return $result;
	}
}