<?php


namespace App\Event\TeamsNotification;

use Monolog\Logger;

/**
 * Class TeamsMessage
 * @package App\Event\TeamsNotification
 * @version v0.9.0
 * @author Jorge Reyes Gálvez
 */
class TeamsMessage {

	/**
	 * @var string $url
	 */
	public $url;

	/**
	 * @var string $level
	 */
	public $level = 'WARNING';

	/**
	 * @var array $levelColors
	 */
	public $levelColors = [
		Logger::DEBUG     => '0080FF',
		Logger::INFO      => '0080FF',
		Logger::NOTICE    => '0080FF',
		Logger::WARNING   => 'FF8000',
		Logger::ERROR     => 'FF0000',
		Logger::CRITICAL  => 'FF0000',
		Logger::ALERT     => 'FF0000',
		Logger::EMERGENCY => 'FF0000',
	];

	/**
	 * TeamsMessage constructor.
	 * @link documentacion https://docs.microsoft.com/en-us/outlook/actionable-messages/send-via-connectors
	 * @link documentacion https://techcommunity.microsoft.com/t5/Microsoft-Teams/Microsoft-Teams-Incoming-Webhook-Message-Formatting/m-p/31984
	 *
	 * @param $url
	 * @param int $level
	 */
	public function __construct( $url, $level = Logger::EMERGENCY ) {
		$this->url   = $url;
		$this->level = $level;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize( $data ): array {
		return array_merge( [
			'@context' => 'http://schema.org/extensions',
			'@type'    => 'MessageCard',
		], $data );
	}

	/**
	 * Example SimpleCard @link https://messagecardplayground.azurewebsites.net/
	 *
	 * @param array $record
	 *
	 * @return TeamsSerialize
	 */
	public function getMessage( array $record ): TeamsSerialize {
		return new TeamsSerialize( [
			'title'      => $record['mensaje'],
			'text'       => substr( json_encode( $record, JSON_THROW_ON_ERROR, 512 ), 0, 300 ),
			'themeColor' => array_key_exists( 'level', $record ) ? $this->levelColors[ $record['level'] ] : $this->levelColors[ $this->level ],
		] );
	}

	/**
	 * @param array $record
	 *
	 * @return bool|string
	 */
	public function pushTeams( array $record ) {
		$json = json_encode( $this->getMessage( $record ), JSON_THROW_ON_ERROR, 512 );
		$ch   = curl_init( $this->url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $json )
		] );

		return curl_exec( $ch );
	}
}
