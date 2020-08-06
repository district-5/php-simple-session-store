<?php
/**
 * District5 - SimpleSessionStore
 *
 * @copyright District5
 *
 * @author District5
 * @link https://www.district5.co.uk
 *
 * @license This software and associated documentation (the "Software") may not be
 * used, copied, modified, distributed, published or licensed to any 3rd party
 * without the written permission of District5 or its author.
 *
 * The above copyright notice and this permission notice shall be included in
 * all licensed copies of the Software.
 *
 */
namespace District5\SimpleSessionStore;

/**
 * Class Storage
 *
 * This class provides a Session Namespace approach to storing data for a
 * users session.
 *
 * There are no restrictions, and storage buckets can be constructed and
 * locked to protect the data from accidental damage.
 *
 * @package District5\SimpleSessionStore
 */
class Storage 
{
    /**
     * Name of the secret storage within the session
     *
     * @var string
     */
    protected static $SECRET_STORAGE = "__D5_pr";

    /**
     * Name of the public storage within the session
     *
     * @var string
     */
    protected static $PUBLIC_STORAGE = "__D5_pb";

    /**
     * The name of this namespace.
     *
     * @var string
     */
    protected $_namespaceName = null;

    /**
     * Initiate a new or existing namespace
     *
     * @param string $name defaults to 'Default'
     * @throws SessionException
     */
    public function __construct($name = 'Default')
    {
        $name = trim($name);
        $prefix = Session::SESSION_PREFIX;

        if (headers_sent($filename, $linenum)) {
            // @codeCoverageIgnoreStart
            throw new SessionException('Headers already sent in ' . $filename . '::' . $linenum);
            // @codeCoverageIgnoreEnd
        } else {
            if ($name === null || $name === '') {
                throw new SessionException('Namespace name cannot be empty');
            } else if ($name[0] == "_" && substr($name, 0, strlen($prefix)) !== $prefix) {
                throw new SessionException('Namespace name cannot start with an underscore.');
            } else if (preg_match('#(^[0-9])#i', $name[0])) {
                throw new SessionException('Namespace name cannot start with a number');
            } else {
                $this->_namespaceName = $name;
                @session_start();
                $this->setup();
            }
        }
    }

    /**
     * Lock the namespace, this will prevent removal of keys
     *
     * @return boolean
     * @throws SessionException
     */
    public function lock()
    {
        $this->_validate();
        $_SESSION[self::$SECRET_STORAGE]['locks'][$this->_namespaceName] = true;
        return true;
    }

    /**
     * Unlock the namespace, this will allow removal of keys
     *
     * @return boolean
     * @throws SessionException
     */
    public function unlock()
    {
        $this->_validate();
        $_SESSION[self::$SECRET_STORAGE]['locks'][$this->_namespaceName] = false;
        return true;
    }

    /**
     * Check if a namespace is currently locked.
     *
     * @return boolean
     * @throws SessionException
     */
    public function isLocked()
    {
        $this->_validate();
        if ($_SESSION[self::$SECRET_STORAGE]['locks'][$this->_namespaceName] == true) {
            return true;
        }
        return false;
    }

    /**
     * Set a value in the current namespace
     *
     * @param string $name
     * @param mixed $value
     * @return boolean result of save
     * @throws SessionException
     */
    public function set($name, $value)
    {
        if (!$this->isLocked()) {
            $_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName][$name] = $value;
            return true;
        }
        return false;
    }

    /**
     * Retrieve a single value from the namespace
     *
     * @param string $name
     * @return mixed
     * @throws SessionException
     */
    public function get($name)
    {
        $this->_validate();
        if (array_key_exists($name, $_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName])) {
            return $_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName][$name];
        }
        return false;
    }

    /**
     * Retrieve the entire namespace
     *
     * @return array|false on failure
     * @throws SessionException
     */
    public function getAll()
    {
        $this->_validate();
        if (array_key_exists($this->_namespaceName, $_SESSION[self::$PUBLIC_STORAGE]['store'])) {
            return $_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName];
        }
        return false;
    }

    /**
     * Remove an key from the namespace
     *
     * @param string $name
     * @return boolean result of removal
     * @throws SessionException
     */
    public function remove($name)
    {
        $this->_validate();
        if (!$this->get($name)) {
            return true;
        }
        if (!$this->isLocked()) {
            unset($_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName][$name]);
            return true;
        }
        return false;
    }

    /**
     * Clear all values currently held in this namespace
     *
     * @return boolean status of removal
     * @throws SessionException
     */
    public function removeAll()
    {
        $this->_validate();
        if (!$this->isLocked()) {
            $_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName] = array();
            return true;
        }
        return false;
    }

    /**
     * Destroy this entire namespace. After calling this
     * the namespace will no longer be held in session
     *
     * @return boolean
     * @throws SessionException
     */
    public function destroy()
    {
        $this->_validate();
        if (!$this->isLocked()) {
            unset($_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName]);
            return true;
        }
        return false;
    }

    /**
     * Ensure the session contains the data we expect to see.
     *
     * @return boolean
     */
    protected function setup()
    {
        if (array_key_exists(self::$SECRET_STORAGE, $_SESSION)) {
            if (!is_array($_SESSION[self::$SECRET_STORAGE])) {
                $_SESSION[self::$SECRET_STORAGE] = array('locks' => array($this->_namespaceName => false));
            } else {
                if (!array_key_exists($this->_namespaceName, $_SESSION[self::$SECRET_STORAGE]['locks'])) {
                    $_SESSION[self::$SECRET_STORAGE]['locks'][$this->_namespaceName] = false;
                } else {
                    if (!is_bool($_SESSION[self::$SECRET_STORAGE]['locks'][$this->_namespaceName])) {
                        $_SESSION[self::$SECRET_STORAGE]['locks'][$this->_namespaceName] = false;
                    }
                }
            }
        } else {
            $_SESSION[self::$SECRET_STORAGE] = array('locks' => array($this->_namespaceName => false));
        }

        if (array_key_exists(self::$PUBLIC_STORAGE, $_SESSION)) {
            if (array_key_exists('store', $_SESSION[self::$PUBLIC_STORAGE])) {
                if (!array_key_exists($this->_namespaceName, $_SESSION[self::$PUBLIC_STORAGE]['store'])) {
                    $_SESSION[self::$PUBLIC_STORAGE]['store'][$this->_namespaceName] = array();
                }
            } else {
                $_SESSION[self::$PUBLIC_STORAGE]['store'] = array($this->_namespaceName => array());
            }
        } else {
            $_SESSION[self::$PUBLIC_STORAGE] = array('store' => array($this->_namespaceName => array()));
        }

        return true;
    }

    /**
     * Validate if a session exists.
     *
     * @throws SessionException
     */
    protected function _validate()
    {
        if (!isset($_SESSION)) {
            throw new SessionException('Session may not be started.');
        }
        return;
    }
}
