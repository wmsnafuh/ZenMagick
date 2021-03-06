<?php
/*
 * ZenMagick - Smart e-commerce
 * Copyright (C) 2006-2011 zenmagick.org
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
   * Load WP API.
   *
   * @see http://www.ardamis.com/2006/07/10/wordpress-googlebot-404-error/
   */
  // use API
  define('WP_USE_THEMES', false);

  if (null != ($plugin = Runtime::getContainer()->get('pluginService')->getPluginForId('wordpress'))) {
      if (file_exists($plugin->get('wordpressDir').'wp-config.php')) { // @todo error message
          require_once $plugin->get('wordpressDir').'wp-config.php';
          $wp->init();
          $wp->parse_request();
          $wp->query_posts();
          $wp->register_globals();
      }
  }
