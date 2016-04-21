<?php

WrongActionException extends \ErrorException {
	
	public function __construct (array $actionTypes, $given, $file, $line)
	{
		$actionTypes = join(', ', $actionTypes);

		parent::construct("\$action must be one of {$actionTypes}... {$given} given", 0, 1, $file, $line);
	}
}