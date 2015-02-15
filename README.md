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

## Configuration Examples

```php
// Simple example, give voice to every user who joins the channel.
new \hashworks\Phergie\Plugin\WhoisOnJoin\Plugin(function(\hashworks\Phergie\Plugin\WhoisOnJoin\WhoisResult $whoisResult) {
    $whoisResult->setChannelMode('+v', $whoisResult->getNick());
})
```

```php
// This is how I use it. Kickban every user who is in 13 channels or more. Ban based on nick and username, replace numbers with question marks.
new \hashworks\Phergie\Plugin\WhoisOnJoin\Plugin(function(\hashworks\Phergie\Plugin\WhoisOnJoin\WhoisResult $whoisResult) {
    if (count($whoisResult->getChannels()) >= 13) {
        $strReplaceNumbers = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $banMask = preg_replace_callback('/^(?<nick>.+?)(?<nicknumbers>[0-9]{0,})!(?<username>.+?)(?<usernumbers>[0-9]{0,})@.+$/', function ($matches) {
                        return $matches['nick'] . str_replace($strReplaceNumbers, '?', $matches['nicknumbers']) . '!' .
                                $matches['username'] . str_replace($strReplaceNumbers, '?', $matches['usernumbers']) . '@*';
        }, $whoisResult->getNick() . '!' . $whoisResult->getUsername() . '@' . $whoisResult->getHost());
        if (!empty($banMask)) {
            $whoisResult->setChannelMode('+b', $banMask);
            $whoisResult->privmsgUser('You have been banned automatically from ' . $whoisResult->getEvent()->getSource() . '. Please contact hashworks to fill a complaint.');
        }
        $whoisResult->kick('You have been kicked automatically. Please contact hashworks to fill a complaint.'); 
    }
})
```