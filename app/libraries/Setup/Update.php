<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 6/1/20
 *
 * Update.php
 *
 * Pull latest tarball from gaseous.org, extract/replace core files (or barring correct permissions, instruct user on how to fix permissions and/or update manually)
 *
 **/

namespace Setup;

use Db\Changesets;
use Exception;
use Log;
use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Update
{
    const REPOSITORY_BASE_URL   = 'https://www.gaseous.org';
    const REPOSITORY_DOWNLOAD   = self::REPOSITORY_BASE_URL . '/files/releases';

    public $latest;
    private $changesets;

    /**
     * Update constructor.
     */
    public function __construct()
    {
        $this->changesets   = new Changesets();
        $this->latest       = $this->getLatestBuildInfo();
    }

    public function versionCompare()
    {
        return version_compare($this->latest['app_version'], APP_VERSION);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function update()
    {
        $version_compare    = $this->versionCompare();

        switch ($version_compare) {
            case 1:
                try {
                    if ($this->downloadLatest()) {
                        $this->installLatest();
                    }
                } catch(Exception $e) {
                    Log::app($e->getTraceAsString(), $e->getMessage());
                    return false;
                }

                break;
            case 0:
                return true;
            case -1:
                Log::app (
                    'The current version installed ('
                    . APP_VERSION
                    . ') succeeds the latest version available ('
                    . $this->latest['app_version']
                    . '). Skipping update.'
                );

                return true;
        }

        $changeset_starting_point   = $this->changesets->getLastProcessedChangeset();
        $need_to_process            = $this->changesets->collectChangesets($changeset_starting_point);
        $ran_changesets             = $this->changesets->runChangesets($need_to_process);

        Log::app($changeset_starting_point, $need_to_process, $ran_changesets);

        return $ran_changesets;
    }


    /**
     * @return string
     */
    private function getLatestFilename()
    {
        $version = $this->latest['app_version'] . '-' . $this->latest['build_release'];

        if ($this->latest['build_release'] != 'stable') {
            $version .= '.' . $this->latest['build_number'];
        }

        $filename = 'gaseous-' . $version . '.tar.bz2';

        return $filename;
    }


    /**
     * @return bool
     */
    private function installLatest()
    {
        $filename   = $this->getLatestFilename();
        $phar       = new PharData(WEB_ROOT . '/../' . $filename);
        $extracted  = $phar->extractTo(WEB_ROOT . '/../', null, true);

        if ($extracted) {
            $extracted_directory    = realpath(WEB_ROOT . '/../' . str_replace('.tar.bz2', null, $filename));
            $recursive_iterator     = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extracted_directory));

            foreach ($recursive_iterator as $file) {
                $realpath       = $file->getRealPath();
                $destination    = realpath(WEB_ROOT . '/../') . str_replace($extracted_directory, null, $realpath);

                if (!is_dir($realpath) && is_readable($realpath)) {
                    rename ($realpath, $destination);
                }
            }
        }

        return true;
    }


    /**
     * @return false|int
     */
    private function downloadLatest()
    {
        $filename   = $this->getLatestFilename();
        $fpc        = file_put_contents(
            WEB_ROOT . '/../' . $filename,
            file_get_contents(self::REPOSITORY_DOWNLOAD . '/' . $filename)
        );

        return $fpc;
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function getLatestBuildInfo()
    {
        $latest_json    = self::REPOSITORY_BASE_URL . '/latest.json';
        $curl           = curl_init($latest_json);
        $curl_opt_array = [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        ];

        curl_setopt_array(
            $curl,
            $curl_opt_array
        );

        $response = curl_exec($curl);

        if ($response === false) {
            Log::app(curl_error($curl),curl_errno($curl));

            throw new Exception(curl_error($curl), curl_errno($curl));
        }

        curl_close($curl);

        return json_decode($response, true);
    }
}