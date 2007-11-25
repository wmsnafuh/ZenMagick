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
     * Simple function to check if we need zen-cart...
     */
    function zm_needs_zc() {
    global $zm_request;
      
        return zm_is_checkout_page() || zm_is_in_array($zm_request->getPageName(), 'advanced_search_result');
    }

    // skip more zc request handling
    if (zm_setting('isEnableZenMagick') && !zm_needs_zc()) {
        $code_page_directory = 'zenmagick';
    }


    $zm_events->attach(new ZMEventFixes());

    /*****temp fixes for email generation.... ********/
    // set up order for order_status email
    if (null !== $zm_request->getParameter("oID") && 'update_order' == $zm_request->getParameter("action")) {
        $orderId = $zm_request->getParameter("oID");
        $zm_order = $zm_orders->getOrderForId($orderId);
        $zm_account = $zm_accounts->getAccountForId($zm_order->getAccountId());
    }
    /*****temp fixes for email generation.... ********/

    // simulate the number of uploads parameter for add to cart
    if ('add_product' == $zm_request->getParameter('action')) {
        $uploads = 0;
        foreach ($zm_request->getParameterMap() as $name => $value) {
            if (zm_starts_with($name, zm_setting('uploadOptionPrefix'))) {
                ++$uploads;
            }
        }
        $_GET['number_of_uploads'] = $uploads;
    }

    // security fix to allow post for login
    if ("login" == $zm_request->getPageName()) {
        // *disable* zc account create code
        $_GET['action'] = $_POST['action'];
        unset($_POST['action']);
    }

    // zc fails if UI_DATE_FORMAT changes
    define('DOB_FORMAT_STRING', UI_DATE_FORMAT);

?>
