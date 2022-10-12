<?php

namespace App\Controller;

use App\Entity\{Artist, Label, Release, User};
use App\Form\UserType;
use App\Service\{CalendarService, DiscogsService, MailToNewMember};
use Doctrine\Persistence\ManagerRegistry;
use Google_Client;
use Google_Service_Calendar;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RequestStack, Request, JsonResponse};
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\{ArtistRepository, LabelRepository};
use Symfony\Component\Form\Extension\Core\Type\{NumberType, SubmitType};

class MusicController extends AbstractController
{

    public function __construct (
        private Google_Client $client, 
        private ParameterBagInterface $params, 
        private DiscogsService $discogsService, 
        private RequestStack $requestStack
    ) { }

    
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
    public function ajaxSaveReleasesInDBAction(Request $request, ManagerRegistry $doctrine, DiscogsService $discogsService, RequestStack $requestStack)
    {
        $discogsId = $request->query->get('discogsId');
        $em = $doctrine->getManager();
        $typeDiscogsQuery = $request->query->get('typeDiscogs');
        $session = $requestStack->getSession();
        $session->set('videosToPutInPlaylist','');

        // SI ON A DU CONTENU ALORS ON VA LISTER LES RELEASE PAR TYPE DOBJET
        $client = new Client();
        if ($typeDiscogsQuery == 'label') {
            $response = $discogsService->scrapDiscogsLabel($discogsId);
        } elseif ($typeDiscogsQuery == 'artist') {
            $response = $discogsService->scrapDiscogsArtist($discogsId);
        }
        return new JsonResponse($response);
    }


    /**
     * @Route("/sign_up", name="sign_up")
     * @param Request $request
     * @param MailToNewMember $mailToNewMember
     * @param MailerInterface $mailerInterface
     * @param CalendarService $calendarService
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
            $absoluteFilePathCredentials = $credenstialsFile->getRealPath();
        }*/

        return $this->render('signup.html.twig', [
            'number' => 3,
            'userForm'=> $userForm->createView()
        ]);
    }


    #[Route('/music/artist/index', name: 'music.artist.index')]
    public function artistIndex(ManagerRegistry $doctrine, ArtistRepository $artistRepository) 
    {
        
        $page = $_GET['page'] ?? 1;
        $size = 20;

        $params = compact('page', 'size');

        $artists = $artistRepository->getArtistsByParams($params);

        return $this->render('artist/index.html.twig',[
            'artists' => $artists,
        ]);
    }

    #[Route('/music/artist/show/{id}', name: 'music.artist.show')]
    public function artistShow(int $id, ManagerRegistry $doctrine, ArtistRepository $artistRepository, DiscogsService $discogsService) 
    {
        $discogsCredentials = 'key='.$this->getParameter('discogs_consumer_key').'&secret='.$this->getParameter('discogs_consumer_secret');
        $baseDiscogsApi = 'https://api.discogs.com/';

        $artist = $artistRepository->find($id);
        $releases = $artist->getReleases();
        $videos = [];
        $videosString = '';
        foreach ($releases as $release) {
            foreach ($release->getDiscogsVideos() as $video) {
                $video->youtubeId = $discogsService->getDiscogsVideosURIToYoutubeId($video->getUrl());
                $videosString .= $video->youtubeId.', ';
                $videos[] = $video;
            }
        }
        $videosString = substr(trim($videosString), 0 , -1);
     
        return $this->render('video/player.html.twig',[
            'artist' => $artist,
            'videos' => $videos,
            'videosString' =>  $videosString,
        ]);
    }

    /**
     * @Route("/google_redirect_for_calendar", name="google_redirect_for_calendar")
     * @param Request $request
     * @param CalendarService $calendarService
     */
    public function getGoogleCalendarRedirectInformationAction (Request $request, CalendarService $calendarService) {

        $request->getPathInfo();
        $session = $this->requestStack->getSession();
        $session->set('auth_code',$request->query->get('code'));
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
        if ($session->get('access_token')) {

            $client->setAccessToken($session->get('access_token'));
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                }
                else {
                    if ($request->query->get('code')){
                        $accessToken = $client->fetchAccessTokenWithAuthCode($google_code);
                        $client->setAccessToken($accessToken);
                        $session->set('access_token',$accessToken);
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
            $session->set('access_token',$accessToken);
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
