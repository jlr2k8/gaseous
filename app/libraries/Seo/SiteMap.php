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

class SiteMap
{
    public $sitemap, $xml, $urlset;

    public function __construct()
    {
        $this->xml  = new \DOMDocument('1.0', 'UTF-8');

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

            $val = $this->xml->createTextNode($url);

            $changefreq_val = $this->xml->createTextNode('always');

            $loc->appendChild($val);

            $changefreq->appendChild($changefreq_val);
        }

        return $this->xml->saveXML($this->xml);
    }


    /**
     * @return array|bool
     */
    private function getAllActiveCmsUris()
    {
        $sql = "
          SELECT uri.uid, uri.uri
          FROM uri
          INNER JOIN page AS p ON p.uri_uid = uri.uid
          INNER JOIN current_page_iteration AS cpi ON p.page_master_uid = cpi.page_master_uid
          INNER JOIN page_iteration AS pi ON cpi.page_iteration_uid = pi.uid
          WHERE uri.archived = '0'
          AND p.archived = '0'
          AND cpi.archived = '0'
          AND pi.archived = '0'
          AND pi.status = 'active' 
          AND pi.include_in_sitemap = '1'
          ORDER BY
            CASE WHEN uri.uri = 'home'
              THEN 0
              ELSE 1
            END,
            uri.uri
        ";

        $db = new \Db\Query($sql);

        return $db->fetchAllAssoc();
    }


    /**
     * @param $uri
     * @return string
     */
    private static function buildFullUrl($uri)
    {
        // Exception
        if ($uri == '/home')
            $uri = '';
        else
            $uri .= '/';

        return \Settings::value('full_web_url') . $uri;
    }


    /**
     *
     */
    public function outputHeaders()
    {
        header('Content-Type: text/xml');
    }
}