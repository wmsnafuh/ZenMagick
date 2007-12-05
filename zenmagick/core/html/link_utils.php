<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006,2007 ZenMagick
 *
 * Portions Copyright (c) 2003 The zen-cart developers
 * Portions Copyright (c) 2003 osCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * $Id$
 */
?>
<?php


    /**
     * Create a URL for a href.
     *
     * <p>If the <code>view</code> argument is <code>null</code>, the current view will be
     * used. The provided parameter will be merged into the current query string.</p>
     *
     * @package org.zenmagick.html
     * @param string view The view name (ie. the page name as referred to by the parameter <code>main_page</code>)
     * @param string params Query string style parameter; if <code>null</code> add all current parameter
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A full URL.
     */
    function zm_href($view=null, $params='', $echo=true) { return _zm_build_href($view, $params, false, $echo); }


    /**
     * Secure version of {@link org.zenmagick.html#zm_href zm_href}.
     *
     * @package org.zenmagick.html
     * @param string view The view name (ie. the page name as referred to by the parameter <code>main_page</code>)
     * @param string params Query string style parameter; if <code>null</code> add all current parameter
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A full, secure URL.
     */
    function zm_secure_href($view=null, $params='', $echo=true) { return _zm_build_href($view, $params, true, $echo); }


    /**
     * ZenMagick implementation of zen-cart's zen_href_link function.
     */
    function _zm_zen_href_link($page='', $params='', $transport='NONSSL', $addSessionId=true, $seo=true, $isStatic=false, $useContext=true) {
    //TODO:
    global $request_type, $session_started, $http_domain, $https_domain;

        if (zm_is_empty($page)) zm_backtrace('missing page parameter');

        // default to non ssl
        $server = HTTP_SERVER;
        if ($transport == 'SSL' && zm_setting('isEnableSSL')) {
            $server = HTTPS_SERVER;
        }

        $path = '';
        if ($useContext) {
            $path = HTTPS_SERVER == $server ? DIR_WS_HTTPS_CATALOG : DIR_WS_CATALOG;
        }

        // trim '?' and '&' from params
        while ('?' == ($char = substr($params, 0, 1)) || '&' == $char) $params = substr($params, 1);
        while ('?' == ($char = substr($params, -1)) || '&' == $char) $params = substr($params, 0, -1);

        $query = '?';
        if ($isStatic) {
            $path .= $page;
        } else {
            $path .= 'index.php';
            $query .= 'main_page=' . $page . '&';
        }

        if (!zm_is_empty($params)) {
            $query .= strtr(trim($params), array('"' => '&quot;'));
        }

        // trim trailing '?' and '&' from path
        while ('?' == ($char = substr($path, -1)) || '&' == $char) $path = substr($path, 0, -1);

        // Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
        $sid = null;
        if ($addSessionId && $session_started && !zm_setting('isForceCookieUse')) {
            if (defined('SID') && !zm_is_empty(SID)) {
                // defined, so use it
                $sid = SID;
            } elseif (($request_type == 'NONSSL' && HTTPS_SERVER == $server) || ($request_type == 'SSL' && HTTP_SERVER == $server)) {
                // switch from http to https or vice versa
                if ($http_domain != $https_domain) {
                    $sid = zen_session_name() . '=' . zen_session_id();
                }
            }
        }

        if (null !== $sid) {
            $query .= '&' . strtr(trim($sid), array('"' => '&quot;'));
        }

        $query = (1 < strlen($query)) ? $query : '';

        return zm_htmlurlencode($server.$path.$query);
    }


    /**
     * Build a href / url.
     */
    function _zm_build_href($view=null, $params='', $isSecure=false, $echo=true) {
    global $zm_request;

        // custom view and params handling
        if (null === $view || null === $params) {
            $query = array();
            if (null === $view || null === $params) {
                parse_str($zm_request->getQueryString(), $query);
                unset($query['main_page']);
            }
            if (null != $params) {
                parse_str($params, $arr);
                $query = array_merge($query, $arr);
            }
            $params = '';
            foreach ($query as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $params .= "&".$name."[]=".$subValue;
                    }
                } else {
                    $params .= "&".$name."=".$value;
                }
            }
        }

        // default to current view
        $view = $view == null ? $zm_request->getPageName() : $view;

        $href= null;
        if (function_exists('zm_build_seo_href')) {
            // use custom SEO builder function
            $href = zm_build_seo_href($view, $params, $isSecure);
        } else {
            // use default implementation
            $href = _zm_zen_href_link($view, $params, $isSecure ? 'SSL' : 'NONSSL');
        }

        if ($echo) echo $href;
        return $href;
    }


    /**
     * Convenience function.
     *
     * <p>Please note that in <em>ZenMagick</em> all product URLs use the same
     * view name. The actual view name gets resolved only when the href is used.</p>
     *
     * @package org.zenmagick.html
     * @param int productId The product id
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A full URL.
     * @return string A complete product URL.
     */
    function zm_product_href($productId, $echo=true) { 
        return _zm_build_href(FILENAME_PRODUCT_INFO, '&products_id='.$productId, false, $echo);
    }

    /**
     * Convenience function.
     *
     * @package org.zenmagick.html
     * @param string catName The static page name.
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A complete URL for the given static page.
     */
    function zm_static_href($catName, $echo=true) { 
        return _zm_build_href('static', '&cat='.$catName, false, $echo);
    }

    /**
     * Back link.
     *
     * <p>In constrast to the <code>..._href</code> functions, this one will
     * return a full HTML <code>&lt;a&gt;</code> tag.</p>
     *
     * @package org.zenmagick.html
     * @param string text The link text (can be plain text or HTML).
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A fully formated HTML <code>&lt;a&gt;</code> tag.
     */
    function zm_back_link($text, $echo=true) {
        $link = zen_back_link() . $text . '</a>';

        if ($echo) echo $link;
        return $link;
    }

    /**
     * Build href for ez-page.
     *
     * @package org.zenmagick.html
     * @param ZMEZPage page A <code>ZMEZPage</code> instance.
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A complete URL for the given ez-page.
     */
    function zm_ezpage_href($page, $echo=true) {
        if (null === $page) {
            $href = zm_l10n_get('ezpage not found');
            if ($echo) echo $href;
            return $href;
        }
        $params = '&id='.$page->getId();
        if (0 != $page->getTocChapter()) {
            $params .= '&chapter='.$page->getTocChapter();
        }
        $href = $page->isSSL() ? zm_secure_href(FILENAME_EZPAGES, $params, false) : zm_href(FILENAME_EZPAGES, $params, false);
        if (!zm_is_empty($page->getAltUrl())) {
            $url = parse_url($page->getAltUrl());
            parse_str($url['query'], $query);
            $view = $query['main_page'];
            unset($query['main_page']);
            $params = '';
            foreach ($query as $name => $value) {
                $params .= "&".$name."=".$value;
            }
            $href = $page->isSSL() ? zm_secure_href($view, $params, false) : zm_href($view, $params, false);
        } else if (!zm_is_empty($page->getAltUrlExternal())) {
            $href = $page->getAltUrlExternal();
        }

        if ($echo) echo $href;
        return $href;
    }


    /**
     * Create a full HTML &lt;a&gt; tag.
     *
     * @package org.zenmagick.html
     * @param integer id The EZ page id.
     * @param string text Optional link text.
     * @param boolean echo If <code>true</code>, the link will be echo'ed as well as returned.
     * @return string A full HTML link.
     */
    function zm_ezpage_link($id, $text=null, $echo=true) {
    global $zm_pages;
        $page = $zm_pages->getPageForId($id);
        $link = '<a href="' . zm_ezpage_href($page, false) . '"' . zm_href_target($page->isNewWin(), false) . '>' . (null == $text ? $page->getTitle() : $text) . ' </a>';

        if ($echo) echo $link;
        return $link;
    }


    /**
     * Create an absolute image path for the given image.
     *
     * @package org.zenmagick.html
     * @param string src The relative image name (relative to zen-cart's image folder).
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string The image URI.
     */
    function zm_image_uri($src, $echo=true) {
        $href = DIR_WS_CATALOG.DIR_WS_IMAGES . $src;

        if ($echo) echo $href;
        return $href;
    }


    /**
     * Create an redirect href for the given action and id.
     *
     * <p>All messages created up to this point during request handling will be saved and
     * restored with the next request handling cycle.</p>
     *
     * @package org.zenmagick.html
     * @param string action The redirect action.
     * @param string id The redirect id.
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A full URL.
     */
    function zm_redirect_href($action, $id, $echo=true) {
    global $zm_messages;

        if ($zm_messages->hasMessages()) {
            $session = new ZMSession();
            $session->setMessages($zm_messages->getMessages());
        }

        return _zm_build_href(FILENAME_REDIRECT, "action=".$action."&goto=".$id, false, $echo);
    }


    /**
     * Convert a given relative href/URL into a absolute one based on the current context.
     *
     * @package org.zenmagick.html
     * @param string href The URL to convert..
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string The absolute href.
     */
    function zm_absolute_href($href, $echo=true) {
    global $zm_request;

        $host = ($zm_request->isSecure() ? HTTPS_SERVER : HTTP_SERVER);
        $context = ($zm_request->isSecure() ? DIR_WS_HTTPS_CATALOG : DIR_WS_CATALOG);

        if (!zm_starts_with($href, '/')) {
            // make fully qualified
            $href = $context . $href;
        }

        // make full URL
        $href = $host . $href;

        if ($echo) echo $href;
        return $href;
    }


    /**
     * Media href.
     *
     * @package org.zenmagick.html
     * @param string filename The media filename.
     * @param boolean echo If <code>true</code>, the formatted text will be echo'ed as well as returned.
     * @return A URL.
     */
    function zm_media_href($filename, $echo=true) {
        $href = DIR_WS_MEDIA.$filename;

        if ($echo) echo $href;
        return $href;
    }

    /**
     * Convenience function.
     *
     * <p><strong>NOTE:</strong> Ampersand are not encoded in this function.</p>
     *
     * @package org.zenmagick.html
     * @param string controller The controller name without the leading <em>ajax_</em>.
     * @param string method The name of the method to call.
     * @param string params Query string style parameter; if <code>null</code> add all current parameter
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A complete Ajax URL.
     */
    function zm_ajax_href($controller, $method, $params='', $echo=true) { 
    global $zm_request;

        $url = str_replace('&amp;', '&', _zm_build_href('ajax_'.$controller, $params.'&method='.$method, $zm_request->isSecure(), false));

        if ($echo) echo $url;
        return $url;
    }

    /**
     * Convenience function.
     *
     * @package org.zenmagick.html
     * @param string channel The channel.
     * @param string key Optional key, for example, 'new' for the product channel.
     * @param boolean echo If <code>true</code>, the URI will be echo'ed as well as returned.
     * @return string A complete URL.
     */
    function zm_rss_feed_href($channel, $key=null, $echo=true) { 
        $params = 'channel='.$channel;
        if (null !== $key) {
            $params .= "&key=".$key;
        }
        $url = zm_href(ZM_FILENAME_RSS, $params, false);

        if ($echo) echo $url;
        return $url;
    }

?>
