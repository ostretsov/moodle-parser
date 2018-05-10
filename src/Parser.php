<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 11:10.
 */

namespace Ostretsov\MoodleParser;

use Ostretsov\MoodleParser\Exception\FormTokenNotFoundException;
use Ostretsov\MoodleParser\Exception\InvalidFormTokenException;
use Ostretsov\MoodleParser\Token\AbstractFormToken;
use Ostretsov\MoodleParser\Token\MultiChoiceFormToken;
use Ostretsov\MoodleParser\Token\ShortAnswerFormToken;
use Ostretsov\MoodleParser\Token\StringToken;
use Ostretsov\MoodleParser\Token\Token;
use Ostretsov\MoodleParser\Util\StringUtil;

final class Parser
{
    /**
     * @param string $value
     *
     * @return Token[]
     */
    public static function parse(string $value): array
    {
        $tokens = self::tokenize($value);
        self::checkIfThereIsAtLeastOneAbstractFormToken($tokens);

        return $tokens;
    }

    /**
     * @param string $value
     *
     * @return Token[]
     */
    private static function tokenize(string $value): array
    {
        $tokens = [];

        $matches = StringUtil::pregMatchUtf8(true, '/({.*?})/mu', $value);
        if (0 === count($matches)) {
            return $tokens;
        }

        $lastOffset = 0;
        foreach ($matches[0] as $match) {
            list($tokenString, $offset) = $match;
            if ($lastOffset != $offset) {
                $tokens[] = new StringToken(mb_substr($value, $lastOffset, $offset - $lastOffset));
                $lastOffset = $offset;
            }

            // get answer type
            preg_match('/:([A-Z]+):/', $tokenString, $answerTypeMatches);
            if (0 == count($answerTypeMatches)) {
                throw new InvalidFormTokenException(sprintf('"%s" token does not contain valid answer type!', $tokenString));
            }

            switch ($answerTypeMatches[1]) {
                case 'SHORTANSWER':
                    $tokens[] = new ShortAnswerFormToken($tokenString);
                    break;
                case 'MULTICHOICE':
                    $tokens[] = new MultiChoiceFormToken($tokenString);
                    break;
                default:
                    throw new InvalidFormTokenException(sprintf('"%s" type is not supported!', $answerTypeMatches[1]));
            }
            $lastOffset += mb_strlen($tokenString);
        }
        if ($lastOffset != mb_strlen($value)) {
            $tokens[] = new StringToken(mb_substr($value, $lastOffset));
        }

        return $tokens;
    }

    /**
     * @throws FormTokenNotFoundException
     */
    private static function checkIfThereIsAtLeastOneAbstractFormToken($tokens)
    {
        $hasFormToken = false;
        foreach ($tokens as $token) {
            if ($token instanceof AbstractFormToken) {
                $hasFormToken = true;
                break;
            }
        }

        if (!$hasFormToken) {
            throw new FormTokenNotFoundException();
        }
    }
}
