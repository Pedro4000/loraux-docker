<?php

namespace App\Controller;

use App\Entity\{Artist, Label, Release, User};
use App\Form\UserType;
use App\Service\{CalendarService, DiscogsService, MailToNewMember};
use Doctrine\Persistence\ManagerRegistry;
use Google_Client;
use Google_Service_Calendar;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RequestStack, Request, JsonResponse};
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Google\Service\ToolResults\PendingGoogleUpdateInsight;
use App\Entity\PendingYoutubeTask;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ArtistRepository;

class MusicController extends AbstractController
{
    private $params;
    private $client;
    public $em;


    public function __construct (Google_Client $client, ParameterBagInterface $params, DiscogsService $discogsService, RequestStack $requestStack)
    {
        $this->client = $client;
        $this->params = $params;
        $discogsService = $discogsService;
        $this->requestStack = $requestStack;
        $this->session = $this->requestStack->getSession();

    }

    
    #[Route('/', name: 'index')]
    public function indexAction(Request $request, RequestStack $requestStack, ManagerRegistry $doctrine): Response
    {
        $client = new Client();
        $session = $requestStack->getSession();
        $discogsCredentials = 'key='.$this->getParameter('discogs_consumer_key').'&secret='.$this->getParameter('discogs_consumer_secret');
        $baseDiscogsApi = 'https://api.discogs.com/';
        $responseContents = [];
        $videosArray = [];
        $discogsQueryInfos = [];
        $guzzleException='';


        /* auth of type "https://api.discogs.com/database/search?q=Nirvana&key=foo123&secret=bar456" */

        // PREMIERE RECHERCHE AFIN DE TROUVER LOBJET VOULU
        if ($request->get('query-discogs')) {
            $session->set('discogsQueryResult','');
            $discogsQuery = $request->get('query-discogs');
            $res = $client->request('GET', $baseDiscogsApi.'/database/search?q='.$discogsQuery.'&'.$discogsCredentials);
            $responseContents = json_decode($res->getBody()->getContents(), true);

            if (!empty($responseContents['results'])) {
                $session->set('discogsQueryResult',$responseContents);
                $discogsQueryInfos =
                    [
                        'pages' => $responseContents['pagination']['pages'],
                        'totalLength' => count($responseContents['results']),
                    ];
            };
        }

        return $this->render('index.html.twig',[
            'discogsQueryInfos'=> $discogsQueryInfos,
            'img'=> $responseContents['results'][0]['cover_image'] ?? '',
            'responseContents'=> $responseContents,
            'videosArray'=> $videosArray,
            'guzzleException' =>$guzzleException
        ]);

    }

    #[Route('/ajaxSaveReleasesInDB', name: 'ajaxSaveReleasesInDB')]
    public function ajaxSaveReleasesInDBAction(Request $request, ManagerRegistry $doctrine, DiscogsService $discogsService)
    {
        $discogsId = $request->query->get('discogsId');
        $em = $doctrine->getManager();
        $typeDiscogsQuery = $request->query->get('typeDiscogs');
        $discogsCredentials = 'key='.$this->getParameter('discogs_consumer_key').'&secret='.$this->getParameter('discogs_consumer_secret');
        $baseDiscogsApi = 'https://api.discogs.com/';
        $releasesContent = [];
        $videosArray = [];
        $guzzleException= '';
        $videosArray['releases'] = [];
        $this->session->set('videosToPutInPlaylist','');

        // SI ON A DU CONTENU ALORS ON VA LISTER LES RELEASE PAR TYPE DOBJET
        $client = new Client();
        if ($typeDiscogsQuery == 'label') {
            $label = $discogsService->scrapDiscogsLabel($discogsId);
        } elseif ($typeDiscogsQuery == 'artist') {
            $artist = $discogsService->scrapDiscogsArtist($discogsId);
        }

        return new JsonResponse('cool');
    }


