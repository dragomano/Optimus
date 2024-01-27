<?php

global $user_info, $smcFunc;

$user_info['language'] = 'english';

$smcFunc['substr'] = fn($string, $offset, $length) => substr($string, $offset, $length);
$smcFunc['strlen'] = fn($string) => strlen($string);
$smcFunc['htmlspecialchars'] = fn($string, $flags) => htmlspecialchars($string, $flags);

if (! function_exists('parse_bbc')) {
	function parse_bbc($string)
	{
		return $string;
	}
}

if (! function_exists('call_integration_hook')) {
	function call_integration_hook($hook, $args)
	{
	}
}

if (! function_exists('shorten_subject')) {
	function shorten_subject($subject, $length)
	{
		global $smcFunc;

		if ($smcFunc['strlen']($subject) <= $length)
			return $subject;

		return $smcFunc['substr']($subject, 0, $length) . '...';
	}
}
