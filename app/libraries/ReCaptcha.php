<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 10/8/18
 *
 * ReCaptcha.php
 *
 * ReCaptcha validation
 *
 **/

class ReCaptcha
{
    private $remote_addr;

    public function __construct()
    {
        $this->remote_addr = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? false;
    }


    /**
     * @param array $post
     * @return bool
     */
    public function validate($post = [])
    {
        if (empty($post))
            $post = $_POST;

        $response = !empty($post['g-recaptcha-response'])
            ? $post['g-recaptcha-response']
            : false;

        $context_options['ssl']['verify_peer']      = false;
        $context_options['ssl']['verify_peer_name'] = false;

        $response = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret='
            . \Settings::value('recaptcha_private_key')
            . '&response='
            . $response
            . '&remoteip='
            . $this->remote_addr,
            false,
            stream_context_create($context_options)
        );

        $response = json_decode($response, true);

        return ($response['success'] == 'true');
    }


    /**
     * @return mixed
     */
    public static function draw()
    {
        $return             = null;
        $require_recaptcha  = \Settings::value('require_recaptcha');
        $public_key         = \Settings::value('recaptcha_public_key');

        if ($require_recaptcha && $public_key) {
            $templator  = new \Content\Pages\Templator();

            $templator->assign('recaptcha_public_key', $public_key);

            $return = $templator->fetch('recaptcha.tpl');
        }

        return $return;
    }
}