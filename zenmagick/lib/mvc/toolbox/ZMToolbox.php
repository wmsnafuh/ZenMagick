<?php
/*
 * ZenMagick - Another PHP framework.
 * Copyright (C) 2006,2009 ZenMagick
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
 * Container for template related utilities.
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc.utils
 * @version $Id: ZMToolbox.php 2149 2009-04-13 22:59:14Z dermanomann $
 */
class ZMToolbox extends ZMObject {
    /** 
     * @var ZMToolboxHtml
     * @return ZMToolboxHtml
     */
    public $html;

    /** 
     * @var ZMToolboxNet
     * @return ZMToolboxNet
     */
    public $net;


    /** The tools. */
    private $tools_;

    

    /**
     * Create new instance.
     *
     * @param ZMRequest request The current request.
     */
    function __construct($request) {
        parent::__construct();
        $this->tools_ = $this->initTools($request);
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }


    /**
     * Get a map of all tools.
     *
     * @return array A map of all available tools.
     */
    public function getTools() {
        return $this->tools_;
    }

    /**
     * Init all tools.
     *
     * @param ZMRequest request The current request.
     * @return array Map of all tools.
     */
    protected function initTools($request) {
        // default tools
        $tools = array('html' => ZMLoader::make('ToolboxHtml'), 'net' => ZMLoader::make('ToolboxNet'));

        // custom tools: name:class,name:class
        foreach (explode(',', ZMSettings::get('zenmagick.mvc.toolbox.tools')) as $toolInfo) {
            $token = explode(':', $toolInfo);
            $tools[$token[0]] = ZMLoader::make($token[1]);
        }

        foreach ($tools as $name => $tool) {
            // set request where required
            if ($tool instanceof ZMToolboxTool) {
                $tool->setToolbox($this);
                $tool->setRequest($request);
            }

            // set member
            $this->$name = $tool;
        }

        return $tools;
    }

}

?>
