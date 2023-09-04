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

use Db\Query;

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
        $sql    = "
            SELECT
                COUNT(username) AS count_username
            FROM
                account
            WHERE
                username= ?
            AND
                archived = '0'
        ";

        $db     = new Query($sql, [$username]);
        $result = $db->fetchAssoc();

        return ($result['count_username'] > 0);
    }


    /**
     * @param $email
     * @return int
     */
    public static function checkIfEmailExists($email)
    {
        $username   = $_SESSION['account']['username'] ?? null;
        $sql        = "
            SELECT
                COUNT(email) AS count_email
            FROM
                account
            WHERE
                email = ?
            AND
                archived = '0'
        ";

        $bind[] = $email;

        if (!empty($username)) {
            $sql .= "
                AND
                    username != ?
            ";

            $bind[] = $username;
        }

        $db     = new Query($sql, $bind);
        $result = $db->fetchAssoc();
        $exists = (int)($result['count_email'] > 0);

        return $exists;
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

            // TODO - make configurable via settings (along with password requirements message)
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
        $name       = strtolower((string)$name);
        $is_valid   = false;

        // All names must contain 3 - 10 alphabetic characters only - unless $allow_numbers is true

        // TODO - make configurable via settings (along with password requirements message)
        $name_pattern   = '~[a-z' . ($allow_numbers ? '0-9' : null) . ']{3,10}~';

        if (
            preg_match($name_pattern, $name)
            && self::checkProfanity($name) === false
            && self::checkDeceitfulName($name) === false
        ) {
            $is_valid = true;
        }

        return $is_valid;
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
     * TODO - put in table or use API! so much codified profanity!
     *
     * @param $word
     * @return bool
     */
    public static function checkProfanity($word)
    {
        $word       = strtolower((string)$word);
        $is_profane = false;

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
            $is_profane = true;
        }

        return $is_profane;
    }


    /**
     * TODO - put in table or use API
     *
     * @param $word
     * @return bool
     */
    public static function checkDeceitfulName($word)
    {
        $word           = strtolower((string)$word);
        $is_deceitful   = false;

        /* Deceitful usernames */
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
            $is_deceitful = true;
        }

        return $is_deceitful;
    }
}