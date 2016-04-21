<?php

WrongTypeException extends \ErrorException {
	
	public function __construct ($varName, $shouldBeType, $isType, $file, $line)
	{
		parent::construct("\${$varName} must be of type {$shouldBeType}... {$isType} given", 0, 1, $file, $line);
	}
}