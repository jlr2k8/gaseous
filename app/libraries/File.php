<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/10/20
 *
 * File.php
 *
 * File/upload management
 *
 **/

use Seo\Url;
use Utilities\Token;


class File
{
    public static $allowed_file_extensions = [
        'jpg',
        'jpeg',
        'gif',
        'png',
        'bmp',
    ];


    public static $getimagesize_imagetypes = [
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_BMP,
    ];


    public function __construct()
    {
    }


    /**
     * Requires global $_FILES to be set
     *
     * @param string $file_input_name
     * @param array $allowed_file_extensions
     * @return bool|string
     * @throws Exception
     */
    public function uploadFormFile($file_input_name = 'upload', $allowed_file_extensions = [])
    {
        $allowed_file_extensions    = !empty($allowed_file_extensions) ? $allowed_file_extensions : self::$allowed_file_extensions;
        $file_input_name            = filter_var($file_input_name, FILTER_SANITIZE_STRING);
        $file_upload                = !empty($_FILES[$file_input_name]['tmp_name']) ? $_FILES[$file_input_name]['tmp_name'] : false;
        $file_reference             = !empty($_FILES[$file_input_name]['name']) ? $_FILES[$file_input_name]['name'] : false;
        $pathinfo                   = pathinfo($file_reference);
        $uploaded                   = false;

        if ($file_upload && $file_reference && in_array($pathinfo['extension'], $allowed_file_extensions)) {
            $token                  = Token::generate();
            $upload_root            = rtrim(Settings::value('upload_root'), '/');
            $new_file_name          = Url::convert($pathinfo['filename']) . '-' . $token . '.' . $pathinfo['extension'];
            $new_file_destination   = $upload_root . '/' . $new_file_name;
            $upload_relative        = rtrim(self::isImageFile($file_upload) ? Settings::value('upload_image_relative') : Settings::value('upload_file_relative'), '/');
            $new_file_relative_url  = $upload_relative . '/' . $new_file_name;

            try {
                move_uploaded_file($file_upload, $new_file_destination);

                $uploaded   = $new_file_relative_url;
            } catch (Exception $e) {
                Log::app('File upload failed!', $new_file_destination, $new_file_relative_url, $e->getTraceAsString(), $e->getMessage());

                throw $e;
            }
        } else {
            Log::app('File upload is missing/invalid, or the file type is not allowed. Allowed file extensions are: ' . implode(' ', $allowed_file_extensions));
        }

        return $uploaded;
    }


    /**
     * @param $path
     * @return bool
     */
    private function isImageFile($path)
    {
        $getimagesize = getimagesize($path);

        return (!empty($getimagesize[2]) && in_array($getimagesize[2], self::$getimagesize_imagetypes));
    }


    /**
     * @param $path
     * @return bool
     */
    public static function validatePath($path)
    {
        $path = filter_var($path, FILTER_SANITIZE_URL);

        return !preg_match('~\/\.{1,2}\/~', $path);
    }
}