<?php

namespace Test;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
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

    public function testCreateQueryWithLesson()
    {
        $expectedLesson = new Lesson(
            1,
            2,
            3,
            new \DateTime(),
            new \DateTime(),
        );

        $data = [
            'lesson' => $expectedLesson,
        ];

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertEquals($expectedLesson, $result->lesson);
    }

    public function testCreateQueryWithInstructor()
    {
        $expectedInstructor = new Instructor(
            3,
            'expectedInstructorFirstname',
            'expectedInstructorLastname'
        );

        $data = [
            'instructor' => $expectedInstructor,
        ];

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertEquals($expectedInstructor, $result->instructor);
    }

    public function testCreateQueryWithLearner()
    {
        $expectedLearner = new Learner(
            2,
            'expectedFirstname',
            'expectedLastname',
            'expectedEmail'
        );

        $data = [
            'user' => $expectedLearner,
        ];

        $this->applicationContext->getCurrentUser()
            ->shouldNotBeCalled();

        $result = (new QueryService($this->applicationContext->reveal()))->createQuery($data);

        $this->assertEquals($expectedLearner, $result->learner);
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
