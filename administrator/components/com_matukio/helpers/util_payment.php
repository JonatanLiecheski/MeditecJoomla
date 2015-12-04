<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.04.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperPayment
 *
 * @since  2.2.0
 */
class MatukioHelperPayment
{
	private static $instance;

	public static $matukio_payment_plugins = array(
		"cash", "banktransfer", "debit", "paypal", "paypalpro", "2checkout", "bycheck", "byorder", "linkpoint",
		"ccavenue", "payu", "authorizenet", "jomsocialpoints", "authorizenet", "alphauserpoints", "epaydk",
		"payfast", "paymill"
	);

	/**
	 * Get the payment select box
	 *
	 * @param   array   $payment_array  - The array of payment methods
	 * @param   string  $selected       - The selected item (opt)
	 *
	 * @return  string
	 */

	public static function getPaymentSelect($payment_array, $selected = null)
	{
		$html = "<select name=\"payment\" id=\"payment\">";

		if (count($payment_array) > 1)
		{
			$html .= "<option name=\"choose\" value=\"\">" . JTEXT::_("COM_MATUKIO_FIELD_CHOOSE") . "</option>";
		}

		for ($i = 0; $i < count($payment_array); $i++)
		{
			$pay = $payment_array[$i];

			$select = "";

			if ($pay['name'] == $selected)
			{
				$select = ' selected="selected" ';
			}

			$html .= "<option name=\"" . $pay['name'] . "\" value=\"" . $pay['name']
				. "\" " . $select . ">" . JText::_($pay['title']) . "</option>";
		}

		$html .= "</select>";

		return $html;
	}

	/**
	 * Gets the branktransfer informations html
	 *
	 * Not used in the payment processing (since 2.2 plugin)
	 * Mostly used for the template generation
	 *
	 * @param   string  $account        -
	 * @param   string  $blz            -
	 * @param   string  $bank           -
	 * @param   string  $accountholder  -
	 * @param   string  $iban           -
	 * @param   string  $bic            -
	 *
	 * @return  string
	 */

	public static function getBanktransferInfo($account, $blz, $bank, $accountholder, $iban, $bic)
	{
		$html = '<div id="mat_banktransfer">';
		$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_INTRO');
		$html .= "<br />";
		$html .= "<br />";

		if (!empty($account))
		{
			$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_ACCOUNT');
			$html .= ": " . $account;
			$html .= "<br />";
		}

		if (!empty($blz))
		{
			$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_BANKCODE');
			$html .= ": " . $blz;
			$html .= "<br />";
		}

		if (!empty($iban))
		{
			$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_IBAN');
			$html .= ": " . $iban;
			$html .= "<br />";
		}

		if (!empty($bic))
		{
			$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_BIC');
			$html .= ": " . $bic;
			$html .= "<br />";
		}

		if (!empty($bank))
		{
			$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_BANK');
			$html .= ": " . $bank;
			$html .= "<br />";
		}

		$html .= JText::_('COM_MATUKIO_PAYMENT_BANKTRANSFER_ACCOUNTHOLDER');
		$html .= ": " . $accountholder;
		$html .= "<br />";

		$html .= "</div>\n";

		return $html;
	}

	/**
	 * Returns the html code for the paypal form
	 *
	 * @param   string  $payment_adress  -
	 * @param   string  $eventname       -
	 * @param   string  $fee             -
	 * @param   string  $currency        -
	 * @param   string  $returnurl       -
	 *
	 * @deprecated 2.2 - replaced through Joomla payment API plugins
	 *
	 * @return string
	 */
	public static function getPayPalForm($payment_adress, $eventname, $fee, $currency, $returnurl)
	{
		$html = '<div id="mat_paypal">';
		$html .= JText::_('COM_MATUKIO_PAYMENT_PAYPAL_INTRO');
		$html .= '</div>';

		return $html;
	}

	/**
	 * Generates a Universally Unique IDentifier, version 4. (truly random UUID)
	 *
	 * @param   bool  $hex  - If TRUE return the uuid in hex format, otherwise as a string
	 *
	 * @see http://tools.ietf.org/html/rfc4122#section-4.4
	 * @see http://en.wikipedia.org/wiki/UUID
	 *
	 * @return string - A UUID, made up of 36 characters or 16 hex digits.
	 */
	public static function getUuid($hex = false)
	{
		$pr_bits = false;

		if (!$pr_bits)
		{
			$fp = @fopen('/dev/urandom', 'rb');

			if ($fp !== false)
			{
				$pr_bits .= @fread($fp, 16);
				@fclose($fp);
			}
			else
			{
				// If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
				$pr_bits = "";

				for ($cnt = 0; $cnt < 16; $cnt++)
				{
					$pr_bits .= chr(mt_rand(0, 255));
				}
			}
		}

		$time_low = bin2hex(substr($pr_bits, 0, 4));
		$time_mid = bin2hex(substr($pr_bits, 4, 2));
		$time_hi_and_version = bin2hex(substr($pr_bits, 6, 2));
		$clock_seq_hi_and_reserved = bin2hex(substr($pr_bits, 8, 2));
		$node = bin2hex(substr($pr_bits, 10, 6));

		/**
		 * Set the four most significant bits (bits 12 through 15) of the
		 * time_hi_and_version field to the 4-bit version number from
		 * Section 4.1.3.
		 * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
		 */
		$time_hi_and_version = hexdec($time_hi_and_version);
		$time_hi_and_version = $time_hi_and_version >> 4;
		$time_hi_and_version = $time_hi_and_version | 0x4000;

		/**
		 * Set the two most significant bits (bits 6 and 7) of the
		 * clock_seq_hi_and_reserved to zero and one, respectively.
		 */
		$clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
		$clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

		// Either return as hex or as string
		$format = $hex ? '%08s%04s%04x%04x%012s' : '%08s-%04s-%04x-%04x-%012s';

		return sprintf($format, $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
	}

	/**
	 * Validates an E-Mail (bool)
	 *
	 * @param   string  $email  - The String containing the E-Mail
	 *
	 * @return  bool - false if not valid
	 */

	public static function validEmail($email)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");

		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex + 1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);

