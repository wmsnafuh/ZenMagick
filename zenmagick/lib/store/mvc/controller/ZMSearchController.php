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
 * Search controller.
 *
 * <p>The default for <em>autoSearch</em> is <code>true</code>.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.store.mvc.controller
 * @version $Id: ZMSearchController.php 2350 2009-06-29 04:22:59Z dermanomann $
 */
class ZMSearchController extends ZMController {
    private $autoSearch_;

    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->autoSearch_ = true;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Set the auto search flag.
     *
     * <p>If enabled, the controller will automatically run a search even if only the keyword is set.
     * This allows to create simple URLs that run a search.</p>
     *
     * @param boolean autoSearch The new value.
     */
    public function setAutoSearch($autoSearch) {
        $this->autoSearch_ = ZMLangUtils::asBoolean($autoSearch);
    }

    /**
     * Get the auto search setting.
     *
     * @return boolean The auto search flag.
     */
    public function isAutoSearch() {
        return $this->autoSearch_;
    }

    /**
     * {@inheritDoc}
     */
    public function isFormSubmit() {
        return $this->isAutoSearch();
    }

    /**
     * {@inheritDoc}
     */
    public function processGet($request) {
        ZMCrumbtrail::instance()->addCrumb(ZMToolbox::instance()->utils->getTitle(null, false));

        $criteria = $this->getFormBean();

        if (!ZMLangUtils::isEmpty($criteria->getKeywords()) && $this->autoSearch_) {
            $resultList = ZMLoader::make('ResultList');
            //TODO: filter??
            foreach (explode(',', ZMSettings::get('resultListProductSorter')) as $sorter) {
                $resultList->addSorter(ZMLoader::make($sorter));
            }

            $source = ZMLoader::make('SearchResultSource', $criteria);
            $resultList->setResultSource($source);
            $resultList->setPageNumber($request->getPageIndex());
            return $this->findView('results', array('zm_resultList' => $resultList));
        }

        return $this->findView();
    }

}

?>