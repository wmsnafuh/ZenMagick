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
 * A proxy module to allow ZenMagick plugins to act as order total.
 *
 * <p><strong>Currently credit_class functionality is not supported.</strong></p>
 *
 * <p>This proxy class uses the Plugin service to lookup existing order total modules.</p>
 *
 * <p>While the proxy shows up as a single module in the admin view, it might handle multiple
 * plugins and also multiple order total lines on any order.</p>
 *
 * <p>Disabling this module will disable <strong>*all*</strong> ZenMagick order total plugins.
 * To disable individual order total plugins the Plugin Manager should be used.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick
 * @version $Id$
 */
class ot_zenmagick {
    var $title;
    var $code;
    var $sort_order;
    var $credit_class;
    var $output;
    private $plugins_;
    private $installed_;


    /**
     * Create proxy instance.
     */
    function __construct() {
        $this->title = zm_l10n_get('ZenMagick Order Totals');
        $this->code = 'ot_zenmagick';
        $this->sort_order = defined('MODULE_ORDER_TOTAL_ZENMAGICK_SORT_ORDER') ? MODULE_ORDER_TOTAL_ZENMAGICK_SORT_ORDER : 0;
        // to start with
        $this->credit_class = false;
        $this->output = array();

        // find all order total plugins
        $this->plugins_ = array();
        foreach (ZMPlugins::getAllPlugins() as $type => $plugins) {
            foreach ($plugins as $plugin) {
                if ($plugin instanceof ZMOrderTotalPlugin) {
                    $this->plugins_[$plugin->getId()] = $plugin;
                }
            }
        }

        // add ative plugin ids to the description
        if (0 < count($this->plugins_)) {
            $ids = ' (' . implode(',', array_keys($this->plugins_)) . ')';
            $this->title .= $ids;
        }

        $this->installed_ = null;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
    }


    /**
     * Generate order total line(s).
     *
     * <p>Each order total line must contain the following elements:</p>
     * <ul>
     *  <li>title - The order total text.</li>
     *  <li>text - The order total value as string.</li>
     *  <li>value - The actual value as float.</li>
     * </ul>
     *
     * @return array A list of order total line info (which is of type <code>array</code> too).
     */
    public function process() {
    global $order;

        $cart = ZMRequest::instance()->getShoppingCart();
        $detailsList = array();
        foreach ($this->plugins_ as $plugin) {
            if (null != ($details = $plugin->calculate($cart))) {
                if (!is_array($details)) {
                    $details = array($details);
                }
                // allows empty array too
                foreach ($details as $detail) {
                    $detailsList[] = $detail;
                }
            }
        }

        // now convert to $output style
        $toolbox = ZMToolbox::instance();
        foreach ($detailsList as $detail) {
            $order->info['total'] += $detail->getAmount();
            $order->info['subtotal'] += $detail->getSubtotal();
            $order->info['tax'] += $detail->getTax();
            $this->output[] = array(
                'title' => $detail->getTitle(),
                'text' => $toolbox->utils->formatMoney($detail->getAmount(), true, false),
                'value' => $detail->getAmount()
            );
        }
    }

    /**
     * Check if this module is active.
     *
     * @return boolean <code>true</code> if active.
     */
    public function check() {
        if (null === $this->installed_) {
          $sql = "SELECT configuration_value FROM " . TABLE_CONFIGURATION . "
                  WHERE configuration_key = :key";
          $args = array('key' => 'MODULE_ORDER_TOTAL_ZENMAGICK_STATUS');
          $result = Runtime::getDatabase()->querySingle($sql, $args, TABLE_CONFIGURATION);
          $this->installed_ = null != $result;
        }

        return $this->installed_;
    }

    /**
     * Return configuration keys.
     *
     * @return array Empty list.
     */
    public function keys() {
        return array('MODULE_ORDER_TOTAL_ZENMAGICK_STATUS', 'MODULE_ORDER_TOTAL_ZENMAGICK_SORT_ORDER');
    }

    /**
     * Install module.
     */
    public function install() {
        ZMConfig::instance()->createConfigValue('Plugin Status', 'MODULE_ORDER_TOTAL_ZENMAGICK_STATUS', true, ZENMAGICK_PLUGIN_GROUP_ID, 'Enable/disable this plugin.', 0, "zen_cfg_select_drop_down(array(array('id'=>'1', 'text'=>'Enabled'), array('id'=>'0', 'text'=>'Disabled')), ");
        ZMConfig::instance()->createConfigValue('Plugin sort order', 'MODULE_ORDER_TOTAL_ZENMAGICK_SORT_ORDER', 110, ZENMAGICK_PLUGIN_GROUP_ID, 'Controls the execution order of plugins.', 1);
        return;
    }

    /**
     * Remove module.
     */
    public function remove() {
        ZMConfig::instance()->removeConfigValue('MODULE_ORDER_TOTAL_ZENMAGICK_STATUS');
        ZMConfig::instance()->removeConfigValue('MODULE_ORDER_TOTAL_ZENMAGICK_SORT_ORDER');
    }

}

?>
