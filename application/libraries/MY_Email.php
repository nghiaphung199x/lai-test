<?php
class MY_Email extends CI_Email {

	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	/**
	 * SMTP Connect
	 *
	 * @return	string
	 */
	protected function _smtp_connect()
	{
		if (is_resource($this->_smtp_connect))
		{
			return TRUE;
		}
		$context = stream_context_create();
		$result = stream_context_set_option($context, 'ssl', 'verify_peer', false);
		$ssl = ($this->smtp_crypto === 'ssl') ? 'ssl://' : '';
		
		$this->_smtp_connect = stream_socket_client($ssl.$this->smtp_host . ':'.$this->smtp_port, $errno, $errstr, $this->smtp_timeout, STREAM_CLIENT_CONNECT, $context);

		if ( ! is_resource($this->_smtp_connect))
		{
			$this->_set_error_message('lang:email_smtp_error', $errno.' '.$errstr);
			return FALSE;
		}

		stream_set_timeout($this->_smtp_connect, $this->smtp_timeout);
		$this->_set_error_message($this->_get_smtp_data());

		if ($this->smtp_crypto === 'tls')
		{
			$this->_send_command('hello');
			$this->_send_command('starttls');

			$crypto = stream_socket_enable_crypto($this->_smtp_connect, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT);

			if ($crypto !== TRUE)
			{
				$this->_set_error_message('lang:email_smtp_error', $this->_get_smtp_data());
				return FALSE;
			}
		}

		return $this->_send_command('hello');
	}
}