<?php

namespace hashworks\Phergie\Plugin\WhoisOnJoin;

use Phergie\Irc\Bot\React\AbstractPlugin;
use Phergie\Irc\Event\ServerEvent;
use Phergie\Irc\Event\UserEventInterface as Event;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Client\React\Exception;

/**
 * Plugin class.
 *
 * @category Phergie
 * @package hashworks\Phergie\Plugin\WhoisOnJoin
 */
class Plugin extends AbstractPlugin {

	protected $callback;

	/**
	 * @param callable $callback
	 * @throws Exception
	 */
	public function __construct(callable $callback) {
		$this->callback = $callback;
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents () {
		return array('irc.received.join' => 'handleJoin');
	}

	/**
	 * @param Event $event
	 * @param Queue $queue
	 */
	public function handleJoin(Event $event, Queue $queue) {
		$nick = $event->getNick();

		if (empty($nick) || $nick == $event->getConnection()->getNickname()) {
			// Don't handle our own join
			return;
		}

		$user = new whoisResult($event, $queue);
		$user->setNick($nick);
		$user->setUsername($event->getUsername());
		$user->setHost($event->getHost());

		$queue->ircWhois('', $nick);

		$whoisUserListener = function(ServerEvent $event) use ($user) {
			$user->setServer($event->getServername());
			if (isset($event->getParams()[6])) {
				$user->setRealname($event->getParams()[6]);
			}
			$listeners = array(
				'irc.received.rpl_whoisregnick' => function() use ($user) {
					$user->setIdentified(true);
				}, // 307 (Bahamut, Unreal)
				'irc.received.rpl_whoisserver' => function(ServerEvent $event) use ($user) {
					if (isset($event->getParams()[3])) {
						$user->setServer($event->getParams()[3]);
					}
				}, // 312
				'irc.received.rpl_whoisoperator' => function() use ($user) {
					$user->setIrcOperator(true);
				}, // 313
				'irc.received.rpl_whoischannels' => function(ServerEvent $event) use ($user) {
					if (isset($event->getParams()[3])) {
						$user->setChannels(explode(' ', $event->getParams()[3]));
					}
				}, // 319
				'irc.received.rpl_whoissecure' => function() use ($user) {
					$user->setSecureConnection(true);
				} // 671
			);

			foreach ($listeners as $event => $listener) {
				// RPL_WHOISCHANNELS can be send multiple times if the user is in many channels
				if ($event == 'irc.received.rpl_whoischannels') {
					$this->emitter->on($event, $listener);
				} else {
					$this->emitter->once($event, $listener);
				}
			}

			$this->emitter->once('irc.received.rpl_endofwhois', function() use ($listeners, $user) {
				foreach ($listeners as $event => $listener) {
					$this->emitter->removeListener($event, $listener);
				}
				// $this->callback($user) won't work.
				$callback = $this->callback;
				$callback($user);
			}); // 318
		};

		$noSuchNickListener = function() use ($whoisUserListener) {
			$this->emitter->removeListener('irc.received.rpl_whoisuser', $whoisUserListener);
		};

		$this->emitter->once('irc.received.rpl_whoisuser', $whoisUserListener); // 311
		$this->emitter->once('irc.received.err_nosuchnick', $noSuchNickListener); // 401

		$this->emitter->once('irc.received.rpl_whoisuser', function() use($noSuchNickListener) {
			$this->emitter->removeListener('irc.received.err_nosuchnick', $noSuchNickListener);
		}); // 311
	}
}
