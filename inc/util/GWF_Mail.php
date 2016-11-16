<?php
if (!defined('GWF_DEBUG_EMAIL'))
{
	define('GWF_DEBUG_EMAIL', 31);
}
/**
 * Will send very simple html and plaintext mails.
 * Supports GPG signing and encryption.
 * Uses UTF8 encoding and features attachments.
 * @TODO: Implement cc and bcc
 * @TODO: Make use of staff cc?
 * @TODO: Test Attechments in combination with GPG
 * @author gizmore
 * @version 3.0
 * @since 2008
 * */
final class GWF_Mail
{
	public static $RESEND_PATH = 'dbimg/_GWF3_MAIL_RESEND_.txt';
	public static $RESEND_THRESHOLD = 1800;
	
	const HEADER_NEWLINE = "\n";
	const GPG_PASSPHRASE = ''; #GWF_EMAIL_GPG_SIG_PASS;
	const GPG_FINGERPRINT = ''; #GWF_EMAIL_GPG_SIG;

	private $reply = '';
	private $replyName = '';
	private $receiver = '';
	private $receiverName = '';
	private $return = '';
	private $returnName = '';
	private $sender = '';
	private $senderName = '';
	private $subject = '';
	private $body = '';
	private $attachments = array();
	private $headers = array();
	private $gpgKey = '';
	private $resendCheck = false;

	private $allowGPG = true;

// 	public function __construct() {}
	public function setReply($r) { $this->reply = $r; }
	public function setReplyName($rn) { $this->replyName = $rn; }
	public function setSender($s) { $this->sender = $s; }
	public function setSenderName($sn) { $this->senderName = $sn; }
	public function setReturn($r) { $this->return = $r; }
	public function setReturnName($rn) { $this->returnName = $rn; }
	public function setReceiver($r) { $this->receiver = $r; }
	public function setReceiverName($rn) { $this->receiverName = $rn; }
	public function setSubject($s) { $this->subject = $this->escapeHeader($s); }
	public function setBody($b) { $this->body = $b; }
	public function setGPGKey($k) { $this->gpgKey = $k; }
	public function setAllowGPG($bool) { $this->allowGPG = $bool; }
	public function setResendCheck($bool) { $this->resendCheck = $bool; }
	public function addAttachment($title, $data, $mime='application/octet-stream', $encrypted=true) { $this->attachments[$title] = array($data, $mime, $encrypted); }
	public function addAttachmentFile($title, $filename) { die('TODO: GET MIME TYPE AND LOAD FILE INTO MEMORY.'); }
	public function removeAttachment($title) { unset($this->attachments[$title]); }
	
	private function escapeHeader($h) { return str_replace("\r", '', str_replace("\n", '', $h)); }
	
	private function getUTF8Reply()
	{
		if ($this->reply === '')
		{
			return $this->getUTF8Sender();
		}
		return $this->getUTF8($this->reply, $this->replyName);
	}
	
	private function getUTF8Return()
	{
		if ($this->reply === '')
		{
			return $this->getUTF8Sender();
		}
		return $this->getUTF8($this->return, $this->returnName);
	}
	
	private function getUTF8($email, $name)
	{
		return $name === '' ? $email : '"'.$this->getUTF8Encoded($name)."\" <{$email}>";
	}
	
	private function getUTF8Sender()
	{
		return $this->getUTF8($this->sender, $this->senderName);
	}
	
	private function getUTF8Receiver()
	{
		return $this->getUTF8($this->receiver, $this->receiverName);
	}

	private function getUTF8Subject() { return $this->getUTF8Encoded($this->subject); }
	
	private function getUTF8Encoded($string) { return '=?UTF-8?B?'.base64_encode($string).'?='; }
	
	public static function sendMailS($sender, $receiver, $subject, $body, $html=false, $resendCheck=false)
	{
		$mail = new self();
		$mail->setSender($sender);
		$mail->setReceiver($receiver);
		$mail->setSubject($subject);
		$mail->setBody($body);
		$mail->setResendCheck($resendCheck);

		return false === $html
			? $mail->sendAsText()
			: $mail->sendAsHTML();
	}

	public static function sendDebugMail($subject, $body)
	{
		return self::sendMailS(GWF_BOT_EMAIL, GWF_ADMIN_EMAIL, GWF_SITENAME.$subject, GWF_Debug::getDebugText($body), false, true);
	}
	
	private static function br2nl($s, $nl=PHP_EOL)
	{
		return preg_replace('/< *br *\/? *>/i', $nl, $s);
	}
	
	public function nestedHTMLBody()
	{
		if (!class_exists('GWF_Template'))
		{
			return nl2br($this->body);
		}
		
		$tVars = array(
			'content' => nl2br($this->body),
		);
		return GWF_Template::templateMain('mail.tpl', $tVars);
	}

