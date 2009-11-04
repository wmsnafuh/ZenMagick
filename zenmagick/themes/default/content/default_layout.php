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
<?php

    // allow for custom layout settings without having to copy the whole file every time...
    $pageLayout = "layout/".$request->getRequestId().".php";
    if ($zm_theme->themeFileExists($pageLayout)) {
        echo $this->fetch($pageLayout);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <title><?php $metaTags->getTitle() ?></title>
    <base href="<?php echo $request->getPageBase() ?>" />
    <meta http-equiv="content-type" content="text/html; charset=<?php echo zm_i18n('HTML_CHARSET') ?>" />
    <meta name="generator" content="ZenMagick <?php echo ZMSettings::get('zenmagick.version') ?>" />
    <meta name="keywords" content="<?php $metaTags->getKeywords()?>" />
    <meta name="description" content="<?php $metaTags->getDescription()?>" />
    <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php $zm_theme->themeURL("site.css") ?>" />
    <!--[if IE]><link rel="stylesheet" type="text/css" media="screen,projection" href="<?php $zm_theme->themeURL("ie.css") ?>"  /><![endif]-->
    <?php $utils->jsBottom('common.js') ?>
    <?php /* give other themes the chance to add to the default CSS without having to copy everything */ ?>
    <?php if ($zm_theme->themeFileExists("theme.css")) { ?>
      <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php $zm_theme->themeURL("theme.css") ?>" />
    <?php } ?>
    <?php $pageCSS = "css/".$request->getRequestId().".css"; ?>
    <?php /* page specific CSS */ ?>
    <?php if ($zm_theme->themeFileExists($pageCSS)) { ?>
      <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php $zm_theme->themeURL($pageCSS) ?>" />
    <?php } ?>
    <?php if (!ZMTemplateManager::instance()->isLeftColEnabled() || !ZMTemplateManager::instance()->isRightColEnabled()) { ?>
      <style type="text/css" media="screen,projection">
        <?php if (!ZMTemplateManager::instance()->isLeftColEnabled()) { ?>
          #content {margin-left:10px;}
        <?php } ?>
        <?php if (!ZMTemplateManager::instance()->isRightColEnabled()) { ?>
          body div#content {margin-right:20px;}
        <?php } ?>
      </style>
    <?php } ?>
  </head>

  <body id="b_<?php echo $request->getRequestId() ?>"<?php $html->onload() ?>>
    <?php if (null != ($bannerBox = ZMBanners::instance()->getBannerForSet('header1'))) { ?>
        <div id="bannerOne"><?php $macro->showBanner($bannerBox); ?></div>
    <?php } ?>

    <div id="container">
      <?php echo $this->fetch('header.php') ?>
      <?php echo $this->fetch('menu.php') ?>

      <?php if (ZMTemplateManager::instance()->isLeftColEnabled()) { ?>
        <div id="leftcol">
          <?php foreach (ZMTemplateManager::instance()->getLeftColBoxNames() as $box) { ?>
              <?php echo $this->fetch('boxes/'.$box) ?>
          <?php } ?>
        </div>
      <?php } ?>

      <?php if (ZMTemplateManager::instance()->isRightColEnabled()) { ?>
        <div id="rightcol">
          <?php foreach (ZMTemplateManager::instance()->getRightColBoxNames() as $box) { ?>
              <?php echo $this->fetch('boxes/'.$box) ?>
          <?php } ?>
        </div>
      <?php } ?>

      <div id="content">
        <?php if ('index' != $request->getRequestId()) { /* this is the actual view, not neccessarily what is in the URL */ ?>
            <?php echo $macro->buildCrumbtrail($crumbtrail, " &gt; "); ?>
        <?php } ?>

        <?php if (null != ($bannerBox = ZMBanners::instance()->getBannerForSet('header3'))) { ?>
            <div id="bannerThree"><?php $macro->showBanner($bannerBox); ?></div>
        <?php } ?>

        <?php if (ZMMessages::instance()->hasMessages()) { ?>
            <ul id="messages">
            <?php foreach (ZMMessages::instance()->getMessages() as $message) { ?>
                <li class="<?php echo $message->getType() ?>"><?php echo $message->getText() ?></li>
            <?php } ?>
            </ul>
        <?php } ?>

        <?php echo $this->fetch($viewTemplate); ?>

        <?php if (null != ($bannerBox = ZMBanners::instance()->getBannerForSet('footer1'))) { ?>
            <div id="bannerFour"><?php $macro->showBanner($bannerBox); ?></div>
        <?php } ?>
      </div>

      <?php echo $this->fetch('footer.php') ?>
    </div>

    <?php if (null != ($bannerBox = ZMBanners::instance()->getBannerForSet('footer3'))) { ?>
        <div id="bannerSix"><?php $macro->showBanner($bannerBox); ?></div>
    <?php } ?>

  </body>
</html>
