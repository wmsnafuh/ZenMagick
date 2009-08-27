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
 *
 * $Id$
 */
?>

<?php if (1 < $zm_resultList->getNumberOfPages()) { ?>
    <div class="rnav">
        <span class="pno"><?php zm_l10n("Page %s/%s", $zm_resultList->getPageNumber(), $zm_resultList->getNumberOfPages()) ?></span>
        <?php if ($zm_resultList->hasPreviousPage()) { ?>
            <a href="<?php $net->resultListBack($zm_resultList) ?>"><?php zm_l10n("Previous") ?></a>&nbsp;
        <?php } else { ?>
            <span class="nin"><?php zm_l10n("Previous") ?></span>&nbsp;
        <?php } ?>
        <?php if ($zm_resultList->hasNextPage()) { ?>
            <a href="<?php $net->resultListNext($zm_resultList) ?>"><?php zm_l10n("Next") ?></a>
        <?php } else { ?>
            <span class="nin"><?php zm_l10n("Next") ?></span>
        <?php } ?>
    </div>
<?php } ?>