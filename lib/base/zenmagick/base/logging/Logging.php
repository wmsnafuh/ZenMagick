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
namespace zenmagick\base\logging;

use zenmagick\base\Beans;

/**
 * The ZenMagick logging service.
 *
 * <p>Logging manager. The actual logging is delegated to all configured logging handlers.</p>
 *
 * <p>Logging calls will be dispatched to all <code>LoggingHandler</code> classes registered via the setting <em>'zenmagick.base.logging.handler'</em>.</p>
 *
 * @author DerManoMann
 * @package zenmagick.base.logging
 */
class Logging {
    /** Log level: Disabled. */
    const NONE = 0;
    /** Log level: Error. */
    const ERROR = 1;
    /** Log level: Warning. */
    const WARN = 2;
    /** Log level: Info. */
    const INFO = 3;
    /** Log level: Debug. */
    const DEBUG = 4;
    /** Log level: Trace. */
    const TRACE = 5;
    /** Log level: ALL. */
    const ALL = 99999;

    /** String to log level lookup. */
    private static $LOG_LEVEL_LOOKUP = array(
        'NONE' => self::NONE, 
        'ERROR' => self::ERROR,
        'WARN' => self::WARN,
        'INFO' => self::INFO,
        'DEBUG' => self::DEBUG,
        'TRACE' => self::TRACE,
        'ALL' => self::ALL
    );

    /** Readable list of log level. */
    public static $LOG_LEVEL = array('NONE', 'ERROR', 'WARN', 'INFO', 'DEBUG', 'TRACE');

    private $enabled_;
    private $globalLogLevel_;
    private $handler_;
    private $handlerList_;
    private $ERR_MAP = array(
        1 => "Error",
        2 => "Warning",
        4 => "Parsing Error",
        8 => "Notice",
        16 => "Core Error",
        32 => "Core Warning",
        64 => "Compile Error",
        128 => "Compile Warning",
        256 => "User Error",
        512 => "User Warning",
        1024 => "User Notice",
        2048 => "Strict",
        4096 => "Recoverable Error",
        8192 => "Deprecated",
        16384 => "User Deprecated"
    );


    /**
     * Create new instance.
     */
    public function __construct() {
        $this->handler_ = null;
        $this->handlerList_ = array();
        $this->enabled_ = \ZMSettings::get('zenmagick.core.logging.enabled', false);
        $this->globalLogLevel_ = \ZMSettings::get('zenmagick.core.logging.level', 0);
        // allow string values
        if (array_key_exists($this->globalLogLevel_, self::$LOG_LEVEL_LOOKUP)) {
            $this->globalLogLevel_ = self::$LOG_LEVEL_LOOKUP[$this->globalLogLevel_];
        }
    }


    /**
     * Get all handler.
     *
     * @return array A list of handlers.
     */
    protected function getHandlers() {
        if ($this->handler_ != ($setting = \ZMSettings::get('zenmagick.base.logging.handler', '\zenmagick\base\logging\handler\DefaultLoggingHandler'))) {
            // populate freshly
            $this->handlerList_ = array();
            foreach (explode(',', $setting) as $def) {
                $def = trim($def);
                if (null != ($handler = Beans::getBean($def))) {
                    $this->handlerList_[$def] = $handler;
                }
            }
        }

        return $this->handlerList_;
    }

    /**
     * Log info.
     *
     * @param string msg The message to log.
     */
    public function info($msg) {
        $this->log($msg, self::INFO);
    }

    /**
     * Log warning.
     *
     * @param string msg The message to log.
     */
    public function warn($msg) {
        $this->log($msg, self::WARN);
    }

    /**
     * Log error.
     *
     * @param string msg The message to log.
     */
    public function error($msg) {
        $this->log($msg, self::ERROR);
    }

    /**
     * Log debug.
     *
     * @param string msg The message to log.
     */
    public function debug($msg) {
        $this->log($msg, self::DEBUG);
    }

    /**
     * Simple logging function.
     *
     * <p>Messages will either be appended to the webserver's error log or, if a custom
     * error handler is installed, trigger a <em>E_USER_NOTICE</em> error.</p>
     *
     * @param string msg The message to log.
     * @param int level Optional level; default: <code>INFO</code>.
     */
    public function log($msg, $level=self::INFO) {
      if ($this->enabled_) {
            foreach ($this->getHandlers() as $handler) {
                if ((null === ($customLevel = $handler->getLogLevel()) && $level <= $this->globalLogLevel_) || $level <= $customLevel) {
                    $handler->log($msg, $level);
                }
            }
        }
    }

