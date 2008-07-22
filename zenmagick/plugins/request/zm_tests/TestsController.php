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
 * Unit testing controller.
 *
 * @author DerManoMann
 * @package org.zenmagick.plugins.zm_tests
 * @version $Id$
 */
class TestsController extends ZMController {
    private $plugin;

    /**
     * Create new instance.
     */
    function __construct() {
    global $zm_tests;

        $this->plugin = $zm_tests;
        parent::__construct();
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Process a HTTP GET request.
     * 
     * @return ZMView A <code>ZMView</code> that handles presentation or <code>null</code>
     * if the controller generates the contents itself.
     */
    public function processGet() {
        // show test view only
        ZMRuntime::getTheme()->getThemeInfo()->setLayout('tests', null);

        $testsLoader = ZMLoader::make("Loader");
        $testsLoader->addPath($this->plugin->getPluginDir().'tests/');
        // test data  is lower case
        $testsLoader->loadStatic();

        ZMLoader::instance()->setParent($testsLoader);

        $tests = array();
        foreach ($testsLoader->getClassPath() as $class => $file) {
            if (ZMTools::startsWith($class, 'Test')) {
                $tests[] = $class;
            }
        }

        $this->exportGlobal('all_tests', $tests);

        $run = ZMRequest::getParameter('tests', array());
        if (0 < count($run)) {
            // prepare selected tests
            $suite = new TestSuite('ZenMagick Tests');
            foreach ($run as $name) {
                $testCase = ZMLoader::make($name);
                if ($testCase instanceof UnitTestCase) {
                    $suite->addTestCase($testCase);
                }
            }
            $this->exportGlobal('test_suite', $suite);
        }

        return $this->findView('tests');
    }

}

?>
