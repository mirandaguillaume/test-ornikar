<?php

namespace Test;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\MeetingPoint;
use App\Entity\Query;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\Service\LessonRenderer;
use App\Service\QueryService;
use App\TemplateManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class TemplateManagerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $lessonRenderer;

    private ObjectProphecy $queryService;

    /**
     * Init the mocks
     */
    public function setUp(): void
    {
        $this->lessonRenderer = $this->prophesize(LessonRenderer::class);
        $this->queryService = $this->prophesize(QueryService::class);
    }

    /**
     * @test
     */
    public function test()
    {
        $expectedInstructor = new Instructor(1, "jean", "rock");
        $expectedMeetingPoint = new MeetingPoint(1, "http://lambda.to", "paris 5eme");
        $expectedLearner = new Learner(1, "toto", "bob", "toto@bob.to");

        $start_at = new \DateTime("2021-01-01 12:00:00");
        $end_at = $start_at->add(new \DateInterval('PT1H'));

        $lesson = new Lesson(1, $expectedMeetingPoint->id, $expectedInstructor->id, $start_at, $end_at);

        $expectedQuery = new Query();
        $expectedQuery->lesson = $lesson;
        $expectedQuery->lessonInstructor = $expectedInstructor;
        $expectedQuery->learner = $expectedLearner;
        $expectedQuery->lessonMeetingPoint = $expectedMeetingPoint;

        $this->queryService->createQuery([
            'lesson' => $lesson
            ])
            ->shouldBeCalledOnce()
            ->willReturn($expectedQuery)
        ;

        $this->lessonRenderer
            ->renderHtml(Argument::type(Lesson::class))
            ->shouldBeCalledOnce()
            ->willReturn('renderedHtml')
        ;

        $this->lessonRenderer
            ->renderText(Argument::type(Lesson::class))
            ->shouldBeCalledOnce()
            ->willReturn('renderedText')
        ;

        $template = new Template(
            1,
            'Votre leçon de conduite avec [lesson:instructor_name]',
            "
Bonjour [user:first_name],

La reservation du [lesson:start_date] de [lesson:start_time] à [lesson:end_time] avec [lesson:instructor_name] a bien été prise en compte!
Voici votre point de rendez-vous: [lesson:meeting_point].

Bien cordialement,

L'équipe Ornikar
"
        );

        $templateManager = new TemplateManager(
            $this->lessonRenderer->reveal(),
            $this->queryService->reveal(),
        );

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson
            ]
        );

        $this->assertEquals("Votre leçon de conduite avec {$expectedInstructor->firstname}", $message->subject);
        $this->assertEquals("
Bonjour Toto,

La reservation du {$start_at->format('d/m/Y')} de {$start_at->format('H:i')} à {$end_at->format('H:i')} avec {$expectedInstructor->firstname} a bien été prise en compte!
Voici votre point de rendez-vous: {$expectedMeetingPoint->name}.

Bien cordialement,

L'équipe Ornikar
", $message->content);
    }
}
