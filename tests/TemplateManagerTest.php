<?php

namespace Test;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\MeetingPoint;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\Service\LessonRenderer;
use App\TemplateManager;
use PHPUnit\Framework\TestCase;

class TemplateManagerTest extends TestCase
{
    private InstructorRepository $instructorRepository;

    private MeetingPointRepository $meetingPointRepository;

    private ApplicationContext $applicationContext;

    private LessonRepository $lessonRepository;

    private LessonRenderer $lessonRenderer;

    /**
     * Init the mocks
     */
    public function setUp(): void
    {
        $this->instructorRepository = new InstructorRepository();
        $this->instructorRepository->save(new Instructor(1, "jean", "rock"));


        $this->meetingPointRepository = new MeetingPointRepository();
        $this->meetingPointRepository->save(new MeetingPoint(1, "http://lambda.to", "paris 5eme"));

        $this->applicationContext = new ApplicationContext();
        $this->applicationContext->setCurrentUser(new Learner(1, "toto", "bob", "toto@bob.to"));

        $this->lessonRepository = new LessonRepository();

        $this->lessonRenderer = new LessonRenderer();
    }

    /**
     * Closes the mocks
     */
    public function tearDown(): void
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $expectedInstructor = $this->instructorRepository->getById(1);
        $expectedMeetingPoint = $this->meetingPointRepository->getById(1);
        $expectedUser = $this->applicationContext->getCurrentUser();
        $start_at = new \DateTime("2021-01-01 12:00:00");
        $end_at = $start_at->add(new \DateInterval('PT1H'));

        $lesson = new Lesson(1, 1, 1, $start_at, $end_at);
        $this->lessonRepository->save($lesson);

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
            $this->applicationContext,
            $this->lessonRepository,
            $this->meetingPointRepository,
            $this->instructorRepository,
            $this->lessonRenderer
        );

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson
            ]
        );

        $this->assertEquals('Votre leçon de conduite avec ' . $expectedInstructor->firstname, $message->subject);
        $this->assertEquals("
Bonjour Toto,

La reservation du " . $start_at->format('d/m/Y') . " de " . $start_at->format('H:i') . " à " . $end_at->format('H:i') . " avec " . $expectedInstructor->firstname . " a bien été prise en compte!
Voici votre point de rendez-vous: " . $expectedMeetingPoint->name . ".

Bien cordialement,

L'équipe Ornikar
", $message->content);
    }
}
