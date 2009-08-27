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
 *
 * $Id$
 */
?>

<?php $manufacturer = $zm_product->getManufacturer() ?>
<h2><?php $html->encode(null != $manufacturer ? $manufacturer->getName() : '') ?> <?php $html->encode($zm_product->getName()) ?></h2>

<?php $form->addProduct($zm_product->getId()) ?>
  <?php $imageInfo = $zm_product->getImageInfo() ?>
  <div>
      <?php if ($imageInfo->hasLargeImage()) { ?>
          <a href="<?php $net->absolute($imageInfo->getLargeImage()) ?>" onclick="productPopup(event, this); return false;"><?php $html->image($imageInfo, ZMProducts::IMAGE_MEDIUM) ?></a>
      <?php } else { ?>
          <?php $html->image($imageInfo, ZMProducts::IMAGE_MEDIUM) ?>
      <?php } ?>
      <div id="desc"><?php $html->encode($zm_product->getDescription()) ?></div>
      <?php if (null != $manufacturer) { ?>
        <?php zm_l10n("Producer") ?>: <?php $html->encode($manufacturer->getName()); ?><br />
      <?php } ?>
      <p id="price"><?php $html->encode($zm_product->getModel()) ?>: <?php $macro->productPrice($zm_product) ?></p>
  </div>

  <?php $attributes = $macro->productAttributes($zm_product); ?>
  <?php foreach ($attributes as $attribute) { ?>
      <fieldset>
          <legend><?php $html->encode($attribute['name']) ?></legend>
          <?php foreach ($attribute['html'] as $option) { ?>
            <p><?php echo $option ?></p>
          <?php } ?>
      </fieldset>
  <?php } ?>

  <?php 
      // set up services
      $zm_music = ZMLoader::make("Music");
      $zm_mediaManager = ZMLoader::make("MediaManager");
      // get artist information
      $artist = $zm_music->getArtistForProductId($zm_product->getId());
      // get musc collections for this product/artist
      $collections = $zm_mediaManager->getMediaCollectionsForProductId($zm_product->getId());
  ?>
  <fieldset>
      <legend><?php zm_l10n("Additional Music Info") ?></legend>
      <p><?php zm_l10n("Genre:") ?> <?php echo $artist->getGenre() ?></p>
      <?php if ($artist->hasUrl()) { ?>
          <p>
              <?php zm_l10n("Homepage:") ?>
              <a href="<?php zm_redirect_href('url', $artist->getUrl()) ?>"<?php zm_href_target() ?>><?php echo $artist->getName() ?></a>
          </p>
      <?php } ?>
  </fieldset>

  <?php if (0 < count($collections)) { ?>
      <fieldset>
          <legend><?php zm_l10n("Media Collections") ?></legend>
          <?php foreach($collections as $collection) { ?>
              <div class="mcol">
                  <h4><?php echo $collection->getName() ?></h4>
                  <ul>
                      <?php foreach($collection->getItems() as $media) { $type = $media->getType(); ?>
                          <li><a href="<?php zm_media_href($media->getFilename()) ?>"><?php echo $media->getFilename() ?></a> (<?php echo $type->getName() ?>)</li>
                      <?php } ?>
                  </ul>
              </div>
          <?php } ?>
      </fieldset>
  <?php } ?>

  <fieldset>
      <legend><?php zm_l10n("Shopping Options") ?></legend>
      <?php $minMsg = ""; if (1 < $zm_product->getMinOrderQty()) { $minMsg = zm_l10n_get(" (Order minimum: %s)", $zm_product->getMinOrderQty()); } ?>
      <label for="cart_quantity"><?php zm_l10n("Quantity") ?><?php echo $minMsg; ?></label>
      <input type="text" id="cart_quantity" name="cart_quantity" value="1" maxlength="6" size="4" />
      <input type="submit" class="btn" value="<?php zm_l10n("Add to cart") ?>" />
  </fieldset>

  <?php $addImgList = $zm_product->getAdditionalImages(); ?>
  <?php if (0 < count($addImgList)) { ?>
      <fieldset>
          <legend><?php zm_l10n("Additional Images") ?></legend>
          <?php foreach ($addImgList as $addImg) { ?>
              <?php if ($addImg->hasLargeImage()) { ?>
                  <a href="<?php zm_absolute_href($addImg->getLargeImage()) ?>" onclick="productPopup(event, this); return false;"><img src="<?php zm_absolute_href($addImg->getDefaultImage()) ?>" alt="" title="" /></a>
              <?php } else { ?>
                  <img src="<?php zm_absolute_href($addImg->getDefaultImage()) ?>" alt="" title="" />
              <?php } ?>
          <?php } ?>
      </fieldset>
  <?php } ?>
  <?php if ($zm_product->hasReviews() || $zm_product->getTypeSetting('reviews') || $zm_product->getTypeSetting('tell_a_friend')) { ?>
      <fieldset>
          <legend><?php zm_l10n("Other Options") ?></legend>
          <?php if ($zm_product->hasReviews()) { ?>
              <a class="btn" href="<?php $net->url(FILENAME_PRODUCT_REVIEWS, '') ?>"><?php zm_l10n("Read Reviews") ?></a>
          <?php } ?>
          <?php if ($zm_product->getTypeSetting('reviews')) { ?>
              <a class="btn" href="<?php $net->url(FILENAME_PRODUCT_REVIEWS_WRITE, "products_id=".$zm_product->getId()) ?>"><?php zm_l10n("Write a Review") ?></a>
          <?php } ?>
          <?php if ($zm_product->getTypeSetting('tell_a_friend')) { ?>
              <a class="btn" href="<?php $net->url(FILENAME_TELL_A_FRIEND, "products_id=".$zm_product->getId()) ?>"><?php zm_l10n("Tell a friend about this product") ?></a>
          <?php } ?>
      </fieldset>
  <?php } ?>
</form>