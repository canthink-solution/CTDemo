<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sentMail($recipientData = NULL, $subject = NULL, $dataBody = NULL, $attachment = NULL)
{
	global $config;

	$driver = $config['mail']['driver'];

	if (in_array($driver, ['smtp'])) {
		return sentUsingMailer($recipientData, $subject, $dataBody, $attachment);
	} else {
		return ['success' => false, 'message' => "Mailer Error: Could not find any driver for " . $driver];
	}
}

// Sent Using PHPMAILER / Default
function sentUsingMailer($recipientData = NULL, $subject = NULL, $dataBody = NULL, $attachment = NULL)
{
	global $config;

	// Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);

	try {

		$debug = $config['mail']['debug'];

		// Server settings
		if (filter_var($debug, FILTER_VALIDATE_BOOLEAN)) {
			$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
		}

		$mail->isSMTP();  									// Send using SMTP
		$mail->SMTPAuth   = true;                 			// Enable SMTP authentication

		$mail->Host       = $config['mail']['host']; 		// Set the SMTP server to send through
		$mail->Username   = $config['mail']['username'];    // SMTP username
		$mail->Password   = $config['mail']['password'];    // SMTP password
		$mail->SMTPSecure = $config['mail']['encryption'];  // Enable implicit TLS encryption
		$mail->Port       = $config['mail']['port'];        // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

		// Recipients
		$mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);
		$mail->addAddress($recipientData['recipient_email'], $recipientData['recipient_name']); // Add a recipient

		// Add a CC recipient
		if (array_key_exists("recipient_cc", $recipientData) && hasData($recipientData['recipient_cc'])) {
			$ccs = $recipientData['recipient_cc'];
			if (is_array($ccs)) {
				foreach ($ccs as $cc) {
					$mail->addCC($cc);
				}
			} else {
				$mail->addCC($ccs);
			}
		}

		// Add a BCC recipient
		if (array_key_exists("recipient_bcc", $recipientData) && hasData($recipientData['recipient_bcc'])) {
			$bccs = $recipientData['recipient_bcc'];
			if (is_array($bccs)) {
				foreach ($bccs as $bcc) {
					$mail->AddBCC($bcc);
				}
			} else {
				$mail->AddBCC($bccs);
			}
		}

		// Content
		$mail->isHTML(true); //Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body    = $dataBody;

		if (!empty($attachment)) {
			if (is_array($attachment)) {
				foreach ($attachment as $files) {
					if (file_exists($files))
						$mail->addAttachment($files);
				}
			} else {
				if (file_exists($attachment))
					$mail->addAttachment($attachment);
			}
		}

		if ($mail->send()) {
			$response =  ['success' => true, 'message' => 'Email sent successfully'];
		} else {
			$response =  ['success' => false, 'message' => 'Email unable to sent'];
		}
	} catch (Exception $e) {
		$response = ['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
	}

	return $response;
}

function replaceTextWithData($string = NULL, $arrayOfStringToReplace = array())
{
	$dataToReplace = arrayDataReplace($arrayOfStringToReplace);
	return str_replace(array_keys($dataToReplace), array_values($dataToReplace), $string);
}

function arrayDataReplace($data)
{
	$newKey = $newValue = $newData = [];
	foreach ($data as $key => $value) {
		array_push($newKey, '%' . $key . '%');
		array_push($newValue, $value);
	}

	foreach ($newKey as $key => $data) {
		$newData[$data] = $newValue[$key];
	}

	return $newData;
}
