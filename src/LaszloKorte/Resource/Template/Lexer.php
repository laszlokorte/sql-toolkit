<?php

namespace LaszloKorte\Resource\Template;

final class Lexer {

	const T_WHITESPACE = 'T_WHITESPACE';
	const T_OPEN = 'T_OPEN';
	const T_CLOSE = 'T_CLOSE';
	const T_PATH_SEPARATOR = 'T_PATH_SEPARATOR';
	const T_FILTER_SEPARATOR = 'T_FILTER_SEPARATOR';
	const T_IDENTIFIER = 'T_IDENTIFIER';
	const T_FOREIGN = 'T_FOREIGN';


	private $terminals = [
		 'T_WHITESPACE' => '~(\s+)~iA',
		 'T_OPEN' => '~(\{\{)~iA',
		 'T_CLOSE' => '~(\}\})~iA',
		 'T_PATH_SEPARATOR' => '~(\.)~iA',
		 'T_FILTER_SEPARATOR' => '~(\|)~iA',
		 'T_IDENTIFIER' => '~(?:(?:([0-9a-zA-Z\$\_]+)|\`((?:[^\`]|\\|\`)+)\`))~iA',
		 'T_FOREIGN' => '~((?:{[^{])*(?:[^{](?:\\\\)*(?:\\{)*)+)~iA',
	];

	public function __construct() {
	}

	public function tokenize($string) {
		$offset = 0;
		$length = strlen($string);
		$tokens = [];
		while($offset < $length) {
			$result = $this->match($string, $offset);
			if($result === false) {
				throw new \Exception(sprintf("Unexpected char '%s' at offset %d", $string[$offset], $offset));
			}
			$tokens[] = $result;
			$offset += strlen($result['match']);
		}

		return $tokens;
	}

	private function match($string, $offset) {
		foreach($this->terminals as $name => $pattern) {
			if(preg_match($pattern, $string, $matches, 0, $offset)) {
				return array(
					'match' => $matches[1],
					'token_type' => $name,
					'offset' => $offset,
				);
			}
		}

		return false;
	}
}