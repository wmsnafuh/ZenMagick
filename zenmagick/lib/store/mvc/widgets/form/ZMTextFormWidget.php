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
 * <p>A single line text input form widget.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.store.mvc.widgets.form
 * @version $Id: ZMTextFormWidget.php 1975 2009-02-16 11:28:30Z dermanomann $
 */
class ZMTextFormWidget extends ZMFormWidget {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->setAttributeNames(array('id', 'size', 'maxlength'));
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * {@inheritDoc}
     */
    public function render() {
        $slash = ZMSettings::get('isXHTML') ? '/' : '';
        return '<input type="text"'.$this->getAttributeString().$slash.'>';
    }

    /**
     * {@inheritDoc}
     */
    public function compare($value) {
        return $value == $this->getValue();
    }

}

?>
