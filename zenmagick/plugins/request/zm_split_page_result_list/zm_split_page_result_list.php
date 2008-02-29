<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2008 ZenMagick
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
 * Plugin that implements resultlist and friends using zen-cart's default_filter.
 *
 * @package org.zenmagick.plugins.zm_split_page_result_list
 * @author mano
 * @version $Id$
 */
class zm_split_page_result_list extends ZMPlugin {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct('Split Page Result List', 'Result list based on zen-cart\'s split_page_results class', '${plugin.version}');
        $this->setLoaderSupport('ALL');
    }

    /**
     * Create new instance.
     */
    function zm_split_page_result_list() {
        $this->__construct();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

}

?>
