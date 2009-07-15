<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 ZenMagick
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
 * Request controller for product review pages.
 *
 * @author DerManoMann
 * @package org.zenmagick.store.mvc.controller
 * @version $Id: ZMProductReviewsInfoController.php 2350 2009-06-29 04:22:59Z dermanomann $
 */
class ZMProductReviewsInfoController extends ZMController {

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
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
    public function processGet($request) {
        // crumbtrail handling
        ZMCrumbtrail::instance()->addCategoryPath($request->getCategoryPathArray());
        ZMCrumbtrail::instance()->addManufacturer($request->getManufacturerId());
        ZMCrumbtrail::instance()->addProduct($request->getProductId());
        ZMCrumbtrail::instance()->addCrumb("Reviews");

        $data = array();
        $product = ZMProducts::instance()->getProductForId($request->getProductId());
        $data['zm_product'] = $product;

        $review = ZMReviews::instance()->getReviewForId($request->getReviewId());
        $data['zm_review'] = $review;

        if (ZMSettings::get('isLogPageStats')) {
            ZMReviews::instance()->updateViewCount($request->getReviewId());
        }

        return $this->findView(null, $data);
    }

}

?>