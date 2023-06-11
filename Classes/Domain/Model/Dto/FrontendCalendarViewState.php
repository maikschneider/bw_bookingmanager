<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Psr\Http\Message\ServerRequestInterface;

class FrontendCalendarViewState
{
    public ?string $start;

    public ?string $end;

    public int $calendar;

    public static function createFromApiRequest(ServerRequestInterface $request): FrontendCalendarViewState
    {
        $params = $request->getQueryParams();

        $state = new self();
        $state->start = $params['start'];
        $state->end = $params['end'];

        return $state;
    }

    public function getStartDate(): \DateTime
    {
        return new \DateTime($this->start);
    }

    public function getEndDate(): \DateTime
    {
        return new \DateTime($this->end);
    }
}
