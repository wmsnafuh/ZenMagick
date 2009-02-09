<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006-2008 ZenMagick
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
 * A theme.
 *
 * @author DerManoMann
 * @package org.zenmagick.rp.uip.themes
 * @version $Id$
 */
class ZMTheme extends ZMObject {
    private $themeId_;
    private $themeInfo_;


    /**
     * Create new instance.
     *
     * @params string themeId The theme id/name.
     */
    function __construct($themeId) {
        parent::__construct();
        $this->themeId_ = $themeId;
        $this->themeInfo_ = null;
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get this themes id.
     *
     * @return string The theme id.
     */
    public function getThemeId() { return $this->themeId_; }

    /**
     * Check if a layout is configured for the given page.
     *
     * @param string page The page name.
     * @return boolean <code>true</code> if a layout file is configured for the given page.
     */
    public function hasLayout($page) {
        // layouts reside in the content directory
        return file_exists($this->getContentDir().$page.ZMSettings::get('templateSuffix'));
    }

    /**
     * Resolve a theme relative URI.
     *
     * <p>The given <code>uri</code> is assumed to be relative to the themes <em>content</em> folder.</p>
     *
     * <p>If the file is not found and <code>isEnableThemeDefaults</code> is set to <code>true</code>,
     * the method will try to resolve the name in the default theme.</p>
     *
     * @param string uri The relative URI.
     * @param boolean echo If <code>true</code>, the URL will be echo'ed as well as returned.
     * @return string An absolute URL.
     */
    public function themeURL($uri, $echo=ZM_ECHO_DEFAULT) {
        $url = ZMRuntime::getThemesPathPrefix().$this->themeId_."/".'content/'.$uri;
        if (ZMSettings::get('isEnableThemeDefaults') && !file_exists($this->getContentDir().$uri)) {
            if (file_exists(ZMRuntime::getThemesDir().ZM_DEFAULT_THEME.'/'.'content/'.$uri)) {
                $url = ZMRuntime::getThemesPathPrefix().ZM_DEFAULT_THEME."/".'content/'.$uri;
            }
        }

        $url = ZMToolbox::instance()->html->encode($url, false);

        if ($echo) echo $url;
        return $url;
    }


    /**
     * Return the full filename for the themes root directory.
     *
     * @return string The themes root directory.
     */
    public function getRootDir() {
        return ZMRuntime::getThemesDir() . $this->themeId_ . '/';
    }

    /**
     * Return the path of the extra directory.
     *
     * @return string A full filename denoting the themes extra directory.
     */
    public function getExtraDir() {
        return $this->getRootDir() . 'extra/';
    }

    /**
     * Return the path of the boxes directory.
     *
     * @return string A full filename denoting the themes boxes directory.
     */
    public function getBoxesDir() {
        return $this->getRootDir() . 'content/boxes/';
    }

    /**
     * Return the path of the content directory.
     *
     * @return string A full filename denoting the themes content directory.
     */
    public function getContentDir() {
        return $this->getRootDir() . 'content/';
    }

    /**
     * Return the path of the views directory.
     *
     * @return string A full filename denoting the themes views directory.
     */
    public function getViewsDir() {
        $themeInfo = $this->getThemeInfo();
        return $themeInfo->getViewsDir();
    }

    /**
     * Return the path of the lang directory.
     *
     * @return string A full filename denoting the themes lang directory.
     */
    public function getLangDir() {
        return $this->getRootDir() . 'lang/';
    }

    /**
     * Resolve a theme relative filename into a full path.
     *
     * <p>If the file is not found and <code>isEnableThemeDefaults</code> is set to <code>true</code>,
     * the method will try to resolve the name in the default theme.</p>
     *
     * @param string name A theme relative filename.
     * @param string baseDir An optional base directory; default is <code>content/</code>
     * @param boolean echo If <code>true</code>, the URL will be echo'ed as well as returned.
     * @return string A fully qualified filename.
     */
    public function themeFile($name, $baseDir='content/', $echo=false) {
        $file = $this->getRootDir().$baseDir.$name;
        if (ZMSettings::get('isEnableThemeDefaults') && !file_exists($file)) {
            // check for default
            $dfile = ZMRuntime::getThemesDir().ZM_DEFAULT_THEME.'/'.$baseDir.$name;
            if (file_exists($dfile)) {
                $file = $dfile;
            }
        }

	    if ($echo) echo $file;
        return $file;
    }

    /**
     * Check if the given theme relative file exists.
     *
     * @param string name A theme relative filename.
     * @param string baseDir An optional base directory; default is <code>content/</code>
     * @return boolean <code>true</code> if the file exists, <code>false</code> if not.
     */
    public function themeFileExists($name, $baseDir='content/') {
		    return file_exists($this->themeFile($name, $baseDir));
    }

    /**
     * Get a list of available static pages.
     *
     * @param boolean includeDefaults If set to <code>true</code>, default pages will be included; default is <code>false</code>.
     * @param int languageId Optional language id; default is <code>null</code> for current language.
     * @return array List of available static page names.
     */
    public function getStaticPageList($includeDefaults=false, $languageId=null) {
        if (null == $languageId) {
            $session = ZMRequest::getSession();
            $language = $session->getLanguage();
        } else {
            $language = ZMLanguages::instance()->getLanguageForId($languageId);
        }
        $languageDir = $language->getDirectory();
        $path = $this->getLangDir().$languageDir."/".'static/';

        $pages = array();
        if (is_dir($path)) {
            $handle = @opendir($path);
            while (false !== ($file = readdir($handle))) { 
                if (!ZMTools::endsWith($file, '.php')) {
                    continue;
                }
                $page = str_replace('.php', '', $file);
                $pages[$page] = $page;
            }
            @closedir($handle);
        }

        if ($includeDefaults) {
            $path = ZMRuntime::getThemesDir().ZM_DEFAULT_THEME.'/'.'lang/'.$languageDir."/".'static/';
            if (is_dir($path)) {
                $handle = @opendir($path);
                while (false !== ($file = readdir($handle))) { 
                    if (!ZMTools::endsWith($file, '.php')) {
                        continue;
                    }
                    $page = str_replace('.php', '', $file);
                    $pages[$page] = $page;
                }
                @closedir($handle);
            }
        }
        return $pages;
    }

    /**
     * Write the content of a static (define) page.
     *
     * @param string page The page name.
     * @param string contents The contents.
     * @param int languageId Optional language id; default is <code>null</code> for current language.
     * @return boolean The status.
     */
    public function saveStaticPageContent($page, $contents, $languageId=null) {
        if (null == $languageId) {
            $session = ZMRequest::getSession();
            $language = $session->getLanguage();
        } else {
            $language = ZMLanguages::instance()->getLanguageForId($languageId);
        }
        $languageDir = $language->getDirectory();
        $path = $this->getLangDir().$languageDir."/".'static/';
        if (!file_exists($path)) {
            ZMTools::mkdir($path);
        }
        $filename = $path.$page.'.php';

        if (file_exists($filename)) {
            if (file_exists($filename.'.bak')) {
                @unlink($filename.'.bak');
            }
            @rename($filename, $filename.'.bak');
        }
        $handle = fopen($filename, 'w');
        fwrite($handle, $contents, strlen($contents));
        fclose($handle);
        ZMTools::setFilePerms($filename);

        return file_exists($filename);
    }

    /**
     * Get the content of a static (define) page.
     *
     * <p>If the file is not found and <code>isEnableThemeDefaults</code> is set to <code>true</code>,
     * the method will try to resolve the name in the default theme.</p>
     *
     * @param string page The page name.
     * @param int languageId Optional language id; default is <code>null</code> for current language.
     * @param boolean echo If <code>true</code>, the URL will be echo'ed as well as returned.
     * @return string The content or <code>null</code>.
     */
    public function staticPageContent($page, $languageId=null, $echo=ZM_ECHO_DEFAULT) {
        if (!ZMSettings::get('isZMDefinePages')) {
            return $this->zcStaticPageContent($page, $languageId, $echo);
        }

        if (null === $languageId) {
            $session = ZMRequest::getSession();
            $language = $session->getLanguage();
        } else {
            $language = ZMLanguages::instance()->getLanguageForId($languageId);
        }
        $languageDir = $language->getDirectory();
        $path = $this->getLangDir().$languageDir."/".'static/';

        $filename = $path.$page.'.php';
        if (!file_exists($filename) && ZMSettings::get('isEnableThemeDefaults')) {
            $filename = ZMRuntime::getThemesDir().ZM_DEFAULT_THEME.'/'.'lang/'.$languageDir."/".'static/'.$page.'.php';
        }

        $contents = null;
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
        }

        if ($echo && null !== $contents) echo $contents;
        return $contents;
    }