    /**
     * Simple dump function.
     *
     * @param mixed obj The object to dump.
     * @param string msg An optional message.
     * @param int level Optional level; default: <code>DEBUG</code>.
     */
    public function dump($obj, $msg=null, $level=self::DEBUG) {
      if ($this->enabled_) {
            foreach ($this->getHandlers() as $handler) {
                if ((null === ($customLevel = $handler->getLogLevel()) && $level <= $this->globalLogLevel_) || $level <= $customLevel) {
                    $handler->dump($obj, $msg, $level);
                }
            }
        }
    }

    /**
     * Create a simple stack trace.
     *
     * @param mixed msg An optional string or array.
     * @param int level Optional level; default: <code>DEBUG</code>.
     */
    public function trace($msg=null, $level=self::DEBUG) {
      if ($this->enabled_) {
            foreach ($this->getHandlers() as $handler) {
                if ((null === ($customLevel = $handler->getLogLevel()) && $level <= $this->globalLogLevel_) || $level <= $customLevel) {
                    $handler->trace($msg, $level);
                }
            }
        }
    }

    /**
     * Get the error type.
     *
     * @param int errno The error number.
     * @return string An error type.
     */
    protected function getErrorType($errno) {
        return isset(self::$ERR_MAP[$errno]) ? self::$ERR_MAP[$errno] : "Unknown";
    }

    /**
     * Format error handler log line.
     *
     * @param int errno The error level.
     * @param string errstr The error message.
     * @param string errfile The source filename.
     * @param int errline The line number.
     * @param array errcontext All variables of scope when error triggered.
     * @return string A formatted log line.
     */
    protected function formatLog($errno, $errstr, $errfile, $errline, $errcontext) {
        $time = date("d M Y H:i:s");
        // Get the error names from the error number
        $errTypes = array (
        1 => "Error",
        2 => "Warning",
        4 => "Parsing Error",
        8 => "Notice",
        16 => "Core Error",
        32 => "Core Warning",
        64 => "Compile Error",
        128 => "Compile Warning",
        256 => "User Error",
        512 => "User Warning",
        1024 => "User Notice",
        2048 => "Strict",
        4096 => "Recoverable Error",
        8192 => "Deprecated",
        16384 => "User Deprecated",
        );

        if (isset($errTypes[$errno])) {
            $errlevel = $errTypes[$errno];
        } else {
            $errlevel = "Unknown";
        }

        $root = \ZMFileUtils::normalizeFilename(dirname(\ZMRuntime::getInstallationPath()));
        $errfile = str_replace($root, '', \ZMFileUtils::normalizeFilename($errfile));

        return "\"$time\",\"$errfile: $errline\",\"($errlevel) $errstr\"\r\n";
    }

    /**
     * A callback function that can be overriden to implement custom logging.
     *
     * @param string line The pre-fromatted log line.
     * @param array info All available log information.
     */
    public function logError($line, $info) {
      if ($this->enabled_) {
            foreach ($this->getHandlers() as $handler) {
                if ((null === ($customLevel = $handler->getLogLevel()) && self::ERROR <= $this->globalLogLevel_) || self::ERROR <= $customLevel) {
                    $handler->logError($line, $info);
                }
            }
        }
    }

    /**
     * PHP error handler callback.
     *
     * <p>if configured, this method will append all messages to the file
     * configured with <em>zmLogFilename</em>.</p>
     *
     * <p>If no file is configured, the regular webserver error file will be used.</p>
     *
     * @param int errno The error level.
     * @param string errstr The error message.
     * @param string errfile The source filename.
     * @param int errline The line number.
     * @param array errcontext All variables of scope when error triggered.
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        // convert all into an easy to handle array
        $info = array('errno' => $errno, 'msg' => $errstr, 'file' => $errfile, 'line' => $errline, 'context' => $errcontext);

        $line = $this->formatLog($errno, $errstr, $errfile, $errline, $errcontext);
        $this->logError($line, $info);
    }

    /**
     * PHP exception handler callback.
     *
     * @param Exception e The exception.
     */
    public function exceptionHandler($e) {
        $this->logError('Uncaught exception: '.$e->getMessage(), array('errno' => E_ERROR, 'context' => array('exception' => $e)));
    }

}
