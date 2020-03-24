<?php

namespace Tests\Events;

use App\Event\TeamsNotification\TeamsMessage;
use App\Event\TeamsNotification\TeamsNotification;
use Exception;
use Monolog\Logger;
use Tests\TestCase;

class TeamsNotificationTest extends TestCase {

	public function testTeamsMessagePushNotification(): void {
		$data_post = [
			'title'   => 'title',
			'mensaje' => 'message',
			'level'   => Logger::EMERGENCY,
		];
		$response  = ( new TeamsMessage( env( 'LOG_WEBHOOK', TeamsNotification::DEFAULT_LOG_WEBHOOK ) ) )
			->pushTeams( $data_post );
		$this->assertTrue( (boolean) $response );
	}

	public function testTeamsNotificationSendError(): void {
		$response = TeamsNotification::sendErrorException( new Exception( 'ErrorPhpUnitTest', 400 ) );
		$this->assertTrue( (boolean) $response );
	}
}
