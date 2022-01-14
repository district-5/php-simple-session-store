<?php
/**
 * District5 Session Store Library
 *
 * @author      District5 <hello@district5.co.uk>
 * @copyright   District5 <hello@district5.co.uk>
 * @link        https://www.district5.co.uk
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
     * @var Session|null
     */
    protected static ?Session $_instance = null;

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
            throw new SessionException('Headers already sent in ' . $filename . '::' . $linenum);
        } else {
            $this->setup();
        }
    }

    /**
     * Retrieve an instance of Session
     *
     * @return Session
     * @throws SessionException
     * @noinspection PhpDocRedundantThrowsInspection
     * @noinspection PhpUnused
     */
    public static function getInstance(): Session
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
     * @return boolean result of set
     * @throws SessionException
     * @noinspection PhpUnused
     */
    public function set(string $name, $value): bool
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
     * @return boolean
     * @throws SessionException
     * @noinspection PhpUnused
     */
    public function remove(string $name): bool
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
     * @noinspection PhpUnused
     */
    public function removeAll(): bool
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
     * @return boolean
     * @throws SessionException
     * @noinspection PhpUnused
     */
    public function destroy(bool $regenerate = false): bool
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
     * @return mixed|false for failure
     * @throws SessionException
     * @noinspection PhpUnused
     */
    public function get(string $name)
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
