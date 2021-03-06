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
namespace zenmagick\http;

use zenmagick\base\Beans;
use zenmagick\base\Runtime;
use zenmagick\base\ZMException;
use zenmagick\base\events\Event;
use zenmagick\base\logging\Logging;

/**
 * ZenMagick MVC request dispatcher.
 *
 * @author DerManoMann <mano@zenmagick.org>
 */
class Dispatcher {

    /**
     * Dispatch a request.
     *
     * @param ZMRequest request The request to dispatch.
     */
    public function dispatch($request) {
        ob_start();

        // load saved messages
        Runtime::getContainer()->get('messageService')->loadMessages($request->getSession());

        Runtime::getEventDispatcher()->dispatch('dispatch_start', new Event(null, array('request' => $request)));
        $view = self::handleRequest($request);
        Runtime::getEventDispatcher()->dispatch('dispatch_done', new Event(null, array('request' => $request)));

        // allow plugins and event subscribers to filter/modify the final contents; corresponds with ob_start() in init.php
        $event = new Event(null, array('request' => $request, 'view' => $view, 'content' => ob_get_clean()));
        Runtime::getEventDispatcher()->dispatch('finalise_content', $event);

        echo $event->get('content');

        // if we get to here all messages have been displayed
        $messageService = Runtime::getContainer()->get('messageService');
        $messageService->clear();
        $messageService->saveMessages($request->getSession());

        // all done
        Runtime::getEventDispatcher()->dispatch('all_done', new Event(null, array('request' => $request, 'view' => $view, 'content' => $event->get('content'))));
        $request->closeSession();
    }

    /**
     * Handle a request.
     *
     * @param ZMRequest request The request to dispatch.
     * @return View The view or <code>null</code>.
     */
    public function handleRequest($request) {
        $controller = $request->getController();
        $view = null;

        try {
            // execute controller
            $view = $controller->process($request);
        } catch (Exception $e) {
            Runtime::getLogging()->dump($e, sprintf('controller::process failed: %s', $e->getMessage()), Logging::ERROR);
            $controller = Beans::getBean(Runtime::getSettings()->get('zenmagick.mvc.controller.default', 'ZMController'));
            $view = $controller->findView('error', array('exception' => $e));
            $request->setController($controller);
            $controller->initViewVars($view, $request);
        }

        // generate response
        if (null != $view) {
            try {
                if (null !== $view->getContentType()) {
                    $s = 'Content-Type: '.$view->getContentType();
                    if (null !== $view->getEncoding()) {
                        $s .= '; charset='.$view->getEncoding();
                    }
                    header($s);
                }
                Runtime::getEventDispatcher()->dispatch('view_start', new Event(null, array('request' => $request, 'view' => $view)));
                // generate response
                echo $view->generate($request);
                Runtime::getEventDispatcher()->dispatch('view_done', new Event(null, array('request' => $request, 'view' => $view)));
            } catch (ZMException $e) {
                Runtime::getLogging()->dump($e, sprintf('view::generate failed: %s', $e), Logging::ERROR);
            } catch (Exception $e) {
                Runtime::getLogging()->dump($e, sprintf('view::generate failed: %s', $e->getMessage()), Logging::ERROR);
                //TODO: what to do?
            }
        } else {
            Runtime::getLogging()->debug('null view, skipping $view->generate()');
        }

        return $view;
    }

}
