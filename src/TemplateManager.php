<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Instructor;
use App\Entity\Learner;
use App\Entity\Lesson;
use App\Entity\Template;
use App\Repository\InstructorRepository;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\Service\LessonRenderer;

class TemplateManager
{
    public function __construct(
        private readonly ApplicationContext $applicationContext,
        private readonly LessonRepository $lessonRepository,
        private readonly MeetingPointRepository $meetingPointRepository,
        private readonly InstructorRepository $instructorRepository,
        private readonly LessonRenderer $lessonRenderer,
    ) {
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $lesson = (isset($data['lesson']) and $data['lesson'] instanceof Lesson) ? $data['lesson'] : null;

        if ($lesson) {
            $_lessonFromRepository = $this->lessonRepository->getById($lesson->id);
            $meetingPoint = $this->meetingPointRepository->getById($lesson->meetingPointId);
            $instructor = $this->instructorRepository->getById($lesson->instructorId);

            if (strpos($text, '[lesson:instructor_link]') !== false) {
                $text = str_replace('[instructor_link]', 'instructors/' . $instructor->id .'-'.urlencode($instructor->firstname), $text);
            }

            $containsSummaryHtml = strpos($text, '[lesson:summary_html]');
            $containsSummary     = strpos($text, '[lesson:summary]');

            if ($containsSummaryHtml !== false) {
                $text = str_replace(
                    '[lesson:summary_html]',
                    $this->lessonRenderer->renderHtml($_lessonFromRepository),
                    $text
                );
            }
            if ($containsSummary !== false) {
                $text = str_replace(
                    '[lesson:summary]',
                    $this->lessonRenderer->renderText($_lessonFromRepository),
                    $text
                );
            }

            $text = str_replace('[lesson:instructor_name]', $instructor->firstname, $text);
        }

        if ($lesson->meetingPointId) {
            if (strpos($text, '[lesson:meeting_point]') !== false) {
                $text = str_replace('[lesson:meeting_point]', $meetingPoint->name, $text);
            }
        }

        if (strpos($text, '[lesson:start_date]') !== false) {
            $text = str_replace('[lesson:start_date]', $lesson->start_time->format('d/m/Y'), $text);
        }

        if (strpos($text, '[lesson:start_time]') !== false) {
            $text = str_replace('[lesson:start_time]', $lesson->start_time->format('H:i'), $text);
        }

        if (strpos($text, '[lesson:end_time]') !== false) {
            $text = str_replace('[lesson:end_time]', $lesson->end_time->format('H:i'), $text);
        }


        if (isset($data['instructor'])  and ($data['instructor']  instanceof Instructor)) {
            $text = str_replace('[instructor_link]', 'instructors/' . $data['instructor']->id .'-'.urlencode($data['instructor']->firstname), $text);
        } else {
            $text = str_replace('[instructor_link]', '', $text);
        }

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof Learner)) ? $data['user'] : $this->applicationContext->getCurrentUser();
        if ($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
