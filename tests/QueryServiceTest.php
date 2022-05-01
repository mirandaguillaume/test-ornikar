<?php

namespace Test;

use App\Context\ApplicationContext;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Service\QueryService;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class QueryServiceTest extends TestCase
{
    private ObjectProphecy $applicationContext;

    private Learner $contextLearner;

    public function setUp(): void
    {
        $this->contextLearner = new Learner(
            1,
            'toto',
            'titi',
            'testEmail'
        );

        $this->applicationContext = $this->prophesize(ApplicationContext::class);
        $this->applicationContext
            ->getCurrentUser()
            ->shouldBeCalledOnce()
            ->willReturn($this->contextLearner)
        ;
    }

    public function testCreateQueryWithLessonOK()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [
            'lesson' => $lesson->reveal(),
        ];

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertEquals($data['lesson'], $result->lesson);
    }

    public function testCreateQueryNoLesson()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [];

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertNull($result->lesson);
    }

    public function testCreateQueryNoInstructor()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [];

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertNull($result->instructor);
    }

    public function testCreateQueryNoLearner()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [];

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertEquals($this->contextLearner, $result->learner);
    }
}
