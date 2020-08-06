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
 * Class Session
 *
 * This class controls the basic Session functionality that's needed for
 * applications of any size.
 *
 * The primary goal of this class is to provide a simplistic interface to
 * interact with session data.
 *
 * @package District5\SimpleSessionStore
 */
class Session
{
    const SESSION_PREFIX = '__D5_';

    /**
     * Instance of this class
     *
     * @var Session
     */
    protected static $_instance = null;

    /**
     * @var Storage
     */
    private $instance = false;

    /**
     * Protected __construct()
     *
     * @throws SessionException
     */
    final protected function __construct()
    {
        /** @noinspection SpellCheckingInspection */
        if (headers_sent($filename, $linenum)) {
            // @codeCoverageIgnoreStart
            throw new SessionException('Headers already sent in ' . $filename . '::' . $linenum);
            // @codeCoverageIgnoreEnd
        } else {
            $this->setup();
        }
    }

    /**
     * Retrieve an instance of Session
     *
     * @return Session
     * @throws SessionException
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Set a key, value in the standard session
     *
     * @param string $name
     * @param mixed $value
     * @throws SessionException
     * @return boolean result of set
     */
    public function set($name, $value)
    {
        $this->instance->unlock();
        $result = $this->instance->set($name, $value);
        $this->instance->lock();

        return $result;
    }

    /**
     * Remove a single value from the session
     *
     * @param string $name
     * @throws SessionException
     * @return boolean
     */
    public function remove($name)
    {
        $this->instance->unlock();
        $result = $this->instance->remove($name);
        $this->instance->lock();
        return $result;
    }

    /**
     * Clear all session values outside of the namespace.
     *
     * @throws SessionException
     * @return boolean
     */
    public function removeAll()
    {
        $this->instance->unlock();
        $result = $this->instance->removeAll();
        $this->instance->lock();
        return $result;
    }

    /**
     * Destroy the session and optionally specify $regenerate = true
     * to regenerate a new session id.
     *
     * @param boolean $regenerate
     * @throws SessionException
     * @return boolean
     */
    public function destroy($regenerate = false)
    {
        $this->instance->unlock();
        $result = $this->instance->destroy();
        if ($regenerate == true) {
            session_regenerate_id(true);
        }
        return $result;
    }

    /**
     * Retrieve a value from the session
     *
     * @param string $name
     * @throws SessionException
     * @return mixed|false for failure
     */
    public function get($name)
    {
        return $this->instance->get($name);
    }

    /**
     * Setup the namespace object
     */
    protected function setup()
    {
        $this->instance = new Storage(self::SESSION_PREFIX);
    }
}
