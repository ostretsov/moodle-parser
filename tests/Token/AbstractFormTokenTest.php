<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 12:43.
 */

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use Ostretsov\MoodleParser\Token\AbstractFormToken;

class AbstractFormTokenTest extends TestCase
{
    public function testEmptyWeightParsing()
    {
        $mock = $this->getMockBuilder(AbstractFormToken::class)->setMethods(['getType'])->setConstructorArgs(['{:SHORTANSWER:bla-bla-bla}'])->getMock();

        $this->assertEquals(1, $mock->getWeight());
    }

    public function testWeightParsing()
    {
        $mock = $this->getMockBuilder(AbstractFormToken::class)->setMethods(['getType'])->setConstructorArgs(['{7:SHORTANSWER:bla-bla-bla}'])->getMock();

        $this->assertEquals(7, $mock->getWeight());
    }

    public function testFloatWeightParsing()
    {
        $mock = $this->getMockBuilder(AbstractFormToken::class)->setMethods(['getType'])->setConstructorArgs(['{3.2:SHORTANSWER:bla-bla-bla}'])->getMock();

        $this->assertEquals(3.2, $mock->getWeight(), '', 0.01);
    }

    public function testSimpelAnswerParsing()
    {
        $arg = '{1:SHORTANSWER:=Berlin}';
        $mock = $this->getMockBuilder(AbstractFormToken::class)->setMethods(['getType'])->setConstructorArgs([$arg])->getMock();
        $answers = $mock->getAnswers();

        $this->assertCount(1, $answers);
        $this->assertEquals(100, $answers[0]->getScore());
        $this->assertEquals('Berlin', $answers[0]->getAnswerText());
        $this->assertEquals('', $answers[0]->getFeedback());
    }

    public function testHeavyAnswersParsing()
    {
        $arg = '{1:SHORTANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).~*#Wrong answer. The capital of France is Paris, of course.}';
        $mock = $this->getMockBuilder(AbstractFormToken::class)->setMethods(['getType'])->setConstructorArgs([$arg])->getMock();
        $answers = $mock->getAnswers();

        $this->assertCount(2, $answers);
        $this->assertEquals(100, $answers[0]->getScore());
        $this->assertEquals('Paris', $answers[0]->getAnswerText());
        $this->assertEquals('Congratulations!', $answers[0]->getFeedback());
        $this->assertEquals(50, $answers[1]->getScore());
        $this->assertEquals('Marseille', $answers[1]->getAnswerText());
        $this->assertEquals('No, that is the second largest city in France (after Paris).', $answers[1]->getFeedback());
        $this->assertEquals('Wrong answer. The capital of France is Paris, of course.', $mock->getFeedbackForEverythingElse());
    }

    public function testAnotherAnswersParsing()
    {
        $arg = '{1:SHORTANSWER:Wrong answer#Feedback for this wrong answer~=Correct answer#Feedback for correct answer~%50%Answer that gives half the credit#Feedback for half credit answer}';
        $mock = $this->getMockBuilder(AbstractFormToken::class)->setMethods(['getType'])->setConstructorArgs([$arg])->getMock();
        $answers = $mock->getAnswers();

        $this->assertCount(3, $answers);
        $this->assertEquals(0, $answers[0]->getScore());
        $this->assertEquals('Wrong answer', $answers[0]->getAnswerText());
        $this->assertEquals('Feedback for this wrong answer', $answers[0]->getFeedback());
        $this->assertEquals(100, $answers[1]->getScore());
        $this->assertEquals('Correct answer', $answers[1]->getAnswerText());
        $this->assertEquals('Feedback for correct answer', $answers[1]->getFeedback());
        $this->assertEquals(50, $answers[2]->getScore());
        $this->assertEquals('Answer that gives half the credit', $answers[2]->getAnswerText());
        $this->assertEquals('Feedback for half credit answer', $answers[2]->getFeedback());
    }
}
