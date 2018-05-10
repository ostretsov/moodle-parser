<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 10:59.
 */

namespace Ostretsov\MoodleParser\Token;

interface Token
{
    public function getType(): string;
}
