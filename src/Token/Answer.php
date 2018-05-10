<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 11:07.
 */

namespace Ostretsov\MoodleParser\Token;

final class Answer
{
    /**
     * @var int In percent
     */
    private $score;

    /**
     * @var string
     */
    private $answerText;

    /**
     * @var string|null
     */
    private $feedback;

    /**
     * Answer constructor.
     *
     * @param int         $score
     * @param string      $answerText
     * @param null|string $feedback
     */
    public function __construct($score, $answerText, $feedback)
    {
        $this->score = $score;
        $this->answerText = $answerText;
        $this->feedback = $feedback;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getAnswerText(): string
    {
        return $this->answerText;
    }

    /**
     * @return null|string
     */
    public function getFeedback()
    {
        return $this->feedback;
    }
}
