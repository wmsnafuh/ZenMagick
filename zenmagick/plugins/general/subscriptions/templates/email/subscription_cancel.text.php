<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2009 zenmagick.org
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
 * $Id: email_subscription_cancel.text.php 2861 2010-01-26 02:58:32Z dermanomann $
 */
?>
orderId: <?php echo $order->getId(); ?>

<?php $schedules = $plugin->getSchedules(); ?>
Schedule: <?php echo $schedules[$order->getSchedule()]['name'] ?>

Next order date: <?php echo $locale->shortDate($order->getNextOrder()) ?>

Min last order date: <?php echo $locale->shortDate($plugin->getMinLastOrderDate($order->getId())) ?>
