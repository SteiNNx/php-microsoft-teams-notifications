<?php


namespace App\Event\TeamsNotification;


use Exception;

class TeamsNotification {

	public const DEFAULT_LOG_WEBHOOK = 'QID_CODE_WEBHOOK';

	/**
	 * @param Exception $exception
	 *
	 * @return bool|string
	 */
	public static function sendErrorException( Exception $exception ) {
		if ( env( 'LOG_SEND', false ) ) {
			$data = [
				'fichero'  => $exception->getFile(),
				'linea'    => $exception->getLine(),
				'mensaje'  => $exception->getMessage(),
				'codigo'   => $exception->getCode(),
				'function' => $exception->getTrace()[0]['function'],
				'args'     => $exception->getTrace()[0]['args'],
			];

			return ( new TeamsMessage( env( 'LOG_WEBHOOK', self::DEFAULT_LOG_WEBHOOK ) ) )->pushTeams( $data );
		}
	}
}
