<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 16.11.17 11:18.
 */

namespace Tests;

use ;
use Ostretsov\MoodleParser\Parser;
use PHPUnit\Framework\TestCase;
use Ostretsov\MoodleParser\Token\ShortAnswerFormToken;
use Ostretsov\MoodleParser\Token\StringToken;

class ParserTest extends TestCase
{
    /**
     * @expectedException \Ostretsov\MoodleParser\Exception\FormTokenNotFoundException
     */
    public function testQuestionWithoutFormTokens()
    {
        Parser::parse('Random text without form tokens');
    }

    /**
     * @expectedException \Ostretsov\MoodleParser\Exception\InvalidFormTokenException
     */
    public function testUnsupportedFormToken()
    {
        $result = Parser::parse('Random text with just one {1:WOWANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).~*#Wrong answer. The capital of France is Paris, of course.} short answer token');
    }

    public function testSimpleQuestionWithOneShortAnswerFormToken()
    {
        $result = Parser::parse('Random text with just one {1:SHORTANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).~*#Wrong answer. The capital of France is Paris, of course.} short answer token');

        $this->assertCount(3, $result);
        $this->assertInstanceOf(StringToken::class, $result[0]);
        $this->assertInstanceOf(ShortAnswerFormToken::class, $result[1]);
        $this->assertInstanceOf(StringToken::class, $result[2]);
    }

    public function testSimpleQuestionWithOneShortAnswerFormTokenAtTheEndOfString()
    {
        $result = Parser::parse('Random text with just one {1:SHORTANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).}');

        $this->assertCount(2, $result);
        $this->assertInstanceOf(StringToken::class, $result[0]);
        $this->assertInstanceOf(ShortAnswerFormToken::class, $result[1]);
    }

    public function testSimpleQuestionWithOnlyShortAnswerFormToken()
    {
        $result = Parser::parse('{1:SHORTANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).}');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ShortAnswerFormToken::class, $result[0]);
    }

    public function testSimpleQuestionWithTwoShortAnswerFormTokens()
    {
        $result = Parser::parse('{1:SHORTANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).}{1:SHORTANSWER:%100%Paris#Congratulations!~%50%Marseille#No, that is the second largest city in France (after Paris).}');

        $this->assertCount(2, $result);
        $this->assertInstanceOf(ShortAnswerFormToken::class, $result[0]);
        $this->assertInstanceOf(ShortAnswerFormToken::class, $result[1]);
    }

    public function testUnicodeValue()
    {
        $result = Parser::parse('Christina of Denmark, Duchess of Milan. She’s 16 years old and she’s a widow wearing widow’s clothes. You look at her hand and you’ll see her wedding ring. She didn’t seem to be too upset that she’s a widow but the 1. {1:SHORTANSWER:=marriage}  to her husband, the Duke of Milan, was arranged for her and when he died, well, she’s on the market again. This picture was made by Hans Holbein, a German artist who was working in London and it was actually made for a 2. {1:SHORTANSWER:=prospective}  husband who sent Holbein to meet Christina and to paint her 3. {1:SHORTANSWER:%100%likeness~%100%alikeness}, to take it back to him to see if he liked her. 
And the husband-to-be was called Henry VIII and Henry VIII, as I’m sure you know, got through wives like there was no tomorrow. This particular wife-to-be certainly tickled his fancy because the story goes that when he saw the picture he went straight up to it and planted a big wet kiss on the lips, which 4. {1:SHORTANSWER:%100%conservationists~%100%conservators}  now of course try to persuade people not to do to Old Master paintings. But, of course, Henry had paid for the picture; this was going to be his wife. Christina of Denmark was spared the fate of marrying Henry. The marriage 5. {1:SHORTANSWER:=negotiations}  fell through and, in fact, Christina later in her life is reported to have said that she was very glad that she didn’t marry Henry because she felt she was rather too attached to her head.');

        $this->assertSame('Christina of Denmark, Duchess of Milan. She’s 16 years old and she’s a widow wearing widow’s clothes. You look at her hand and you’ll see her wedding ring. She didn’t seem to be too upset that she’s a widow but the 1. ', $result[0]->getValue());
    }
}
