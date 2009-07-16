<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
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
 * Form widget base class.
 *
 * <p>Form widgets are widgets that represent various HTML form input elements.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.store.mvc.widgets.form
 * @version $Id: ZMFormWidget.php 1974 2009-02-16 11:05:06Z dermanomann $
 */
abstract class ZMFormWidget extends ZMWidget {
    private $name_;
    private $value_;
    private $attributeNames_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->name_ = '';
        $this->value_ = null;
        $this->attributeNames_ = array();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Set the name.
     *
     * @param string name The name.
     */
    public function setName($name) {
        $this->name_ = $name;
    }

    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function getName() {
        return $this->name_;
    }

    /**
     * Set the value.
     *
     * @param mixed value The value.
     */
    public function setValue($value) {
        $this->value_ = $value;
    }

    /**
     * Get the value.
     *
     * @return mixed The value.
     */
    public function getValue() {
        return $this->value_;
    }

    /**
     * Set the list of supported attributes.
     *
     * @param array names The attribute names.
     */
    public function setAttributeNames($names) {
        $this->attributeNames_ = $names;
    }

    /**
     * Get the list of supported attributes.
     *
     * @return array The attribute names.
     */
    public function getAttributeNames() {
        return $this->attributeNames_;
    }

    /**
     * Get the formatted attribute string.
     *
     * @param boolean addValue Optional flag to include/exclude the value; default is <code>true</code>.
     * @return string All set (and allowed) attributes as formatted HTML string.
     */
    public function getAttributeString($addValue=true) {
        $attr = ' name="'.$this->getName().'"';
        $html = ZMToolbox::instance()->html;
        foreach ($this->properties_ as $name => $value) {
            if (in_array($name, $this->attributeNames_)) {
                $attr .= ' '.$name.'="'.$html->encode($value, false).'"';
            }
        }

        if ($addValue) {
            $attr .= ' value="'.$html->encode($this->getValue(), false).'"';
        }

        return $attr;
    }


    /**
     * Compare the given value with the widget value.
     *
     * @param string value A string value.
     * @return boolean <code>true</code> if the given value evaluates to the
     * same value as the widget value.
     */
    public abstract function compare($value);

}

?>
