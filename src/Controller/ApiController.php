<?php

namespace App\Controller;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $entityManager;

    /**
     * @param $entityManager
     */public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    ## permet de mettre à jour l'evenement du calendrier
    #[Route('/api/{id}/edit', name: 'app_api_event_edit', methods: ['PUT'])]
    public function majEvent(?Calendar $calendar, Request $request, EntityManagerInterface $em): Response
    {
        //on recupere les données de fullCalendar
        $donnees = json_decode($request->getContent());

        //on verifie qu'on a toutes les infos nécessaire
        if (
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->end) && !empty($donnees->end) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ) {
            //les données sont complètes
            // on initialise un code
            $code = 200;

            //on voit si l'id existe
            if (!$calendar) {
                // on instancie un rdv
                $calendar = new Calendar();

                //on change le code
                $code = 201;
            }

            //on hydrate l'objet avec nos données
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new \DateTime($donnees->start));
            if ($donnees->allDay) {
                $calendar->setEnd(new \DateTime($donnees->start));
            }else{
                $calendar->setEnd(new \DateTime($donnees->end));
            }
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);

            $em->persist($calendar);
            $em->flush();

            //on retourne un code
            return new Response('ok', $code);
        }else{
            //les données sont incomplètes
            return  new Response('données incomplètes', 404);
        };

        return $this->render('api/index.html.twig');
    }
}
