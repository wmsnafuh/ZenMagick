<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006-2010 zenmagick.org
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
namespace zenmagick\base\settings;

use zenmagick\base\Runtime;
use zenmagick\base\ioc\loader\YamlLoader;


/**
 * A hierarchical settings repository.
 *
 * @author DerManoMann
 * @package org.zenmagick.base
 */
class Settings {

    /**
     * Lookup a given path.
     *
     * @param string path The path.
     * @return mixed Either an <code>array</code> with value, element name (the last path element) and parent container in it, or <code>null</code>.
     */
    private function lookup($path) {
        $current = Runtime::getContainer()->getParameterBag()->all();
        $base = &$current;
        foreach (explode('/', $path) as $element) {
            if (empty($element)) {
                throw new \RuntimeException(sprintf('invalid path: %s', $path));
            }
            if (!array_key_exists($element, $current)) {
                return null;
            }
            $last = &$current;
            $current = &$current[$element];
        }
        return array($current, $element, $last, $base);
    }

    /**
     * Check if a given path exists.
     *
     * @param string path The path.
     * @return boolean <code>true</code> if the path exists.
     */
    public function exists($path) {
        $path = str_replace('.', '/', $path);
        return null !== $this->lookup($path);
    }

    /**
     * Get the value for the given path.
     *
     * @param string path The path.
     * @param mixed default Optional default value to be returned if the path doesn't exist; default is <code>null</code>.
     * @return mixed The value, the default value or <code>null</code>.
     */
    public function get($path, $default=null) {
        $path = str_replace('.', '/', $path);
        if (null !== ($lookup = $this->lookup($path))) {
            return $lookup[0];
        }

        return $default;
    }

    /**
     * Set the value for the given path.
     *
     * @param string path The path.
     * @param mixed value The (new) value.
     * @return mixed The old value or <code>null</code>.
     */
    public function set($path, $value) {
        $path = str_replace('.', '/', $path);
        $token = explode('/', $path);
        if (null !== ($lookup = $this->lookup($path))) {
            $lookup[2][$lookup[1]] = $value;
            $base = $lookup[3];
            Runtime::getContainer()->getParameterBag()->set($token[0], $base[$token[0]]);
            return $lookup[0];
        }

        // create path
        $current = Runtime::getContainer()->getParameterBag()->all();
        $base = &$current;
        foreach ($token as $element) {
            if (empty($element)) {
                throw new \RuntimeException(sprintf('invalid path: %s', $path));
            }
            if (!array_key_exists($element, $current)) {
                $current[$element] = array();
            }
            $last = &$current;
            $current = &$current[$element];
        }
        $last[$element] = $value;
        Runtime::getContainer()->getParameterBag()->set($token[0], $base[$token[0]]);
        return null;
    }

    /**
     * Append to an existing value.
     *
     * @param string path The path to append to.
     * @param mixed value The value to append.
     * @param string delim Optional delimiter to be used if the value exists and is not empty; default is <em>','</em>.
     * @return mixed The old value or <code>null</code>.
     */
    public function append($path, $value, $delim=',') {
        $path = str_replace('.', '/', $path);
        if (null !== ($lookup = $this->lookup($path))) {
            $value = $lookup[2][$lookup[1]] . $delim.$value;
            $this->set($path, $value);
            return $lookup[0];
        }
        return $this->set($path, $value);
    }

    /**
     * Add to an existing value.
     *
     * <p>If the value doesn't exist, it will be created, if it exists and is not an array it will be converted to an array
     * with the current value as the first and the new value as the second element.</p>
     *
     * @param string path The path to append to.
     * @param mixed value The value to append.
     * @return mixed The old value or <code>null</code>.
     */
    public function add($path, $value) {
        $path = str_replace('.', '/', $path);
        if (null !== ($lookup = $this->lookup($path))) {
            if (!is_array($lookup[2][$lookup[1]])) {
                $lookup[2][$lookup[1]] = array($lookup[2][$lookup[1]]);
            }
            $lookup[2][$lookup[1]][] = $value;
            $this->set($path, $lookup[2][$lookup[1]]);
            return $lookup[0];
        }

        if (!is_array($value)) {
            $value = array($value);
        }
        return $this->set($path, $value);
    }

    /**
     * Get a map of all settings.
     *
     * @return array Map of all settings.
     */
    public function getAll() {
        return Runtime::getContainer()->getParameterBag()->all();
    }

    /**
     * Merge in a map of settings.
     *
     * @param mixed settings Either a map of settings or another <code>Settings</code> instance.
     */
    public function setAll($settings) {
        if ($settings instanceof Settings) {
            $settings = $settings->getAll();
        }
        if (is_array($settings) && 0 < count($settings)) {
            // TODO: make more efficient
            $loader = new YamlLoader(Runtime::getContainer());
            $loader->setParameters($settings);
        }
    }

}
