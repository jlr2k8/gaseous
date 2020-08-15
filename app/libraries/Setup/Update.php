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


    /**
     * Update constructor.
     */
    public function __construct()
    {
        $this->changesets = new Changesets();
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function update()
    {
        $latest             = $this->getLatestBuildInfo();
        $version_compare    = version_compare($latest['app_version'], APP_VERSION);
        $ran_changesets     = false;

        switch ($version_compare) {
            case (int)1:
                $download   = $this->downloadLatest($latest);
                $install    = $this->installLatest($latest);

                if ($download && $install) {
                    $changeset_starting_point   = $this->changesets->getLastProcessedChangeset();
                    $need_to_process            = $this->changesets->collectChangesets($changeset_starting_point);
                    $ran_changesets             = $this->changesets->runChangesets($need_to_process);

                    Log::app($changeset_starting_point, $need_to_process, $ran_changesets);
                }

                break;
            case (int)0:
                break;
            case (int)-1:
                Log::app (
                    'The current version installed ('
                    . APP_VERSION
                    . ') succeeds the latest version available ('
                    . $latest['app_version']
                    . '). It could be witchcraft... but in any case, pulling this latest version would be a downgrade - so... pass!'
                );
                break;
        }

        return $ran_changesets;
    }


    /**
     * @param array $latest
     * @return string
     */
    private function getLatestFilename(array $latest)
    {
        $version = $latest['app_version'];

        if ($latest['build_release'] != 'stable') {
            $version .= '-' . $latest['build_release'] . '.' . $latest['build_number'];
        }

        $filename = 'gaseous-' . $version . '.tar.bz2';

        return $filename;
    }


    /**
     * @param array $latest
     * @return bool
     */
    private function installLatest(array $latest)
    {
        $filename   = $this->getLatestFilename($latest);
        $phar       = new PharData(WEB_ROOT . '/../' . $filename);
        $extracted  = $phar->extractTo(WEB_ROOT . '/../', null, true);
        $renamed    = false;

        if ($extracted) {
            $extracted_directory    = realpath(WEB_ROOT . '/../' . str_replace('.tar.bz2', null, $filename));
            $recursive_iterator     = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extracted_directory));

            foreach ($recursive_iterator as $file) {
                $realpath = $file->getRealPath();

                if (!is_dir($realpath)) {
                    rename (
                        $realpath,
                        realpath(WEB_ROOT . '/../') . str_replace($extracted_directory, null, $realpath)
                    );
                }
            }
        }

        return $renamed;
    }


    /**
     * @param array $latest
     * @return false|int
     */
    private function downloadLatest(array $latest)
    {
        $filename = $this->getLatestFilename($latest);

        $fpc = file_put_contents(
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

        if (!empty($additional_curl_opts) && is_array($additional_curl_opts)) {
            $curl_opt_array += $additional_curl_opts;
        }

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