	public function nestedTextBody()
	{
		$body = $this->body;
		#$body = preg_replace('/<[^>]+>([^<]+)<[^>+]>/', '$1', $body);
		$body = preg_replace('/<[^>]+>/', '', $body);
		$body = self::br2nl($body);
		$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
		return $body;
	}

	/**
	 * This requires a GWF_User and chooses preferences.
	 * Simply do not call it when you use GWF_Mail as standalone.
	 * @param GWF_User $user
	 */
	public function sendToUser(GWF_User $user)
	{
		$this->setupGPG($user);

		if ($user->isOptionEnabled(GWF_User::EMAIL_TEXT))
		{
			return $this->sendAsText();
		}
		else
		{
			return $this->sendAsHTML();
		}
	}

	public function sendAsText($cc='', $bcc='')
	{
		if ($this->alreadySent())
		{
			return true;
		}
		return $this->send($cc, $bcc, $this->nestedTextBody(), false);
	}

	public function sendAsHTML($cc='', $bcc='')
	{
		if ($this->alreadySent())
		{
			return true;
		}
		return $this->send($cc, $bcc, $this->nestedHTMLBody(), true);
	}

	public function send($cc, $bcc, $message, $html=true)
	{
		if (count($this->attachments) > 0)
		{
			return $this->sendWithAttachments($cc, $bcc);
		}
		
		$headers = '';
		$to = $this->getUTF8Receiver();
		$from = $this->getUTF8Sender();
		$subject = $this->getUTF8Subject();
		$contentType = $html ? 'text/html' : 'text/plain';
		$headers .= 
			"Content-Type: $contentType; charset=utf-8".self::HEADER_NEWLINE
			."MIME-Version: 1.0".self::HEADER_NEWLINE
			."Content-Transfer-Encoding: 8bit".self::HEADER_NEWLINE
			."X-Mailer: PHP".self::HEADER_NEWLINE
		    .'From: '.$from.self::HEADER_NEWLINE
        	.'Reply-To: '.$this->getUTF8Reply().self::HEADER_NEWLINE
        	.'Return-Path: '.$this->getUTF8Return();
		$encrypted = $this->encrypt($message);
		if (GWF_DEBUG_EMAIL & 16)
		{
			GWF_Website::addDefaultOutput(sprintf('<h1>Local EMail:</h1><pre>%s<br/>%s</pre>', htmlspecialchars($this->subject), $message));
// 			GWF_Website::addDefaultOutput(sprintf('<h1>Local EMail:</h1><pre>%s<br/>%s</pre>', htmlspecialchars($this->subject), $encrypted));
			return true;
		}
		else
		{
			return @mail($to, $subject, $encrypted, $headers); //, '-r ' . $this->sender);
		}
	}
	
	public function sendWithAttachments($cc, $bcc)
	{
		$to = $this->getUTF8Receiver();
		$from = $this->getUTF8Sender();
		$subject = $this->getUTF8Subject();
		$random_hash = md5(microtime(true));
		$bound_mix = "GWF3-MIX-{$random_hash}";
		$bound_alt = "GWF3-ALT-{$random_hash}";
		$headers = 
			"Content-Type: multipart/mixed; boundary=\"{$bound_mix}\"".self::HEADER_NEWLINE
			."MIME-Version: 1.0".self::HEADER_NEWLINE
			."Content-Transfer-Encoding: 8bit".self::HEADER_NEWLINE
			."X-Mailer: PHP".self::HEADER_NEWLINE
		    .'From: '.$from.self::HEADER_NEWLINE
        	.'Reply-To: '.$this->getUTF8Reply().self::HEADER_NEWLINE
        	.'Return-Path: '.$this->getUTF8Return();
		
		$message  = "--$bound_mix\n";
		$message .= "Content-Type: multipart/alternative; boundary=\"$bound_alt\"\n";
		$message .= "\n";
		
		$message .= "--$bound_alt\n";
		$message .= "Content-Type: text/plain; charset=utf-8\n";
		$message .= "Content-Transfer-Encoding: 8bit\n";
		$message .= "\n";
		
		$message .= $this->encrypt($this->nestedTextBody());
		$message .= "\n\n";
		
		$message .= "--$bound_alt\n";
		$message .= "Content-Type: text/html; charset=utf-8\n";
		$message .= "Content-Transfer-Encoding: 8bit\n";
		$message .= "\n";
		
		$message .= $this->encrypt($this->nestedHTMLBody());
		$message .= "\n\n";
		
		$message .= "--$bound_alt--\n";
		$message .= "\n";
		
		foreach ($this->attachments as $filename => $attachdata)
		{
			list($attach, $mime, $encrypted) = $attachdata;
			$filename = preg_replace("/[^a-z0-9_\-\.]/i", '', $filename);
			$message .= "--$bound_mix\n";
			$message .= "Content-Type: $mime; name=\"$filename\"\n";
			$message .= "Content-Transfer-Encoding: base64\nContent-Disposition: attachment\n\n";
			if ($encrypted)
			{
				$message .= $this->encrypt(chunk_split(base64_encode($attach)));
			}
			else
			{
				$message .= chunk_split(base64_encode($attach));
			}
		}
		
		$message .= "--$bound_mix--\n\n";
		
// 		echo $message;
		
// 		$encrypted = $this->encrypt($message);
		
		if (GWF_DEBUG_EMAIL & 16)
		{
			GWF_Website::addDefaultOutput(sprintf('<h1>Local EMail:</h1><pre>%s<br/>%s</pre>', htmlspecialchars($this->subject), $message));
			return true;
		}
		else
		{
			return @mail($to, $subject, $message, $headers); #, '-r ' . $this->sender);
		}
	}
	
