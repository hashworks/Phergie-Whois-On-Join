<?php

namespace hashworks\Phergie\Plugin\WhoisOnJoin;

use Phergie\Irc\Event\UserEventInterface as Event;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;

/**
 * User class.
 *
 * @category Phergie
 * @package hashworks\Phergie\Plugin\WhoisOnJoin
 */
class WhoisResult {

	protected $nick     = '';
	protected $username = '';
	protected $realname = '';
	protected $host     = '';
	protected $server   = '';

	protected $ircOperator      = false;
	protected $identified       = false;
	protected $secureConnection = false;

	protected $modes    = array();
	protected $channels = array();

	/**
	 * @var Event
	 */
	protected $event;

	/**
	 * @var Queue
	 */
	protected $queue;

	public function __construct(Event $event, Queue $queue) {
		$this->event = $event;
		$this->queue = $queue;
	}

	/**
	 * Sets a user mode for the user.
	 * Example: $user->setUserMode('+iws')
	 *
	 * @link http://docs.dal.net/docs/modes.html#3
	 * @param string $mode
	 * @param string $param = null
	 */
	public function setUserMode($mode, $param = null) {
		$this->queue->ircMode($this->nick, $mode, $param);
	}

	/**
	 * Sets a channel mode.
	 * Example: $user->setChannelMode('+b', 'nickname!~username@host')
	 *
	 * @link http://docs.dal.net/docs/modes.html#2
	 * @param string $mode
	 * @param string $param = null
	 */
	public function setChannelMode($mode, $param = null) {
		$this->queue->ircMode($this->event->getSource(), $mode, $param);
	}

	/**
	 * Kicks the user out of the channel.
	 *
	 * @param string $comment = null
	 */
	public function kick($comment = null) {
		$this->queue->ircKick($this->event->getSource(), $this->nick, $comment);
	}

	/**
	 * Send a PRIVMSG to the channel.
	 *
	 * @param string $privmsg
	 */
	public function privmsgChannel($privmsg) {
		$this->queue->ircPrivmsg($this->event->getSource(), $privmsg);
	}

	/**
	 * Send a PRIVMSG to the user.
	 *
	 * @param string $privmsg
	 */
	public function privmsgUser($privmsg) {
		$this->queue->ircPrivmsg($this->nick, $privmsg);
	}

	/**
	 * Send a NOTICE to the channel.
	 *
	 * @param string $message
	 */
	public function noticeChannel($message) {
		$this->queue->ircNotice($this->event->getSource(), $message);
	}

	/**
	 * Send a NOTICE to the user.
	 *
	 * @param string $message
	 */
	public function noticeUser($message) {
		$this->queue->ircNotice($this->nick, $message);
	}

	/**
	 * @return Event
	 */
	public function getEvent () {
		return $this->event;
	}

	/**
	 * @return Queue
	 */
	public function getQueue () {
		return $this->queue;
	}

	/**
	 * @return string
	 */
	public function getNick () {
		return $this->nick;
	}

	/**
	 * @param string $nick
	 */
	public function setNick ($nick) {
		$this->nick = $nick;
	}

	/**
	 * @return string
	 */
	public function getUsername () {
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername ($username) {
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getRealname () {
		return $this->realname;
	}

	/**
	 * @param string $realname
	 */
	public function setRealname ($realname) {
		$this->realname = $realname;
	}

	/**
	 * @return string
	 */
	public function getHost () {
		return $this->host;
	}

	/**
	 * @param string $host
	 */
	public function setHost ($host) {
		$this->host = $host;
	}

	/**
	 * @return string
	 */
	public function getServer () {
		return $this->server;
	}

	/**
	 * @param string $server
	 */
	public function setServer ($server) {
		$this->server = $server;
	}

	/**
	 * @return boolean
	 */
	public function isIrcOperator () {
		return $this->ircOperator;
	}

	/**
	 * @param boolean $isIrcOperator
	 */
	public function setIrcOperator ($isIrcOperator) {
		$this->ircOperator = boolval($isIrcOperator);
	}

	/**
	 * @return boolean
	 */
	public function isIdentified () {
		return $this->identified;
	}

	/**
	 * @param boolean $isIdentified
	 */
	public function setIdentified ($isIdentified) {
		$this->identified = boolval($isIdentified);
	}

	/**
	 * @return boolean
	 */
	public function hasSecureConnection () {
		return $this->secureConnection;
	}

	/**
	 * @param boolean $hasSecureConnection
	 */
	public function setSecureConnection ($hasSecureConnection) {
		$this->secureConnection = boolval($hasSecureConnection);
	}

	/**
	 * @return string[]
	 */
	public function getModes () {
		return $this->modes;
	}

	/**
	 * @param string[] $modes
	 */
	public function setModes ($modes) {
		$this->modes = $modes;
	}

	/**
	 * @return string[]
	 */
	public function getChannels () {
		return $this->channels;
	}

	/**
	 * @param string[] $channels
	 */
	public function setChannels ($channels) {
		$this->channels = $channels;
	}



}