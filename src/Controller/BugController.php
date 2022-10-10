<?php

namespace App\Controller;

use App\Entity\{Artist, Label, Release, User};
use App\Form\UserType;
use App\Service\{CalendarService, DiscogsService, MailToNewMember};
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RequestStack, Request, JsonResponse};
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\{ArtistRepository, LabelRepository};

class BugController extends AbstractController
{

    public function __construct (
        private ParameterBagInterface $params, 
        private DiscogsService $discogsService, 
        private RequestStack $requestStack
    ) { }

    
    #[Route('/generate429', name: 'generate429')]
    public function generate429(Request $request, RequestStack $requestStack, ManagerRegistry $doctrine): mixed
    {
        $client = new Client();
        $discogsCredentials = 'key='.$this->getParameter('discogs_consumer_key').'&secret='.$this->getParameter('discogs_consumer_secret');
        $baseDiscogsApi = 'https://api.discogs.com/';

        $discogsQuery = "hatman";

        for ($i=0; $i < 200; $i++) {
            try{
                $res = $client->request('GET', $baseDiscogsApi.'/database/search?q='.$discogsQuery.'&'.$discogsCredentials);
            } catch (ClientException $e) {
                dd($e->getResponse()->getStatusCode());
            }
        }    
        return 0;
    }

}