	/**
	 * Check if we have sent this email recently
	 * @return boolean - true if already sent
	 */
	private function alreadySent()
	{
		return false;
// 		return $this->resendCheck ? $this->alreadySentB() : false;
	}
	
// 	/**
// 	 * We do this via a timestamp and hash.
// 	 */
// 	private function alreadySentB()
// 	{
// 		$back = false;
		
// 		$myhash = $this->computeSentHash();
// 		$timeout = time() - self::$RESEND_THRESHOLD;
		
// 		$filename = GWF_WWW_PATH.self::$RESEND_PATH;
		
// 		if ($fh = fopen($filename, 'c+'))
// 		{
// 			if (flock($fh, LOCK_EX))
// 			{
// 				$keep = array();
// 				$changed = false;
				
// 				while (fscanf($fh, "%d:%s\n", $time, $hash))
// 				{
// 					if ($time > $timeout)
// 					{
// 						$keep[] = sprintf("%d:%s\n", $time, $hash);
// 						if ($hash === $myhash)
// 						{
// 							$back = true;
// 						}
// 					}
// 					else
// 					{
// 						$changed = true;
// 					}
// 				}
				
// 				if (!$back)
// 				{
// 					$keep[] = sprintf("%d:%s\n", time(), $myhash);
// 					$changed = true;
// 				}
				
// 				if ($changed)
// 				{
// 					file_put_contents($filename, implode('', $keep));
// 				}

// 				flock($fh, LOCK_UN);
// 			}
// 			fclose($fh);
// 		}
		
// 		return $back;
// 	}
	
	private function computeSentHash()
	{
		$b = $this->subject.$this->body;
		return crc32($b).md5($b).substr(sha1($b), 0, 32);
	}

	public function setupGPG(GWF_User $user)
	{
		if ($this->allowGPG)
		{
			if (function_exists('gnupg_init'))
			{
				if (false !== ($fingerprint = GWF_PublicKey::getFingerprintForUser($user)))
				{
					$this->setGPGKey($fingerprint);
				}
			}
		}
	}

	private function encrypt($message)
	{
		if ($this->gpgKey === '' && self::GPG_FINGERPRINT === '')
		{
			return $message;
		}

		if (false === function_exists('gnupg_init'))
		{
			return $message.PHP_EOL.'GnuPG Error: gnupg extension is missing.';
		}

		if (false === ($gpg = gnupg_init()))
		{
			return $message.PHP_EOL.'GnuPG Error: gnupg_init() failed.';
		}

		if ($this->gpgKey !== '')
		{
			if (false === gnupg_addencryptkey($gpg, $this->gpgKey))
			{
				return $message.PHP_EOL.'GnuPG Error: gnupg_addencryptkey() failed.';
			}
		}

		$signed = false;
//		if (self::GPG_FINGERPRINT !== '') {
//			$sign_key = preg_replace('/[^a-z0-9]/i', '', self::GPG_FINGERPRINT);
//
//			if (self::GPG_PASSPHRASE==='')
//			{
//				if (false === gnupg_addsignkey($gpg, $sign_key)) {
//					$message .= PHP_EOL.'GnuPG Error: gnupg_addsignkey1() failed.';
//				}
//				else {
//					$signed = true;
//				}
//			}
//			else
//			{
//				if (false === gnupg_addsignkey($gpg, $sign_key, self::GPG_PASSPHRASE)) {
//					$message .= PHP_EOL.'GnuPG Error: gnupg_addsignkey2() failed.';
//				}
//				else {
//					$signed = true;
//				}
//			}
//
//		}

		if ($signed === true)
		{
			if (false === ($back = gnupg_encryptsign($gpg, $message))) {
				return $message.PHP_EOL.'GnuPG Error: gnupg_encryptsign() failed.';
			}
		}
		else
		{
			if (false === ($back = gnupg_encrypt($gpg, $message))) {
				return #$message.PHP_EOL.
					'GnuPG Error: gnupg_encrypt() failed.'.PHP_EOL.'Message has been removed!';
			}
		}

		return $back;
	}
}
