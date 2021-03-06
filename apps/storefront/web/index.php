<?php
/*
 * ZenMagick - Another PHP framework.
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

  // app location relative to zenmagick installation
  define('ZM_APP_PATH', 'apps/'.basename(dirname(dirname(__FILE__))));

  // make zen-cart happy
  define('IS_ADMIN_FLAG', false);

  require realpath(dirname(__FILE__).'/../../../bootstrap.php');

  // more zen-cart config stuff we need
  Runtime::getSettings()->set('apps.store.storefront.sessions', true);

  require realpath(dirname(__FILE__).'/../../../mvc.php');
