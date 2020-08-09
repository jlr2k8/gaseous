<?php
/**
 * Created by Josh L. Rogers.
 * Copyright (c) 2020 All Rights Reserved.
 * 5/30/20
 *
 * Content.php
 *
 * Default content
 *
 **/

namespace Setup\Reset;

use Db\PdoMySql;
use PDOException;

class Content
{
    static $content_body_types = [
        'basic_page' => [
            'parent_type_id'    => 'basic_page',
            'label'             => 'Basic Page',
            'description'       => 'Typical HTML content page',
            'template'          => [
                'label'             => 'Basic Page Layout',
                'template'          => '
                    {if !empty($subtitle)}
                        &lt;h2&gt;
                            {$subtitle}
                        &lt;/h2&gt;
                    {/if}
                    
                    &lt;div&gt;{$body}&lt;/div&gt;
                ',
                'uri_scheme'        => '{$page_title_h1}',
            ],
        ],
        'blog_landing_page' => [
            'parent_type_id'    => 'basic_page',
            'label'             => 'Blog Landing Page',
            'description'       => 'Top-level blog landing page for displaying blog categories and recent blogs.',
            'template'          => [
                'label'             => 'Blog Landing Page Layout',
                'template'          => '
                    &lt;div&gt;{$heading}&lt;/div&gt;

                    {assign var=blp value=\Cms::getContentByUri()}
                    {assign var=categories value=\Cms::getChildContentByContentUid($blp.content_uid)}
                    {assign var=blogs value=[]}
                    
                    {foreach from=$categories item=category}
                        {$blogs = array_merge($blogs, \Cms::getChildContentByContentUid($category.content_uid))}
                    {/foreach}
                    
                    {assign var=paged_blogs value=\Cms::getContentPaged($blogs)}
                    
                    &lt;div class="two-thirds_left"&gt;
                    {if !empty($blp.content_uid)}
                        {foreach from=$paged_blogs item=blog}
                            &lt;h3&gt;
                            &lt;a href="{$blog.url}"&gt;
                            {$blog.page_title_h1}&lt;/a&gt;
                            &lt;/h3&gt;
                            &lt;p&gt;
                            {if !empty($blog.body_fields.subtitle)}
                                {$blog.body_fields.subtitle}
                            {else}
                                {\Cms::teaser($blog.body_fields.body)}
                            {/if}
                            &lt;br /&gt;
                            &lt;span class="caption"&gt;
                            By:
                            &lt;a href="/users/{$blog.body_fields.author}"&gt;{$blog.body_fields.author}&lt;/a&gt;
                            on {date(\'F d, Y g:i:s A T\', $blog.body_fields.published_date)}
                            &lt;/span&gt;
                            &lt;/p&gt;
                        {/foreach}
                    {/if}
                                        
                    {\Cms::pager($blogs)}
                    &lt;/div&gt;
                                        
                    {if !empty($blp.content_uid)}
                        &lt;div class="one-third_right slight_padding gray_background"&gt;
                        {foreach from=$categories item=category}
                            &lt;h4&gt;
                            &lt;a href="{$category.uri}"&gt;
                            {$category.page_title_h1}&lt;/a&gt;
                            &lt;/h4&gt;
                        {/foreach}
                        &lt;/div&gt;
                    {/if}
                ',
                'uri_scheme'        => '{$page_title_h1}',
            ],
        ],
        'blog_category_page' => [
            'parent_type_id'    => 'blog_landing_page',
            'label'             => 'Blog Category Page',
            'description'       => 'Second-level blog category page for displaying blogs published to a specific category.',
            'template'          => [
                'label'             => 'Blog Category Page Layout',
                'template'          => '
                    &lt;div&gt;{$heading}&lt;/div&gt;
                    
                    {assign var=category value=\Cms::getContentByUri()}
                    
                    {if !empty($category.content_uid)}
                        {assign var=all_category_blogs value=\Cms::getChildContentByContentUid($category.content_uid)}
                        {assign var=blogs value=\Cms::getContentPaged($all_category_blogs)}
                        
                        &lt;div class="two-thirds_left"&gt;
                            {foreach from=$blogs item=blog}
                                &lt;h3&gt;
                                    &lt;a href="{$blog.uri}"&gt;
                                        {$blog.page_title_h1}&lt;/a&gt;
                                    &lt;/h3&gt;
                                    &lt;p&gt;
                                        {if !empty($blog.body_fields.subtitle)}
                                            {$blog.body_fields.subtitle}
                                        {else}
                                            {\Cms::teaser($blog.body_fields.body)}
                                        {/if}
                                    &lt;br /&gt;
                                    &lt;span class="caption"&gt;
                                        By:
                                        &lt;a href="/users/{$blog.body_fields.author}"&gt;{$blog.body_fields.author}&lt;/a&gt;
                                        on {date(\'F d, Y g:i:s A T\', $blog.body_fields.published_date)}
                                    &lt;/span&gt;
                                &lt;/p&gt;
                            {/foreach}
                            
                            {\Cms::pager($all_category_blogs)}
                        &lt;/div&gt;
                    {/if}
                    
                    
                    {assign var=this_category_page value=\Cms::getContentByUri()}
                    {assign var=categories value=\Cms::getChildContentByContentUid($this_category_page.parent_content_uid)}
                    
                    {if !empty($categories)}
                    &lt;div class="one-third_right slight_padding gray_background"&gt;
                    {foreach from=$categories item=category}
                        &lt;h4 {if $category.content_uid == $this_category_page.content_uid}class="bold italic"{/if}&gt;
                            &lt;a href="{$category.uri}"&gt;
                                {$category.page_title_h1}&lt;/a&gt;
                            &lt;/h4&gt;
                    {/foreach}
                    &lt;/div&gt;
                    {/if}
                ',
                'uri_scheme'        => '{$page_title_h1}',
            ],
        ],
        'blog_article' => [
            'parent_type_id'    => 'blog_category_page',
            'label'             => 'Blog Article',
            'description'       => 'Third-level, blog article page with HTML content, publishing information and replies.',
            'template'          => [
                'label'             => 'Blog Article Layout',
                'template'          => '
                    &lt;div class="float_left"&gt;
                    &lt;p&gt;
                        Created by &lt;a href="/users/{$author}"&gt;{$author}&lt;/a&gt;
                        &lt;br /&gt;
                        &lt;span class="caption"&gt;{$published_date} {if !empty($revised_date)}(Revised {$revised_date}){/if}&lt;/span&gt;
                    &lt;/p&gt;
                &lt;/div&gt;
                
                &lt;hr class="clear_both" /&gt;
                
                {if !empty($subtitle)}
                    &lt;h2&gt;
                        {$subtitle}
                    &lt;/h2&gt;
                {/if}
                
                {if !empty($image)}
                    &lt;div class="blog_image article"&gt;
                        &lt;img src="{$image}" /&gt;
                    &lt;/div&gt;
                {/if}
                
                
                &lt;div&gt;
                    {$body}
                &lt;/div&gt;
                ',
                'uri_scheme'        => '{$author}-{date(\'Y-m-d\',$published_date)}-{$page_title_h1}',
            ],
        ],
    ];

    static $content_body_field_types = [
        'short_text'    => [
            'label'         => 'Basic Short Text',
            'description'   => 'Single-line basic text field',
        ],
        'rich_text' => [
            'label'         => 'Full Rich-text Content',
            'description'   => 'CK Editor-powered HTML generated content - a field which can include formatted fonts, tables, forms, images, file uploads and more.'
        ],
        'published_date' => [
            'label'         => 'Content Published Date',
            'description'   => 'Date of which content was originally published',
        ],
        'revised_date'  => [
            'label'         => 'Content Revision Date',
            'description'   => 'Date of which content was most recently edited',
        ],
        'author'        => [
            'label'         => 'Author',
            'description'   => 'Publisher of content',
        ],
        'file_upload'   => [
            'label'         => 'File Upload',
            'description'   => '',
        ],
    ];

    static $content_body_fields = [
        'basic_page' => [
            'e5a0c8bc-7486-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'rich_text',
                'template_token'                        => 'body',
                'label'                                 => 'Page Body',
                'description'                           => '',
            ],
        ],
        'blog_landing_page' => [
            '9ba9e845-7487-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'rich_text',
                'template_token'                        => 'heading',
                'label'                                 => 'Heading',
                'description'                           => '',
            ],
        ],
        'blog_category_page' => [
            'ce83c8fa-7487-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'rich_text',
                'template_token'                        => 'heading',
                'label'                                 => 'Heading',
                'description'                           => '',
            ],
        ],
        'blog_article' => [
            'f1687091-7487-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'author',
                'template_token'                        => 'author',
                'label'                                 => 'Author',
                'description'                           => '',
            ],
            '0d704a5d-7488-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'published_date',
                'template_token'                        => 'published_date',
                'label'                                 => 'Date Published',
                'description'                           => '',
            ],
            '1b996622-7488-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'revised_date',
                'template_token'                        => 'revised_date',
                'label'                                 => 'Date Revised',
                'description'                           => '',
            ],
            'ff1a3647-7487-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'short_text',
                'template_token'                        => 'subtitle',
                'label'                                 => 'Subtitle',
                'description'                           => '',
            ],
            '20ba5352-940d-11ea-9071-0242ac190003'  => [
                'content_body_field_type_id'            => 'file_upload',
                'template_token'                        => 'image',
                'label'                                 => 'Blog Image',
                'description'                           => '',
            ],
            '2b9dfc04-7488-11ea-bc99-0242ac190005'  => [
                'content_body_field_type_id'            => 'rich_text',
                'template_token'                        => 'body',
                'label'                                 => 'Page Body',
                'description'                           => '',
            ],
        ],
    ];

    static $uris = [
        'a4998d74-9d77-11ea-bab6-0242ac190003'  => '/home',
    ];

    static $content = [
        'a49996d1-9d77-11ea-bab6-0242ac190003'  => [
            'uri_uid'                               => 'a4998d74-9d77-11ea-bab6-0242ac190003',
            'content_body_type_id'                  => 'basic_page',
            'content_iteration'                     => [
                'uid'                                   => '4ab400e6efde3fedfc52b94540ceb095e45982eb070c0fddf8dfe5214f64b34bb78be49b8d8943a2b90f0427ddc9953fff28c1f74b69cfc3321b18b177b7a876',
                'page_title_seo'                        => 'Welcome to the Gaseous Content Management System!',
                'page_title_h1'                         => '',
                'meta_desc'                             => 'Gaseous Content Management System home page',
                'meta_robots'                           => 'index,follow',
                'status'                                => 'active',
                'include_in_sitemap'                    => '1',
                'minify_html_output'                    => '1',
                'body'                                  => [
                    'e5a0c8bc-7486-11ea-bc99-0242ac190005'  => '
                        &lt;h3&gt;
                            Welcome!
                        &lt;/h3&gt;
                        &lt;h4&gt;
                            Gaseous Content Management System v' . APP_VERSION  . '
                        &lt;/h4&gt;
                    ',
                ],
            ],
        ],
    ];


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setContentBodyTypes(PdoMySql $transaction)
    {
        try {
            foreach (self::$content_body_types as $type_id => $data) {
                $parent_type_id = $data['parent_type_id'];
                $label          = $data['label'];
                $description    = $data['description'];

                $template       = $data['template']['template'];
                $template_label = $data['template']['label'];
                $uri_scheme     = $data['template']['uri_scheme'];

                $sql = "
                    INSERT INTO content_body_types (
                        type_id,
                        parent_type_id,
                        label,
                        description
                    ) VALUES (
                        ?,
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        parent_type_id = ?,
                        label = ?,
                        description = ?,
                        modified_datetime = NOW(); 
                ";

                $bind = [
                    $type_id,
                    $parent_type_id,
                    $label,
                    $description,
                    $parent_type_id,
                    $label,
                    $description,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);


                $sql = "
                    INSERT INTO content_body_templates (
                        content_body_type_id,
                        label,
                        template,
                        uri_scheme
                    ) VALUES (
                        ?,
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        label = ?,
                        template = ?,
                        uri_scheme = ?,
                        modified_datetime = NOW(); 
                ";

                $bind = [
                    $type_id,
                    $template_label,
                    $template,
                    $uri_scheme,
                    $template_label,
                    $template,
                    $uri_scheme,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setContentBodyFieldTypes(PdoMySql $transaction)
    {
        try {
            foreach (self::$content_body_field_types as $type_id => $data) {
                $label          = $data['label'];
                $description    = $data['description'];

                $sql = "
                    INSERT INTO content_body_field_types (
                        type_id,
                        label,
                        description
                    ) VALUES (
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        label = ?,
                        description = ?,
                        modified_datetime = NOW(); 
                ";

                $bind = [
                    $type_id,
                    $label,
                    $description,
                    $label,
                    $description,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setContentBodyFields(PdoMySql $transaction)
    {
        try {
            foreach (self::$content_body_fields as $content_body_type_id => $field_data) {
                $i = (int)0;

                foreach ($field_data as $uid => $data) {
                    $i++;
                    $content_body_field_type_id = $data['content_body_field_type_id'];
                    $template_token             = $data['template_token'];
                    $label                      = $data['label'];
                    $description                = $data['description'];

                    $sql = "
                        INSERT INTO content_body_fields (
                            uid,
                            content_body_type_id,
                            content_body_field_type_id,
                            template_token,
                            label,
                            description,
                            sort_order
                        ) VALUES (
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?
                        ) ON DUPLICATE KEY UPDATE
                            content_body_type_id = ?,
                            content_body_field_type_id = ?,
                            template_token = ?,
                            label = ?,
                            description = ?,
                            sort_order = ?,
                            modified_datetime = NOW(); 
                    ";

                    $bind = [
                        $uid,
                        $content_body_type_id,
                        $content_body_field_type_id,
                        $template_token,
                        $label,
                        $description,
                        $i,
                        $content_body_type_id,
                        $content_body_field_type_id,
                        $template_token,
                        $label,
                        $description,
                        $i,
                    ];

                    $transaction
                        ->prepare($sql)
                        ->execute($bind);
                }
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setUris(PdoMySql $transaction)
    {
        try {
            foreach (self::$uris as $uid => $uri) {
                $sql = "
                    INSERT INTO uri (
                        uid,
                        uri
                    ) VALUES (
                        ?,
                        ?
                    );
                ";

                $bind = [
                    $uid,
                    $uri,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }


    /**
     * @param PdoMySql $transaction
     * @return PdoMySql
     */
    public function setContent(PdoMySql $transaction)
    {
        try {
            foreach (self::$content as $content_uid => $content_data) {
                $parent_uid             = $content_data['parent_uid'] ?? null;
                $uri_uid                = $content_data['uri_uid'];
                $content_body_type_id   = $content_data['content_body_type_id'];

                $content_iteration_uid  = $content_data['content_iteration']['uid'];
                $page_title_seo         = $content_data['content_iteration']['page_title_seo'];
                $page_title_h1          = $content_data['content_iteration']['page_title_h1'];
                $meta_desc              = $content_data['content_iteration']['meta_desc'];
                $meta_robots            = $content_data['content_iteration']['meta_robots'];
                $status                 = $content_data['content_iteration']['status'];
                $include_in_sitemap     = $content_data['content_iteration']['include_in_sitemap'];
                $minify_html_output     = $content_data['content_iteration']['minify_html_output'];
                $generated_page_uri     = $content_data['content_iteration']['generated_page_uri'] ?? null;

                foreach ($content_data['content_iteration']['body'] as $content_body_field_uid => $value) {
                    $sql = "
                        INSERT INTO content_body_field_values (
                            content_iteration_uid,
                            content_body_field_uid,
                            `value`
                        ) VALUES (
                            ?,
                            ?,
                            ?
                        );
                    ";

                    $bind = [
                        $content_iteration_uid,
                        $content_body_field_uid,
                        $value,
                    ];

                    $transaction
                        ->prepare($sql)
                        ->execute($bind);
                }

                $sql = "
                    INSERT INTO content (
                        uid,
                        parent_uid,
                        content_body_type_id,
                        uri_uid
                    ) VALUES (
                        ?,
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        parent_uid = ?,
                        content_body_type_id = ?,
                        uri_uid = ?,
                        modified_datetime = NOW();
                ";

                $bind = [
                    $content_uid,
                    $parent_uid,
                    $content_body_type_id,
                    $uri_uid,
                    $parent_uid,
                    $content_body_type_id,
                    $uri_uid,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);



                $sql = "
                    INSERT INTO content_iteration (
                        uid,
                        page_title_seo,
                        page_title_h1,
                        meta_desc,
                        meta_robots,
                        generated_page_uri,
                        status,
                        include_in_sitemap,
                        minify_html_output
                    ) VALUES (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    ) ON DUPLICATE KEY UPDATE
                        page_title_seo = ?,
                        page_title_h1 = ?,
                        meta_desc = ?,
                        meta_robots = ?,
                        generated_page_uri = ?,
                        status = ?,
                        include_in_sitemap = ?,
                        minify_html_output = ?,
                        modified_datetime = NOW();
                ";

                $bind = [
                    $content_iteration_uid,
                    $page_title_seo,
                    $page_title_h1,
                    $meta_desc,
                    $meta_robots,
                    $generated_page_uri,
                    $status,
                    $include_in_sitemap,
                    $minify_html_output,
                    $page_title_seo,
                    $page_title_h1,
                    $meta_desc,
                    $meta_robots,
                    $generated_page_uri,
                    $status,
                    $include_in_sitemap,
                    $minify_html_output,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);


                $sql = "
                    INSERT INTO current_content_iteration (
                        content_uid,
                        content_iteration_uid
                    ) VALUES (
                        ?,
                        ?
                    );
                ";

                $bind = [
                    $content_uid,
                    $content_iteration_uid,
                ];

                $transaction
                    ->prepare($sql)
                    ->execute($bind);
            }
        } catch (PDOException $p) {
            $transaction->rollBack();

            Log::app($p->getTraceAsString(), $p->getMessage());

            throw $p;
        }

        return $transaction;
    }
}