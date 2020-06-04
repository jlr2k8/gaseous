{$body}

{if !empty($content_body_type_id) && $content_body_type_id == 'blog_article'}
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "Blog",
            "author": [
                {
                    "@type": "Person",
                    "name": "{$author}"
                }
            ],
            "name": "{$page_title_h1}",
            "datePublished": "{date('Y-m-d', strtotime($published_date))}",
            "headline": "{if !empty($subtitle)}{$subtitle}{else}{$page_title_seo}{/if}",
            "publisher":
            {
                "@type": "Person",
                "name": "{$author}"
            }
        }
    </script>
{/if}

<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.7.0/highlight.min.js"> </script><script defer="defer" async="async">hljs.initHighlightingOnLoad();</script>
<link href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.7.0/styles/default.min.css" rel="stylesheet" />