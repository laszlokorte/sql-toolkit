<?php

namespace LaszloKorte\Resource;

final class IdConverter {
	public function convert($id) {
		return split_escaped(':', '!', $id);
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