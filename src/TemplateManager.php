<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Template;
use App\Entity\Query;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\Service\LessonRenderer;
use App\Service\QueryService;

class TemplateManager
{
    public function __construct(
        private readonly LessonRepository $lessonRepository,
        private readonly MeetingPointRepository $meetingPointRepository,
        private readonly InstructorRepository $instructorRepository,
        private readonly LessonRenderer $lessonRenderer,
        private readonly QueryService $queryService,
    ) {
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        $query = $this->queryService->createQuery($data);

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $query);
        $replaced->content = $this->computeText($replaced->content, $query);

        return $replaced;
    }

    private function computeText($text, Query $query)
    {
        if ($query->lesson) {
            $meetingPoint = $this->meetingPointRepository->getById($query->lesson->meetingPointId);
            $instructor = $this->instructorRepository->getById($query->lesson->instructorId);

            if (strpos($text, '[lesson:instructor_link]') !== false) {
                $text = str_replace('[instructor_link]', 'instructors/' . $instructor->id .'-'.urlencode($instructor->firstname), $text);
            }

            $text = str_replace(
                '[lesson:summary_html]',
                $this->lessonRenderer->renderHtml($query->lesson),
                $text
            );
            $text = str_replace(
                '[lesson:summary]',
                $this->lessonRenderer->renderText($query->lesson),
                $text
            );

            $text = str_replace('[lesson:instructor_name]', $instructor->firstname, $text);

            $text = str_replace('[lesson:meeting_point]', $meetingPoint->name, $text);
        }

        $text = str_replace('[lesson:start_date]', $query->lesson->start_time->format('d/m/Y'), $text);

        $text = str_replace('[lesson:start_time]', $query->lesson->start_time->format('H:i'), $text);

        $text = str_replace('[lesson:end_time]', $query->lesson->end_time->format('H:i'), $text);

        $instructorReplacement = '';

        if ($query->instructor) {
            $instructorReplacement = 'instructors/' . $query->instructor->id .'-'.urlencode($query->instructor->firstname);
        }

        $text = str_replace('[instructor_link]', $instructorReplacement, $text);

        $text = str_replace('[user:first_name]', ucfirst(strtolower($query->learner->firstname)), $text);

        return $text;
    }
}
