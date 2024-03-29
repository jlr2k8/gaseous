<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/6/18
 *
 * Email.php
 *
 * SwiftMailer extender
 *
 **/

class Email extends Swift_Mailer
{
    public function __construct()
    {
        $smtp_host      = Settings::value('smtp_host');
        $smtp_port      = Settings::value('smtp_port');
        $smtp_user      = Settings::value('smtp_user');
        $smtp_password  = Settings::value('smtp_password');

        $transport  = new Swift_SmtpTransport($smtp_host, $smtp_port);

        $transport->setUsername($smtp_user)
            ->setPassword($smtp_password);

        parent::__construct($transport);
    }


    /**
     * @param $sender_email
     * @param array $recipient_emails
     * @param string $subject
     * @param string $sender_name
     * @param array $recipient_names
     * @param array $recipient_cc_names
     * @param array $recipient_cc_emails
     * @param string $body
     * @param string $content_type
     * @return false|int
     * @throws Exception
     */
    public function sendEmail(
        $sender_email,
        array $recipient_emails,
        $subject                = '(No Subject)',
        $sender_name            = 'Unnamed',
        $recipient_names        = array(),
        $recipient_cc_names     = array(),
        $recipient_cc_emails    = array(),
        $body                   = '(No Content)',
        $content_type           = 'text/html'
    )
    {
        $from   = [
            $sender_email => $sender_name
        ];

        $to     = $cc = false;

        if (!empty($recipient_names) && !empty($recipient_emails) && count($recipient_names) == count($recipient_emails)) {
            $to = array_combine($recipient_emails, $recipient_names);
        } elseif (!empty($recipient_emails) && empty($recipient_names)) {
            $to = $recipient_emails;
        }

        if (!empty($recipient_cc_names) && !empty($recipient_cc_emails) && count($recipient_cc_names) == count($recipient_cc_emails)) {
            $cc = array_combine($recipient_cc_emails, $recipient_cc_names);
        } elseif (!empty($recipient_cc_emails) && empty($recipient_cc_names)) {
            $cc = $recipient_cc_emails;
        }

        $message = new Swift_Message($subject);

        $message->setFrom($from);
        $message->setTo($to);
        
        if ($cc)
            $message->setCc($cc);
        
        $message->setBody($body, $content_type);

        $return = false;

        try {
            $return = $this->send($message);
            Log::app('Email sent...', ['from: ' => $from], ['to' => $to], ['cc' => $cc], strip_tags($body));
        } catch(Exception $e) {
            Log::app($e->getTraceAsString(), $e->getMessage());
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $return;
    }
}