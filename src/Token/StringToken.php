<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 10:59.
 */

namespace Ostretsov\MoodleParser\Token;

final class StringToken implements TokenInterface
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function getType(): string
    {
        return 'string';
    }
}
