<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 11:01.
 */

namespace Ostretsov\MoodleParser\Token;

final class ShortAnswerFormToken extends AbstractFormToken
{
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    public function getType(): string
    {
        return 'short answer';
    }
}
