<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2011 zenmagick.org
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

use zenmagick\base\Runtime;

/**
 * Blocks.
 *
 * @author DerManoMann
 * @package zenmagick.store.shared.services
 */
class ZMBlocks extends ZMObject {

    /**
     * Get instance.
     */
    public static function instance() {
        return Runtime::getContainer()->get('ZMBlocks');
    }


    /**
     * Get a list of all block group names.
     *
     * @return array List of block group names.
     */
    public function getBlockGroups() {
        $sql = "SELECT DISTINCT group_name FROM " . DB_PREFIX.'block_groups';
        $ids = array();
        foreach (ZMRuntime::getDatabase()->query($sql, array(), TABLE_BANNERS) as $result) {
            $ids[] = $result['group_names'];
        }
        return $ids;
    }

    /**
     * Create a new block group.
     *
     * @param ZMBlockGroup blockGroup The block group.
     */
    public function createBlockGroup(ZMBlockGroup $blockGroup) {
        return ZMRuntime::getDatabase()->createModel('block_groups', $blockGroup);
    }

    /**
     * Create a new block group.
     *
     * @param string name The group name.
     * @param string description Optional description; default is <code>''</code>.
     */
    public function createGroup($name, $description='') {
        $sql = 'INSERT INTO ' . DB_PREFIX.'block_groups' . '(group_name, description) VALUES (:group_name, :description)';
        $group = ZMRuntime::getDatabase()->update($sql, array('group_name' => $name, 'description' => $description), 'block_groups');
        unset($group['rows']);
        $group['group_name'] = $name;
        $group['description'] = $description;
        $group['block_group_id'] = $group['lastInsertId'];
        unset($group['lastInsertId']);
        return $group;
    }

    /**
     * Delete block group.
     *
     * @param string name The group name.
     */
    public function deleteGroupForName($name) {
        return ZMRuntime::getDatabase()->removeModel('block_groups', array('group_name' => $name));
    }

}
