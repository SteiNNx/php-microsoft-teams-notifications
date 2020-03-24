<?php


namespace App\Event\TeamsNotification;


use ArrayAccess;
use JsonSerializable;

class TeamsSerialize implements ArrayAccess, JsonSerializable {

	/** @var array */
	private $data;

	/**
	 * @param array $data
	 */
	public function __construct( $data = [] ) {
		$this->data = $data;
	}


	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->data[ $offset ] );
	}


	/**
	 * @param mixed $offset
	 *
	 * @return mixed|null
	 */
	public function offsetGet( $offset ) {
		return $this->data[ $offset ] ?? null;
	}


	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->data[] = $value;
		} else {
			$this->data[ $offset ] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset ) {
		unset( $this->data[ $offset ] );
	}

	public function jsonSerialize() {
		return array_merge( [
			'@context' => 'http://schema.org/extensions',
			'@type'    => 'MessageCard',
		], $this->data );
	}
}
