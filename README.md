SimpleSessionStore
==================

SimpleSessionStore is a session management library for PHP. It was originally part of OhSession.

### Class overview:

* `\District5\SimpleSessionStore\Session`
    * Controls the basic Session functionality that's needed for applications of any size.
      The primary goal of this class is to provide a simplistic interface to interact with 
      session data.
* `\District5\SimpleSessionStore\Storage`
    * Provides a Session Namespace approach to storing data for a users session.

### Usage

* Example Composer file contents:
    ```json
    {
      "repositories":[
          {
              "type": "vcs",
              "url": "git@github.com:district-5/php-simple-session-store.git"
          }
      ],
      "require": {
          "district-5/simple-session-store": ">=1.0.0"
      }
    }  
    ```
* Set a value:
    ```php
    <?php
    $sess = \District5\SimpleSessionStore\Session::getInstance();
    if ($sess->set('foo', 'bar') === true) {
        // set ok.
    }
    ```
* Get a value:
    ```php
    <?php
    $sess = \District5\SimpleSessionStore\Session::getInstance();
    $val = $sess->get('foo');
    if ($val !== false) {
        // get ok
    }
    ```
* Remove a key:
    ```php
    <?php
    $sess = \District5\SimpleSessionStore\Session::getInstance();
    if ($sess->remove('foo') === true) {
        // remove ok
    }
    ```
* Remove all keys:
    ```php
    <?php
    $sess = \District5\SimpleSessionStore\Session::getInstance();
    if ($sess->removeAll() === true) {
        // remove all ok
    }
    ```
* Destroy the session (and optionally regenerate):
    ```php
    <?php
    $sess = \District5\SimpleSessionStore\Session::getInstance();
    if ($sess->destroy(true) === true) { // or pass false if you don't want to regenerate a session.
        // destroy ok
    }
    ```
