<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006 ZenMagick
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
 */
?>
<?php


/**
 * Simple theme view.
 *
 * <p>The content is either a full page or a layout using the page specified in the request.</p>
 *
 * @author mano
 * @package net.radebatz.zenmagick.uip.views
 * @version $Id$
 */
class ZMThemeView extends ZMView {
    var $page_;


    /**
     * Default c'tor.
     */
    function ZMThemeView($page) {
        $this->page_ = $page;
    }

    /**
     * Default c'tor.
     */
    function __construct($page) {
        $this->ZMThemeView($page);
    }

    function __destruct() {
    }


    /**
     * Return the page name.
     *
     * @return string The page name.
     */
    function getPage() { return $this->page_; }

    /**
     * Return the template name.
     *
     * @return string The template name or <code>null</code>.
     */
    function getTemplate() {
    global $zm_theme;

        $themeInfo = $zm_theme->getThemeInfo();
        return $themeInfo->getTemplateFor($this->page_);
    }


    /**
     * Returns the full view filename to be includes by a template.
     *
     * @param string subdir Optional subdirectory name within the views directory.
     * @param bool $prefixToDir If <code>true</code> the subdir is assumed to be the view filename prefix; eg: 'popup_'. If this is the case,
     *  it gets converted into an additional ssubdir instead. Example: <code>popup_cvv_help.php</code> = <code>popup/cvv_help.php</code>.
     * @return string The view filename.
     */
    function getViewFilename($subdir=null, $prefixToDir=true) {
    global $zm_theme;

        $themeInfo = $zm_theme->getThemeInfo();
        $filename = $themeInfo->getViewDir();
        if (null != $subdir) {
            $filename .= $subdir.'/';
            if ($prefixToDir) {
                $filename .= substr($this->page_, strlen($subdir)+1);
            } else {
                $filename .= $this->page_;
            }
        } else {
            $filename .= $this->page_;
        }
        $filename .= '.php';
        return $filename;
    }


    /**
     * Generate view response.
     */
    function generate() { 
    global $zm_theme;

        $controller = $this->getController();
        // *export* globals from controller into view space
        foreach ($controller->getGlobals() as $name => $instance) {
            $$name = $instance;
        }
        $zm_view = $this;

        $template = $this->getTemplate();
        if (null != $template) {
            $zm_content_include = $this->page_;
            include($zm_theme->getThemePath($template.'.php'));
        } else {
            include($zm_theme->getThemePath($this->page_.'.php'));
        }
    }

}

?>
