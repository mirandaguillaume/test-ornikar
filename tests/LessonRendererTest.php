<?php

namespace Test;

use App\Entity\Lesson;
use App\Service\LessonRenderer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class LessonRendererTest extends TestCase
{
    use ProphecyTrait;

    public function testRenderHtml()
    {
        $expectedLesson = $this->createLesson();

        $result = (new LessonRenderer())->renderHtml($expectedLesson);

        $this->assertEquals('<p>' . $expectedLesson->id . '</p>', $result);
    }

    public function testRenderText()
    {
        $expectedLesson = $this->createLesson();

        $result = (new LessonRenderer())->renderText($expectedLesson);

        $this->assertEquals((string)$expectedLesson->id, $result);
    }

    private function createLesson(): Lesson
    {
        return new Lesson(
            1,
            1,
            1,
            new \DateTime(),
            new \DateTime()
        );
    }
}
