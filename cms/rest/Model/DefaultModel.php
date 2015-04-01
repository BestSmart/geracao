<?php

namespace Model;

use JsonSerializable;

abstract class DefaultModel implements JsonSerializable {

	public function __construct() {
		
	}

	public function expose() {
        return get_object_vars($this);
    }

    public function jsonSerialize() {
        return $this->expose();
    }

    public function fromArray($array) {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

}
