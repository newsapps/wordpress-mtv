<?php

$url_patterns = array(
    '/^$/' =>
        'twentyeleven\views\home',
    '/^rss\/?$/' =>
        'twentyeleven\views\feed',
    '/^page\/?(?P<page_num>[0-9]+)\/?$/' =>
        'twentyeleven\views\home',
    '/^search\/?$/' =>
        'twentyeleven\views\search',

    '/(?:[0-9]{4}\/)(?:[0-9]{2}\/){1}(?P<name>[^\/]+)\/?$/' => # year/month/slug
        'twentyeleven\views\single',

    # Post previews
    '/^post\/preview\/(?P<post_id>\d+)$/' =>
        'twentyeleven\views\single',
    # Page previews
    '/^page\/preview\/(?P<page_id>\d+)$/' =>
        'twentyeleven\views\page',

    # Date based archives
    '/(?P<year>[0-9]{4})\/(?P<month>[0-9]{2})\/?$/' => # year/month/
        'twentyeleven\views\date_archive',
    '/(?<year>[0-9]{4})\/?$/' => # year/
        'twentyeleven\views\date_archive',
    '/archive\/?$/' => # year/
        'twentyeleven\views\archive',

    # Page slugs catch-all ALWAYS LAST!!
    '/(?P<slug>[^\/]+)\/?$/' =>
        'twentyeleven\views\page'
);
