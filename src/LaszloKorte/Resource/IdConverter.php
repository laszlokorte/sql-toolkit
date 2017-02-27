<?php

namespace LaszloKorte\Resource;


use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class IdConverter {
	public function convert($id) {
		$parts = split_escaped(':', '!', $id);

		if(empty($parts)) {
			throw new NotFoundHttpException();
		}

		return $parts;
	}
}

function split_escaped($delimiter, $escaper, $text)
{
	$d = preg_quote($delimiter, "~");
	$e = preg_quote($escaper, "~");
	$tokens = preg_split(
		'~' . $e . '(' . $e . '|' . $d . ')(*SKIP)(*FAIL)|' . $d . '~',
		$text
	);
	$escaperReplacement = str_replace(['\\', '$'], ['\\\\', '\\$'], $escaper);
	$delimiterReplacement = str_replace(['\\', '$'], ['\\\\', '\\$'], $delimiter);
	return preg_replace(
		['~' . $e . $e . '~', '~' . $e . $d . '~'],
		[$escaperReplacement, $delimiterReplacement],
		$tokens
	);
}