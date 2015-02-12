# PhergieWhoisOnJoin

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin to whois a user on join and handle the result.

## About

This plugin was originally written to keep the growing amount of chan-whore-monitoring-bots in the Rizon network out of channels.
However you can do what you want with the whois result, it'll require a bit of PHP knowledge trough.

## Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```
composer require hashworks/phergie-whois-on-join-plugin
```

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
// Required. Simple example.
new \hashworks\Phergie\Plugin\WhoisOnJoin\Plugin(function(\hashworks\Phergie\Plugin\WhoisOnJoin\WhoisResult $whoisResult) {
    $whoisResult->setChannelMode('+v', $whoisResult->getNick());
})
```
