# ZTal

ZTal is an open source project to replace the View and Form capabilities of
[Zend Framework](http://framework.zend.com) with the [PHPTAL](http://phptal.org)
templating engine.

ZTal provides the glue that allows Zend Framework to use PHPTAL templates as
Views and Layouts while also allowing PHPTAL to access Zend Framework's
Translation facilities. In particular:

* ZTal replaces Zend View and Layout objects with PHPTAL equivalents.
* ZTal has macros to render Zend_Form and other common Zend objects such as
  Zend_Date and Zend_Currency.
* ZTal has access to Zend_Translate, unifying translation support.
* ZTal handles PHPTAL translation namespaces where they are not supported
  natively in Zend_Translate.
* ZTal provides plural support to PHPTAL's translation capabilities.
* ZTal can use Zend_Cache to cache rendered PHPTAL pages.
* ZTal provides a Zend_Mail subclass to use templates with email.


## Documentation

For more information and documentation please visit the
[ZTal homepage](http://opensource.names.co.uk/ztal) or look through the example
Zend application provided.


## Other Questions

Feel free to chat with the ZTal developers (and others) on IRC in the #ztal
channel on Freenode.


## License

See LICENSE for more details.
