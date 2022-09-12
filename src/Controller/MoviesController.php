<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Release;
use App\Entity\User;
use App\Form\UserType;
use App\Service\CalendarService;
use App\Service\DiscogsService;
use App\Service\MailToNewMember;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Google_Client;
use Google_Service;
use Google_Service_Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Guzzle\Http\Exception\ClientErrorResponseException;
use GuzzleHttp\Exception\ClientException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;



class MoviesController extends AbstractController
{
    private $params;
    private $client;
    public $em;

    public function __construct (Google_Client $client, ParameterBagInterface $params, DiscogsService $discogsService, RequestStack $requestStack)
    {
        $this->client = $client;
        $this->params = $params;
        $this->requestStack = $requestStack;
        $this->session = $this->requestStack->getSession();
        $this->discogsService = $discogsService;
    }


    /**
     * @Route("/movies", name="movies")
     * @param Request $request
     */
    public function MoviesAction(Request $request)
    {
        $session = $this->requestStack->getSession();
    }

}
