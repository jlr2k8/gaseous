<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2017 All Rights Reserved.
 * 10/14/2017
 *
 * validation.php
 *
 * Validate registration (usernames, passwords, email, profanity, etc)
 *
 */

namespace User;

class Validation
{
    public function __construct()
    {
    }


    /**
     * @param $username
     * @return bool
     */
    public static function checkIfUsernameExists($username)
    {
        $sql    = "SELECT COUNT(username) AS count_username FROM account WHERE username= ?";

        $db     = new \Db\Query($sql, [$username]);
        $result = $db->fetchAssoc();

        return ($result['count_username'] > 0);
    }


    /**
     * @param $email
     * @param bool $ignore_guest
     * @return bool
     */
    public static function checkIfEmailExists($email, $ignore_guest = false)
    {
        $sql        = "SELECT COUNT(email) AS count_email FROM account WHERE email = ? ";
        $bind       = [$email];
        $db         = new \Db\Query($sql, $bind);
        $result     = $db->fetchAssoc();

        return ($result['count_email'] > 0);
    }


    /**
     * @param $password
     * @return bool
     */
    public static function checkValidPassword($password)
    {
        $password = !empty($password) ? $password : false;

        /**
         *    Password must
         *        * contain at least 7 characters
         *        * at least one uppercase letter
         *        * at least one lowercase letter
         *        * at least one number
         **/
        if ($password) {

            // TODO - make configurable (along with password requirements message)
            $password_pattern = '/^.*(?=.{7,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/';

            if (preg_match($password_pattern, $password)) {

                return true;
            }
        }

        return false;
    }


    /**
     * @param $name
     * @param bool $allow_numbers
     * @return bool
     */
    public static function checkValidName($name, $allow_numbers = false)
    {
        $name = !empty($name) ? $name : false;

        /** NOTICE
         *    All names must contain 3 - 10 characters (letters only)
         *      (unless $allow_numbers is true!)
         **/

        if ($name) {

            // TODO - make configurable (along with password requirements message)
            $name_pattern   = '[A-Za-z';
            $name_pattern   .= $allow_numbers ? $name_pattern . '0-9' : null;
            $name_pattern   = '/^' . $name_pattern . ']{3,10}$/';

            if (
                preg_match($name_pattern, $name)
                && self::checkProfanity($name) === false
                && self::checkDeceitfulName($name) === false
            ) {

                return true;
            }
        }

        return false;
    }


    /**
     * @param $username
     * @return bool
     */
    public static function checkValidUsername($username)
    {
        $username = !empty($username) ? $username : false;

        // same reqs as person's name, but allow numbers
        return self::checkValidName($username, true);
    }


    /**
     * TODO - put in table! so much codified profanity!
     *
     * @param $word
     * @return bool
     */
    public static function checkProfanity($word)
    {
        $word = !empty($word) ? strtolower($word) : false;

        /* Profanity - Per BannedWordList.com */
        $profane_name_arr = [
            'anal',
            'anus',
            'arse',
            'ass',
            'ballsack',
            'balls',
            'bastard',
            'bitch',
            'biatch',
            'bloody',
            'blowjob',
            'bollock',
            'bollok',
            'boner',
            'boob',
            'bugger',
            'bum',
            'butt',
            'buttplug',
            'clit',
            'clitoris',
            'cock',
            'coon',
            'crap',
            'cunt',
            'damn',
            'dick',
            'dildo',
            'dyke',
            'fag',
            'feck',
            'fellate',
            'fellatio',
            'felching',
            'fuck',
            'fudgepacker',
            'fudge packer',
            'flange',
            'Goddamn',
            'God damn',
            'hell',
            'homo',
            'jerk',
            'jizz',
            'knobend',
            'knob end',
            'labia',
            'lmao',
            'lmfao',
            'muff',
            'nigger',
            'nigga',
            'omg',
            'penis',
            'piss',
            'poop',
            'prick',
            'pube',
            'pussy',
            'queer',
            'scrotum',
            'sex',
            'shit',
            'sh1t',
            'slut',
            'smegma',
            'spunk',
            'tit',
            'tosser',
            'turd',
            'twat',
            'vagina',
            'wank',
            'whore',
            'wtf',
        ];

        if (in_array($word, $profane_name_arr)) {

            return true;
        }

        return false;
    }


    /**
     * TODO - put in table
     *
     * @param $word
     * @return bool
     */
    public static function checkDeceitfulName($word)
    {
        $word = !empty($word) ? strtolower($word) : false;

        /* Deceitful usernames */
        if ($word) {

            $bad_name_arr = [
                'admin',
                'administrator',
                'adm',
                'bot',
                'root',
                'moderator',
                'owner',
                'mod',
                'super',
                'superuser',
                'manager',
                'edit'
            ];

            if (in_array($word, $bad_name_arr)) {

                return true;
            }
        }

        return false;
    }
}