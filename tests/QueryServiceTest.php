<?php

namespace Test;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\MeetingPoint;
use App\Repository\InstructorRepository;
use App\Repository\MeetingPointRepository;
use App\Service\QueryService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class QueryServiceTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $applicationContext;

    private ObjectProphecy $instructorRepository;

    private ObjectProphecy $meetingPointRepository;

    private Learner $contextLearner;

    private QueryService $queryService;

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

        $this->instructorRepository = $this->prophesize(InstructorRepository::class);
        $this->meetingPointRepository = $this->prophesize(MeetingPointRepository::class);

        $this->queryService = new QueryService(
            $this->applicationContext->reveal(),
            $this->meetingPointRepository->reveal(),
            $this->instructorRepository->reveal(),
        );
    }

    public function testCreateQueryWithLesson()
    {
        $expectedInstructor = new Instructor(
            3,
            'expectedInstructor',
            'expectedLastname',
        );

        $expectedMeetingPoint = new MeetingPoint(
            2,
            'expectedUrl',
            'expectedName',
        );

        $expectedLesson = new Lesson(
            1,
            $expectedMeetingPoint->id,
            $expectedInstructor->id,
            new \DateTime(),
            new \DateTime(),
        );

        $data = [
            'lesson' => $expectedLesson,
        ];

        $this->instructorRepository
            ->getById($expectedInstructor->id)
            ->shouldBeCalledOnce()
            ->willReturn($expectedInstructor)
            ;

        $this->meetingPointRepository
            ->getById($expectedMeetingPoint->id)
            ->shouldBeCalledOnce()
            ->willReturn($expectedMeetingPoint)
        ;

        $result = $this->queryService->createQuery($data);

        $this->assertEquals($expectedLesson, $result->lesson);
        $this->assertEquals($expectedInstructor, $result->lessonInstructor);
        $this->assertEquals($expectedMeetingPoint, $result->lessonMeetingPoint);
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

        $result = $this->queryService->createQuery($data);

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

        $result = $this->queryService->createQuery($data);

        $this->assertEquals($expectedLearner, $result->learner);
    }

    public function testCreateQueryNoLesson()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [];

        $result = $this->queryService->createQuery($data);

        $this->assertNull($result->lesson);
    }

    public function testCreateQueryNoInstructor()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [];

        $result = $this->queryService->createQuery($data);

        $this->assertNull($result->instructor);
    }

    public function testCreateQueryNoLearner()
    {
        $lesson = $this->prophesize(Lesson::class);

        $data = [];

        $result = $this->queryService->createQuery($data);

        $this->assertEquals($this->contextLearner, $result->learner);
    }
}
