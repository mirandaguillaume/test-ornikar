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
        $toReplaceArray = [
            '[user:first_name]',
            '[instructor_link]'
        ];

        $replacementArray = [
            ucfirst(strtolower($query->learner->firstname)),
            $query->instructor ? 'instructors/' . $query->instructor->id .'-'.urlencode($query->instructor->firstname) : '',
        ];

        if ($query->lesson) {
            $meetingPoint = $this->meetingPointRepository->getById($query->lesson->meetingPointId);
            $instructor = $this->instructorRepository->getById($query->lesson->instructorId);

            $toReplaceArray = array_merge($toReplaceArray, [
                '[lesson:summary_html]',
                '[lesson:summary]',
                '[lesson:instructor_name]',
                '[lesson:meeting_point]',
                '[lesson:start_date]',
                '[lesson:start_time]',
                '[lesson:end_time]',
            ]);

            $replacementArray = array_merge($replacementArray, [
                $this->lessonRenderer->renderHtml($query->lesson),
                $this->lessonRenderer->renderText($query->lesson),
                $instructor->firstname,
                $meetingPoint->name,
                $query->lesson->start_time->format('d/m/Y'),
                $query->lesson->start_time->format('H:i'),
                $query->lesson->end_time->format('H:i'),
            ]);

            if (strpos($text, '[lesson:instructor_link]') !== false) {
                $toReplaceArray[] = '[instructor_link]';
                $replacementArray[] = 'instructors/' . $instructor->id .'-'.urlencode($instructor->firstname);
            }
        }

        return str_replace(
            $toReplaceArray,
            $replacementArray,
            $text
        );;
    }
}