    /**
     * Get the content of a static (define) page using the zen-cart location.
     *
     * <p>If the file is not found and <code>isEnableThemeDefaults</code> is set to <code>true</code>,
     * the method will try to resolve the name in the default theme.</p>
     *
     * @param string page The page name.
     * @param int languageId Optional language id; default is <code>null</code> for current language.
     * @param boolean echo If <code>true</code>, the URL will be echo'ed as well as returned.
     * @return string The content or <code>null</code>.
     */
    public function zcStaticPageContent($page, $languageId=null, $echo=ZM_ECHO_DEFAULT) {
        if (null == $languageId) {
            $session = ZMRequest::getSession();
            $language = $session->getLanguage();
        } else {
            $language = ZMLanguages::instance()->getLanguageForId($languageId);
        }
        $languageDir = $language->getDirectory();
        $filename = DIR_WS_LANGUAGES . $languageDir . '/html_includes/'.ZMRuntime::getThemeId().'/define_' . $page . '.php';
        if (!file_exists($filename) && ZMSettings::get('isEnableThemeDefaults')) {
            $filename = DIR_WS_LANGUAGES . $languageDir . '/html_includes/define_' . $page . '.php';
        }

        $contents = null;
        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
        }

        if ($echo && null !== $contents) echo $contents;
        return $contents;
    }

    /**
     * Load and <code>eval</code> a theme file.
     *
     * <p>This allows to use <em>PHP</em> code in, for example, JavaScript files. One side-effect is
     * that the evaluated content is inline'ed rather than loaded from a separate file. That
     * means it's more usefule for small snippets rather than large files</p>
     *
     * @param string file The filename.
     * @return string The eval'ed content.
     */
    public function themeFileContents($file) {
        return eval('?>'.file_get_contents($this->themeFile($file)));
    }

    /**
     * Get the <code>ZMThemeInfo</code> for this theme.
     *
     * @return ZMThemeInfo A <code>ZMThemeInfo</code> instance.
     */
    public function getThemeInfo() {
        if (null == $this->themeInfo_) {
            $this->themeInfo_ = ZMThemes::instance()->getThemeInfoForId($this->themeId_);
        }
        if (null == $this->themeInfo_) {
            throw ZMLoader::make('ZMException', 'could not instantiate theme info class for theme: '.$this->themeId_);
        }

        return $this->themeInfo_;
    }

    /**
     * Load locale settings (l10n/i18n).
     *
     * <p>NOTE: This is only going to load mappings. However, since i18n
     * settings need to be set using <code>define(..)</code>, this is done
     * in a separate function, once loading (and theme switching) is over.</p>
     *
     * @param ZMLanguage language The language.
     */
    public function loadLocale($language) {
        if (null === $language) {
            // this may happen if the i18n patch hasn't been updated
            $language = ZMRuntime::getDefaultLanguage();
        }
        $path = $this->getLangDir().$language->getDirectory()."/";
        $l10n = $path . "l10n.php";
        if (file_exists($l10n)) {
            require_once($l10n);
        }
        $i18n = $path . "i18n.php";
        if (file_exists($i18n)) {
            require_once($i18n);
        }
    }

}

?>