    /**
     * @Route("/sign_up", name="sign_up")
     * @param Request $request
     * @param MailToNewMember $mailToNewMember
     * @param MailerInterface $mailerInterface
     * @param CalendarService $calendarService
     * @param ParameterBagInterface $params
     * @return Response
     */
    public function signUpAction(Request $request, MailToNewMember $mailToNewMember, MailerInterface $mailerInterface, CalendarService $calendarService, ManagerRegistry $doctrine)
    {
//        $mailToNewMember->sendMailToNewMember($mailerInterface);

        $em = $doctrine->getManager();
        $newUser = new User();
        $userForm = $this->createForm(UserType::class, $newUser, [
            'action' => $this->generateUrl('sign_up'),
            'method' => 'POST',
        ] );

        $userForm->handleRequest($request);

        // On traite le formulaire et on vient enregister le nouvel utilisateur
        if ($userForm->isSubmitted() && $userForm->isValid()  ) {
            $userForm->getData();
            $newUser->setIsMailAddressVerified(false);
            $em->persist($newUser);
            $em->flush();
        }

/*        $finder = new Finder();
        $finder->in(__DIR__.'/../..');
        $credentialsFiles = $finder->files()->name('credentials.json');
        foreach ($credentialsFiles as $credentialsFile) {
            $absoluteFilePathCredentials = $credentialsFile->getRealPath();
        }*/

        return $this->render('signup.html.twig', [
            'number' => 3,
            'userForm'=> $userForm->createView()
        ]);
    }


    #[Route('/music/artist/index', name: 'music.artist_index')]
    public function artistIndex(ManagerRegistry $doctrine, ArtistRepository $artistRepository) {
        
        $page = $_GET['page'] ?? 1;
        $size = 50;

        $params = compact('page', 'size');

        $artists = $artistRepository->getArtistsByParams($params);

        return $this->render('artist/index.html.twig',[
            'artists' => $artists,
        ]);
    }


    #[Route('/music/label/index', name: 'music.label_index')]
    public function labelIndex() {
        die('ok');
    }
    

    /**
     * @Route("/google_redirect_for_calendar", name="google_redirect_for_calendar")
     * @param Request $request
     * @param CalendarService $calendarService
     */
    public function getGoogleCalendarRedirectInformationAction (Request $request, CalendarService $calendarService) {

        $request->getPathInfo();
        $this->session->set('auth_code',$request->query->get('code'));
        $google_code = $request->query->get('code');
        $dateTimeNow = new \DateTime();
        $dateTime3339 = $dateTimeNow->format("Y-m-d\TH:i:sP");

        // Set up the google client with the general info
        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $client->setRedirectUri('http://127.0.0.1:8000/google_redirect_for_calendar');

        // if an access token has already been set in session
        if ($this->session->get('access_token')) {

            $client->setAccessToken($this->session->get('access_token'));
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                }
                else {
                    if ($request->query->get('code')){
                        $accessToken = $client->fetchAccessTokenWithAuthCode($google_code);
                        $client->setAccessToken($accessToken);
                        $this->session->set('access_token',$accessToken);
                    }
                }
                $service = new Google_Service_Calendar($client);
                $calendarId = 'primary';
                $optionsForListEvent = [
                    'timeMin' => $dateTime3339
                ];
                $results = $service->events->listEvents($calendarId, $optionsForListEvent);
            }
        }
        elseif ($request->query->get('code')) {
            $accessToken = $client->fetchAccessTokenWithAuthCode($google_code);
            $client->setAccessToken($accessToken);
            $this->session->set('access_token',$accessToken);
        }
        else {
            $authUrl = $client->createAuthUrl();
            return $this->redirect($authUrl);
        }

        $service = new Google_Service_Calendar($client);
        $calendarId = 'primary';
        $optionsForListEvent = [
            'timeMin' => $dateTime3339
        ];
        $results = $service->events->listEvents($calendarId, $optionsForListEvent);

        return $this->render('events.html.twig', [
            'events' => $results
        ]);;
    }



    /**
     * @Route("/resume", name="resume")
     */
    public function resume()
    {
        return;
    }

}
