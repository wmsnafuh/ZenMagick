<?php
/*
 * ZenMagick Core - Another PHP framework.
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
 * Handle access control and security mappings.
 *
 * <p>This manager class provides abstract access to access control methods. The actual processing is delegated to
 * implementations of the <code>ZMSacsHandler</code> interface.
 *
 * <p>Access control mappings define the level of authentication required for resources.
 * Resources in this context are controller or page requests.</p>
 *
 * <p>Controller/resources marked as secure will result in redirects using SSL (if configured), if
 * non secure HTTP is used to access them.</p>
 *
 * <p>Default handler (class names) may be set as a comma separated list with the setting <em>zenmagick.mvc.sacs.handler</em>.</p>
 *
 * <p>To add handler dynamically the preferred way is to use <code>addHandler()</code> as the default handler list is only evaluated when
 * the manager instance is created.</p>
 *
 * @author DerManoMann
 * @package org.zenmagick.mvc
 * @version $Id$
 */
class ZMSacsManager extends ZMObject {
    private $mappings_;
    private $handler_;


    /**
     * Create new instance.
     */
    function __construct() {
        parent::__construct();
        $this->mappings_ = array();
        $this->handler_ = array();
        foreach (explode(',', ZMSettings::get('zenmagick.mvc.sacs.handler')) as $class) {
            if (null != ($handler = ZMBeanUtils::getBean($class))) {
                $this->handler_[$handler->getName()] = $handler;
            }
        }
    }

    /**
     * Destruct instance.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Get instance.
     */
    public static function instance() {
        return ZMObject::singleton('SacsManager');
    }


    /**
     * Add a <code>ZMSacsHandler</code>.
     *
     * @param ZMSacsHandler handler The new handler.
     */
    public function addHandler($handler) {
        $this->hander_[] = $handler;
    }

    /**
     * Set a mapping.
     *
     * <p>The <em>authentication</code> value depends on the acutal handler implementation and is passed through <em>as-is</em>.</p>
     *
     * @param string requestId The request id [ie. the request name as set via the <code>ZM_PAGE_KEY</code> URL parameter].
     * @param mixed authentication The level of authentication required; default is <code>null</code>.
     * @param boolean secure Mark resource as secure; default is <code>true</code>.
     * @param array args Optional additional parameter map; default is an empty array.
     */
    public function setMapping($requestId, $authentication=null, $secure=true, $args=array()) {
        if (null == $requestId) {
            throw new ZMException("invalid sacs mapping (requestId missing)");
        }
        $this->mappings_[$requestId] = array_merge($args, array('level' => $authentication, 'secure' => $secure));
    }

    /**
     * Authorize the current request.
     *
     * <p>If no configured handler is found, all requests will be authorized.</p>
     *
     * @param ZMRequest request The current request.
     * @param string requestId The request id to authorize.
     * @param mixed credientials User information; typically a map with username and password.
     * @return boolean <code>true</code> if authorization was sucessful.
     */
    public function authorize($request, $requestId, $credentials) {
        foreach ($this->handler_ as $handler) {
            if (null !== ($result = $handler->evaluate($requestId, $credentials, $this))) {
                if (false === $result) {
                    // not required level of authentication
                    $session = $request->getSession();
                    //TODO: toolbox::net, request->redirect()?
                    // secure flag: leave to net() to lookup via ZMSacsManager if configured, but leave as default parameter to allow override
                    if (!$session->isValid()) {
                        // no valid session
                    // XXX: add setting: mvc.session.invalid.requestId = 'cookies'
                        $request->redirect(ZMToolbox::instance()->net->url(ZMSettings::get('invalidSessionPage'), '', false, false));
                    }
                    $session->markRequestAsLoginFollowUp();
                    // XXX: add setting: mvc.login.requestId = 'login'
                    $request->redirect(ZMToolbox::instance()->net->url('login', '', true, false));
                }
                break;
            }
        }

        return true;
    }

    /**
     * Ensure the page is accessed using proper security.
     *
     * <p>If a page is requested using HTTP and the page is mapped as <em>secure</em>, a
     * redirect using SSL will be performed.</p>
     *
     * @param string requestId The request id.
     */
    public function ensureAccessMethod($request) {
        $secure = $this->getMappingValue($requestId, 'level', false);
        if ($secure && !ZMRequest::instance()->isSecure() && ZMSettings::get('isEnableSSL') && ZMSettings::get('isEnforceSSL')) {
            ZMRequest::instance()->redirect(ZMToolbox::instance()->net->url(null, null, true, false));
        }
    }

    /**
     * Get mapping value.
     *
     * @param string requestId The request id.
     * @param string key The mapping key.
     * @param mixed default The mapping key.
     * @return mixed The value or the provided default value; default is <code>null</code>.
     */
    public function getMappingValue($requestId, $key, $default=null) {
        if (null == $requestId) {
            ZMLogging::instance()->log('null is not a valid requestId', ZMLogging::DEBUG);
            return null;
        }

        if (!isset($this->mappings_[$requestId])) {
            return $default;
        }

        return $this->mappings_[$requestId][$key];
    }

    /**
     * Check if a request to the given page [name] is required to be secure.
     *
     * @param string requestId The request id.
     * @return boolean <code>true</code> if a secure conenction is required.
     */
    public function requiresSecurity($requestId) {
        return $this->getMappingValue($requestId, 'level', false);
    }

}

?>