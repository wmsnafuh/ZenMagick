<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006-2010 zenmagick.org
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
 * Simple session handler interface.
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc.session
 * @version $Id$
 */
class ZMZenCartSessionHandler implements ZMSessionHandler {
    private $expiryTime_ = 1440;


    /**
     * {@inheritDoc}
     */
    public function open($path, $name) {
        ZMLogging::instance()->log('session: open', ZMLogging::TRACE);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read($id) {
        ZMLogging::instance()->log('session: read: '.$id, ZMLogging::TRACE);
        $sql = "SELECT value
                FROM " . TABLE_SESSIONS . "
                WHERE sesskey = :sesskey
                AND expiry > :expiry";
        if (null !== ($result = ZMRuntime::getDatabase()->querySingle($sql, array('sesskey' => $id, 'expiry' => time()), TABLE_SESSIONS))) {
            ZMLogging::instance()->log('session: read: '.$id.'; data: '.$result['value'], ZMLogging::TRACE);
            return $result['value'];
        }
        ZMLogging::instance()->log('session: read: '.$id.'; data(false): '.$result['value'], ZMLogging::TRACE);

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function write($id, $data) {
        ZMLogging::instance()->log('session: write: '.$id.'; data: '.$data, ZMLogging::TRACE);
        if (false !== $this->read($id)) {
            // update
            $sql = "UPDATE " . TABLE_SESSIONS . "
                    SET expiry = :expiry, value = :value
                    WHERE sesskey = :sesskey";
        } else {
            // create
            $sql = "INSERT INTO " . TABLE_SESSIONS . "
                    VALUES (:sesskey, :expiry, :value)";
        }

        $args = array('sesskey' => $id, 'value' => $data, 'expiry' => time() + $this->expiryTime_);
        return ZMRuntime::getDatabase()->update($sql, $args, TABLE_SESSIONS);
    }

    /**
     * {@inheritDoc}
     */
    public function destroy($id) {
        ZMLogging::instance()->log('session: destroy: '.$id, ZMLogging::TRACE);
        $sql = "DELETE FROM " . TABLE_SESSIONS . " WHERE sesskey = :sesskey";
        return ZMRuntime::getDatabase()->update($sql, array('sesskey' => $id), TABLE_SESSIONS);
    }

    /**
     * {@inheritDoc}
     */
    public function gc($lifetime) {
        ZMLogging::instance()->log('session: gc: '.$lifetime, ZMLogging::TRACE);
        $sql = "DELETE FROM " . TABLE_SESSIONS . " where expiry < :expiry";
        return ZMRuntime::getDatabase()->update($sql, array('expiry' => time()), TABLE_SESSIONS);
    }

    /**
     * {@inheritDoc}
     */
    public function close() {
        ZMLogging::instance()->log('session: close', ZMLogging::TRACE);
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setExpiryTime($expiryTime) {
        ZMLogging::instance()->log('session: set expiry time: '.$expiryTime, ZMLogging::TRACE);
        $this->expiryTime_ = $expiryTime;
    }

}
