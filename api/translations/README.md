# Symfony translation files

Docs: [https://symfony.com/doc/current/translation.html#message-format](https://symfony.com/doc/current/translation.html#message-format)

We use the english message as the translation key because symfony does that as well for their validation messages.
[https://github.com/symfony/validator/blob/v6.1.4/Resources/translations/validators.en.xlf](https://github.com/symfony/validator/blob/v6.1.4/Resources/translations/validators.en.xlf)
We still add the english translation, that external translators could improve the maybe
not perfect english of the developers.

Please **alphasort** the keys, that we don't have to discuss their order.
Also use quotes for the values in languages which don't require it that it's consistent in all languages.
