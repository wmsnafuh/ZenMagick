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
 */
?>
<?php


/**
 * Container class for all zen-cart observations we'll hopefully never need.
 *
 * @author mano
 * @package net.radebatz.zenmagick.misc
 * @version $Id$
 */
class ZMZcoObserver extends ZMObject {

    /**
     * Default c'tor.
     */
    function ZMZcoObserver() {
    global $zco_notifier;

        parent::__construct();

        $zco_notifier->attach($this, array('NOTIFY_HEADER_START_GV_SEND'));
        $zco_notifier->attach($this, array('NOTIFY_LOGIN_SUCCESS_VIA_CREATE_ACCOUNT'));
    }

    /**
     * Default c'tor.
     */
    function __construct() {
        $this->ZMZcoObserver();
    }

    /**
     * Default d'tor.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Generic observer callback that delegates to internal methods...
     *
     * <p>The actual method called is generated based on the event id.</p>
     *
     * @param mixed notifier.
     * @param string eventId The event id.
     * @param array args Optional parameter.
     */
    function update($notifier, $eventId, $args) {
        // id is upper case
        $method = strtolower($eventId);
        // remove 
        $method = str_replace('notify', ' ', $method);
        // '_' == word boundary
        $method = str_replace('_', ' ', $method);
        // capitalise words
        $method = ucwords($method);
        // cuddle together :)
        $method = str_replace(' ', '', $method);
        // ad 'on' prefix
        $method = 'on'.$method;

        if (method_exists($this, $method)) {
            zm_log('calling ' . $method . ' callback...');
            call_user_func(array($this, $method), $args);
        }
    }
 

    /**
     * Provide <code>$zm_account</code> right after an account is created...
     */
    function onLoginSuccessViaCreateAccount() {
    global $zm_account, $zm_request;

        $zm_account =& $zm_request->getAccount();
    }

    /**
     * Provide <code>$zm_receiver</code> for gv_send email.
     */
    function onHeaderStartGvSend() {
    global $zm_receiver;

        $zm_receiver = $this->create("GVReceiver");
        $zm_receiver->populateFromRequest();
    }

}

?>
