<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
 *
 * Portions Copyright (c)      Vojtech Semecky, webmaster @ webdot . cz
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
 * A RSS feed channel.
 *
 * <p>ZenMagick wrapper around <code>lastRSS</code>.
 *
 * @author DerManoMann
 * @package org.zenmagick.store.model.rss
 * @version $Id: ZMRssChannel.php 2121 2009-03-31 01:56:56Z dermanomann $
 */
class ZMRssChannel extends ZMObject {
    private $rs_;


    /**
     * Create new RSS channel.
     *
     * @param array Channel data.
     */
    function __construct($rs=null) {
        parent::__construct();
        $this->rs_ = null !== $rs ? $rs : array();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get the channel title.
     *
     * @return string The channel title.
     */
    public function getTitle() { return $this->rs_['title']; }

    /**
     * Get the channel link.
     *
     * @return string The channel link.
     */
    public function getLink() { return $this->rs_['link']; }

    /**
     * Get the channel encoding.
     *
     * @return string The channel encoding.
     */
    public function getEncoding() { return $this->rs_['encoding']; }

    /**
     * Get the channel description.
     *
     * @return string The channel description.
     */
    public function getDescription() { return $this->rs_['description']; }

    /**
     * Get the channels last build date.
     *
     * @return string The channels last build date.
     */
    public function getLastBuildDate() { return $this->rs_['lastBuildDate']; }

    /**
     * Get the channels image title.
     *
     * @return string The channels image title.
     */
    public function getImageTitle() { return $this->rs_['image_title']; }

    /**
     * Get the channels image link.
     *
     * @return string The channels image link.
     */
    public function getImageLink() { return $this->rs_['image_link']; }

    /**
     * Get the channels image width.
     *
     * @return string The channels image width.
     */
    public function getImageWidth() { return $this->rs_['image_width']; }

    /**
     * Get the channels image height.
     *
     * @return string The channels image height.
     */
    public function getImageHeight() { return $this->rs_['image_height']; }

    /**
     * Checks if the channel has an image.
     *
     * @return boolean <code>true</code> if a channel image is available, <code>false</code> if not.
     */
    public function hasImage() { return array_key_exists($this->rs_, 'image_url'); }

    /**
     * Set the channel title.
     *
     * @param string title The channel title.
     */
    public function setTitle($title) { $this->rs_['title'] = $title; }

    /**
     * Set the channel link.
     *
     * @param string link The channel link.
     */
    public function setLink($link) { $this->rs_['link'] = $link; }

    /**
     * Set the channel encoding.
     *
     * @param string encoding The channel encoding.
     */
    public function setEncoding($encoding) { $this->rs_['encoding'] = $encoding; }

    /**
     * Set the channel description.
     *
     * @param string description The channel description.
     */
    public function setDescription($description) { $this->rs_['description'] = $description; }

    /**
     * Set the channels last build date.
     *
     * @param string date The channels last build date.
     */
    public function setLastBuildDate($date) { $this->rs_['lastBuildDate'] = $date; }

    /**
     * Set the channels image title.
     *
     * @param string title The channels image title.
     */
    public function setImageTitle($title) { $this->rs_['image_title'] = $title; }

    /**
     * set the channels image link.
     *
     * @param string link The channels image link.
     */
    public function setImageLink($lin) { $this->rs_['image_link'] = $link; }

    /**
     * set the channels image width.
     *
     * @param int width The channels image width.
     */
    public function setImageWidth($width) { $this->rs_['image_width'] = $width; }

    /**
     * Set the channels image height.
     *
     * @param int height The channels image height.
     */
    public function setImageHeight($height) { $this->rs_['image_height'] = $height; }

    /**
     * Custom set method for properties that do not have a dedicated
     * access method.
     *
     * @param string name The property name.
     * @param string value The value.
     */
    public function setProperty($name, $value) { $this->rs_[$name] = $value; }

    /**
     * Custom get method for properties that do not have a dedicated
     * access method.
     *
     * @param string name The property name.
     * @return string value The value or <code>null</code>.
     */
    public function getProperty($name) { return isset($this->rs_[$name]) ? $this->rs_[$name] : null; }

    /**
     * Get all properties.
     *
     * @return array Name/value map.
     */
    public function getProperties() { return $this->rs_; }

}

?>