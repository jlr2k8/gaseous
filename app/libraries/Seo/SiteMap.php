<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2018 All Rights Reserved.
 * 9/28/18
 *
 * SiteMap.php
 *
 * Site map builder for CMS pages
 *
 **/

namespace Seo;

use Db\Query;
use DOMDocument;
use Settings;

class SiteMap
{
    public $xml, $urlset;

    public function __construct()
    {
        $this->xml  = new DOMDocument('1.0', 'UTF-8');

        $this->xml->preserveWhiteSpace  = false;
        $this->xml->formatOutput        = true;

        $this->urlset = $this->xml->createElementNS(
            'http://www.sitemaps.org/schemas/sitemap/0.9',
            'urlset'
        );

        $this->urlset = $this->xml->appendChild($this->urlset);
    }


    /**
     * @return string
     * @throws \DOMException
     */
    public function getUpdatedMap()
    {
        $uris = $this->getAllActiveCmsUris();

        foreach ($uris as $uri) {
            $url = self::buildFullUrl($uri['uri']);

            $xml = $this->xml->createElement('url');
            $xml = $this->urlset->appendChild($xml);

            $loc = $this->xml->createElement('loc');
            $loc = $xml->appendChild($loc);

            $changefreq = $this->xml->createElement('changefreq');
            $changefreq = $xml->appendChild($changefreq);

            $val            = $this->xml->createTextNode($url);
            $changefreq_val = $this->xml->createTextNode('always');

            $loc->appendChild($val);
            $changefreq->appendChild($changefreq_val);
        }

        return $this->xml->saveXML($this->xml);
    }


    /**
     * @return array
     */
    private function getAllActiveCmsUris()
    {
        $sql = "
          SELECT
                uri.uid, uri.uri
          FROM
                uri
          INNER JOIN
                content AS c ON c.uri_uid = uri.uid
          INNER JOIN
                current_content_iteration AS cci ON c.uid = cci.content_uid
          INNER JOIN
                content_iteration AS ci ON cci.content_iteration_uid = ci.uid
          WHERE
                uri.archived = '0'
          AND
                c.archived = '0'
          AND
                cci.archived = '0'
          AND
                ci.archived = '0'
          AND
                ci.status = 'active' 
          AND
                ci.include_in_sitemap = '1'
          ORDER BY
                CASE WHEN uri.uri = '/home'
                    THEN 0
                    ELSE 1
            END,
            uri.uri
        ";

        $db = new Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @param $uri
     * @return string
     */
    private static function buildFullUrl($uri)
    {
        // Exception
        if ($uri == '/home') {
            $uri = '/';
        } else {
            $uri .= '/';
        }

        $url = !empty(Settings::value('official_canonical_url'))
            ? filter_var(Settings::value('official_canonical_url'), FILTER_SANITIZE_URL)
            : Settings::value('full_web_url');

        return $url . $uri;
    }


    /**
     *
     */
    public function outputHeaders()
    {
        header('Content-Type: text/xml');
    }
}