			if ($localLen < 1 || $localLen > 64)
			{
				// Local part length exceeded
				$isValid = false;
			}
			elseif ($domainLen < 1 || $domainLen > 255)
			{
				// Domain part length exceeded
				$isValid = false;
			}
			elseif ($local[0] == '.' || $local[$localLen - 1] == '.')
			{
				// Local part starts or ends with '.'
				$isValid = false;
			}
			elseif (preg_match('/\\.\\./', $local))
			{
				// Local part has two consecutive dots
				$isValid = false;
			}
			elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			{
				// Character not valid in domain part
				$isValid = false;
			}
			elseif (preg_match('/\\.\\./', $domain))
			{
				// Domain part has two consecutive dots
				$isValid = false;
			}
			elseif (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
				str_replace("\\\\", "", $local)
			)){
				// Character not valid in local part unless
				// Local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',
					str_replace("\\\\", "", $local)
				))
				{
					$isValid = false;
				}
			}

			// Check the domain name
			if ($isValid && !self::is_valid_domain_name($domain))
			{
				return false;
			}

			// Uncomment below to have PHP run a proper DNS check (risky on shared hosts!)
			/**
			 * if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
			 * // domain not found in DNS
			 * $isValid = false;
			 * }
			 * /**/
		}

		return $isValid;
	}

	/**
	 * Validates a domain name
	 *
	 * @param   string  $domain_name  - The host
	 *
	 * @return bool
	 */

	public static function is_valid_domain_name($domain_name)
	{
		$pieces = explode(".", $domain_name);

		foreach ($pieces as $piece)
		{
			if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $piece)
				|| preg_match('/-$/', $piece))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets the PP duration
	 *
	 * @param   int  $days  - The number of days
	 *
	 * @return object
	 */
	public static function _toPPDuration($days)
	{
		$ret = (object) array(
			'unit' => 'D',
			'value' => $days
		);

		// 0-90 => return days
		if ($days < 90)
		{
			return $ret;
		}

		// Translate to weeks, months and years
		$weeks = (int) ($days / 7);
		$months = (int) ($days / 30);
		$years = (int) ($days / 365);

		// Find which one is the closest match
		$deltaW = abs($days - $weeks * 7);
		$deltaM = abs($days - $months * 30);
		$deltaY = abs($days - $years * 365);
		$minDelta = min($deltaW, $deltaM, $deltaY);

		// Counting weeks gives a better approximation
		if ($minDelta == $deltaW)
		{
			$ret->unit = 'W';
			$ret->value = $weeks;

			// Make sure we have 1-52 weeks, otherwise go for a months or years
			if (($ret->value > 0) && ($ret->value <= 52))
			{
				return $ret;
			}
			else
			{
				$minDelta = min($deltaM, $deltaY);
			}
		}

		// Counting months gives a better approximation
		if ($minDelta == $deltaM)
		{
			$ret->unit = 'M';
			$ret->value = $months;

			// Make sure we have 1-24 month, otherwise go for years
			if (($ret->value > 0) && ($ret->value <= 24))
			{
				return $ret;
			}
			else
			{
				$minDelta = min($deltaM, $deltaY);
			}
		}

		// If we're here, we're better off translating to years
		$ret->unit = 'Y';
		$ret->value = $years;

		if ($ret->value < 0)
		{
			// Too short? Make it 1 (should never happen)
			$ret->value = 1;
		}
		elseif ($ret->value > 5)
		{
			// One major pitfall. You can't have renewal periods over 5 years.
			$ret->value = 5;
		}

		return $ret;
	}

	/**
	 * Gets the IPN callback URL
	 *
	 * @param   int  $sandbox  - sandbox (0 false)
	 * @param   int  $ssl      - ssl     (1 true)
	 *
	 * @return  string
	 */

	private static function getCallbackURL($sandbox = 0, $ssl = 1)
	{
		$scheme = $ssl ? 'ssl://' : '';

		if ($sandbox)
		{
			return $scheme . 'www.sandbox.paypal.com';
		}
		else
		{
			return $scheme . 'www.paypal.com';
		}
	}

	/**
	 * Validates the incoming data against PayPal's IPN to make sure this is not a
	 * fraudelent request.
	 *
	 * @param   string  $data  - The data
	 * @param   int     $ssl   - Use ssl? (1)
	 *
	 * @return  bool
	 */
	private function isValidIPN($data, $ssl = 1)
	{
		$url = $this->getCallbackURL();

		$req = 'cmd=_notify-validate';

		foreach ($data as $key => $value)
		{
			$value = urlencode($value);
			$req .= "&$key=$value";
		}

		$header = '';
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		$port = $ssl ? 443 : 80;

		$fp = fsockopen($url, $port, $errno, $errstr, 30);

		if (!$fp)
		{
			// HTTP ERROR
			return false;
		}
		else
		{
			fputs($fp, $header . $req);

			while (!feof($fp))
			{
				$res = fgets($fp, 1024);

				if (strcmp($res, "VERIFIED") == 0)
				{
					return true;
				}
				elseif (strcmp($res, "INVALID") == 0)
				{
					return false;
				}
			}

			fclose($fp);
		}
	}
}
