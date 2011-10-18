<?php
/* This is where we write our URL regexes. Each pattern contains a callback to a view. */
$url_patterns = array(
    '/^$/' =>
        'simple_mtv\views\home',
    '/^page\/?(?P<page_num>[0-9]+)\/?$/' =>
        'simple_mtv\views\home',

    '/(?:[0-9]{4}\/)(?:[0-9]{2}\/){1}(?P<name>[^\/]+)\/?$/' => # year/month/slug
        'simple_mtv\views\single',

    # Post previews
    '/^post\/preview\/(?P<post_id>\d+)$/' =>
        'simple_mtv\views\single',
    # Page previews
    '/^page\/preview\/(?P<page_id>\d+)$/' =>
        'simple_mtv\views\page',

    # Date based archives
    '/(?P<year>[0-9]{4})\/(?P<month>[0-9]{2})\/?$/' => # year/month/
        'simple_mtv\views\date_archive',
    '/(?<year>[0-9]{4})\/?$/' => # year/
        'simple_mtv\views\date_archive',
	'/(?<year>[0-9]{4})\/page\/(?P<page_num>[0-9])\/?$/' => # year/page/2
        'simple_mtv\views\date_archive',
    '/archive\/?$/' => # total archive
        'simple_mtv\views\archive',

    # Page slugs catch-all ALWAYS LAST!!
    '/(?P<slug>[^\/]+)\/?$/' =>
        'simple_mtv\views\page'
);
