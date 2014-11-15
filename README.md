OhSession
=========

OhSession is a session management library for PHP

[![Latest Stable Version](https://poser.pugx.org/rogerthomas84/ohsession/v/stable.svg)](https://packagist.org/packages/rogerthomas84/ohsession)
[![Total Downloads](https://poser.pugx.org/rogerthomas84/ohsession/downloads.svg)](https://packagist.org/packages/rogerthomas84/ohsession)
[![Latest Unstable Version](https://poser.pugx.org/rogerthomas84/ohsession/v/unstable.svg)](https://packagist.org/packages/rogerthomas84/ohsession)
[![License](https://poser.pugx.org/rogerthomas84/ohsession/license.svg)](https://packagist.org/packages/rogerthomas84/ohsession)
[![Build Status](https://travis-ci.org/rogerthomas84/ohsession.png)](http://travis-ci.org/rogerthomas84/ohsession)

Using Composer
--------------

To use OhSession with Composer, add the dependency (and version constraint) to your require block inside your `composer.json` file.

```json
{
    "require": {
        "rogerthomas84/ohsession": "1.0.*"
    }
}
```

### `\OhSession\Session` ###
Controls the basic Session functionality that's needed for applications of any size.
The primary goal of this class is to provide a simplistic interface to interact with session data.

### `\OhSession\Storage` ###
Provides a Session Namespace approach to storing data for a users session.
