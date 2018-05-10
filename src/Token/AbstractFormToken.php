<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 11:00.
 */

namespace Ostretsov\MoodleParser\Token;

use Ostretsov\MoodleParser\Exception\InvalidFormTokenException;

abstract class AbstractFormToken implements Token
{
    /**
     * @var float
     */
    protected $weight = 1;

    /**
     * @var Answer[]
     */
    private $answers = [];

    /**
     * @var string|null
     */
    private $feedbackForEverythingElse;

    public function __construct(string $value)
    {
        $this->parseWeight($value);
        $this->parseAnswers($value);
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return Answer[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    private function parseWeight(string $value)
    {
        preg_match('/^\{([\d\.]*):/', $value, $matches);
        if (0 == count($matches)) {
            throw new InvalidFormTokenException(sprintf('"%s" does not contain weight!'));
        }

        if (empty($matches[1])) {
            $this->weight = 1;
        } else {
            $this->weight = floatval($matches[1]);
        }
    }

    /**
     * @param string $value
     */
    private function parseAnswers(string $value)
    {
        preg_match('/:[A-Z]+:(.+)\}/', $value, $rawAnswers);
        if (empty($rawAnswers[1])) {
            return [];
        }

        $rawAnswers = explode('~', $rawAnswers[1]);
        foreach ($rawAnswers as $answer) {
            // default
            if (0 === strpos($answer, '*') && false !== strpos($answer, '#')) {
                $this->feedbackForEverythingElse = mb_substr($answer, strpos($answer, '#') + 1);

                continue;
            }

            preg_match('/^%(\d+)%/', $answer, $scoreMatches);
            $score = 0;
            if (!empty($scoreMatches) && isset($scoreMatches[1])) {
                $score = intval($scoreMatches[1]);
            } elseif (0 === strpos($answer, '=')) {
                $answer = mb_substr($answer, 1);
                $score = 100;
            }

            $answerText = preg_replace('/^%(\d+)%/', '', $answer);
            $answerText = preg_replace('/#.*$/', '', $answerText);

            $feedback = '';
            if (false !== $pos = strpos($answer, '#')) {
                $feedback = mb_substr($answer, $pos + 1);
            }

            $this->answers[] = new Answer($score, $answerText, $feedback);
        }
    }

    /**
     * @return null|string
     */
    public function getFeedbackForEverythingElse()
    {
        return $this->feedbackForEverythingElse;
    }
}
