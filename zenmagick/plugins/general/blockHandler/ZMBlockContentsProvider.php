<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 zenmagick.org
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
 * Interface for for block content provider.
 *
 * <p>Classes that generate block contents can implement this interface and then
 * register themselfs via the setting 'plugins.blockHandler.blockContentsProviders'.</p>
 *
 * @package org.zenmagick.plugins.blockHandler
 * @author DerManoMann
 * @version $Id$
 */
interface ZMBlockContentsProvider {
    /**
     * Return list of block contents availabe from this provider.
     *
     * @return array List of block contents objects or bean definitions.
     */
    public function getBlockContentsList();
}

?>