<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.05.18 11:47.
 */

namespace Ostretsov\MoodleParser\Util;

class StringUtil
{
    public static function pregMatchUtf8($matchAll, $pattern, $subject, $offset = 0)
    {
        $matchInfo = [];
        $method = 'preg_match';
        $flag = PREG_OFFSET_CAPTURE;
        if ($matchAll) {
            $method .= '_all';
        }
        $n = $method($pattern, $subject, $matchInfo, $flag, $offset);
        $result = [];
        if (0 !== $n && !empty($matchInfo)) {
            if (!$matchAll) {
                $matchInfo = [$matchInfo];
            }
            foreach ($matchInfo as $matches) {
                $positions = [];
                foreach ($matches as $match) {
                    $matchedText = $match[0];
                    $matchedLength = $match[1];
                    $positions[] = [
                        $matchedText,
                        mb_strlen(mb_strcut($subject, 0, $matchedLength)),
                    ];
                }
                $result[] = $positions;
            }
            if (!$matchAll) {
                $result = $result[0];
            }
        }

        return $result;
    }
}