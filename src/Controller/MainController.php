<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(CalendarRepository $calendarRepository): Response
    {
        $events = $calendarRepository->findAll();

        foreach ($events as $event) {
            $reservations[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'start' => $event->getStart()->format('Y-m-d'),
                'end' => $event->getEnd()->format('Y-m-d'),
                'description' => $event->getDescription(),
                'allDay' => $event->isAllDay(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
            ];
        }

        $data = json_encode($reservations);

        return $this->render('main/index.html.twig', compact('data'));
    }
}
