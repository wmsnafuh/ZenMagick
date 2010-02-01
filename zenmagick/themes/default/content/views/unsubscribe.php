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
 * $Id$
 */
?>

<p><?php zm_l10n('We are sorry to hear that you wish to unsubscribe from our newsletter. If you have concerns about your privacy, 
please see our <a href="%s">privacy policy</a>.', $net->staticPage(FILENAME_PRIVACY)) ?></p>

<p><?php zm_l10n('Subscribers to our newsletter are kept notified of new products, price reductions, and site news.') ?></p>

<?php if (ZMSettings::get('isAllowAnonymousUnsubscribe')) { ?>
    <p><?php zm_l10n('If you still do not wish to receive your newsletter, please click the button below. 
          You will be taken to your account-preferences page, where you may edit your subscriptions. You may be prompted to log in first.') ?></p>

    <?php echo $form->open(FILENAME_UNSUBSCRIBE, "", true, array('id'=>'unsubscribe')) ?>
      <fieldset>
        <legend><?php zm_l10n("Newsletter Unsubscribe") ?></legend>
        <div>
          <label for="email_address"><?php zm_l10n("E-Mail Address") ?></label>
          <input type="text" id="email_address" name="email_address" <?php echo $form->fieldLength(TABLE_CUSTOMERS, 'customers_email_address') ?> /> 
        </div>
      </fieldset>
      <div class="btn"><input type="submit" class="btn" value="<?php zm_l10n("Unsubscribe") ?>" /></div>
    </form>
<?php } else { ?>
      <div class="btn"><a href="<?php echo $net->url(FILENAME_ACCOUNT_NEWSLETTERS, '', true) ?>" class="btn"><?php zm_l10n("Unsubscribe") ?></a></div>
<?php } ?>
