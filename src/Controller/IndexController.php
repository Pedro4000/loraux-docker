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

class IndexController extends AbstractController
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

        $metadata = $doctrine->getManager()->getClassMetadata(Label::class)->getAssociationNames();
        dd($metadata);
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
            $queryString = $request->get('query-discogs');
            $res = $client->request('GET', $baseDiscogsApi.'/database/search?q='.$queryString.'&'.$discogsCredentials);
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
            'discogsQueryInfos'=>$discogsQueryInfos,
            'img'=> $responseContents['results'][0]['cover_image'] ?? '',
            'responseContents'=>$responseContents,
            'videosArray'=>$videosArray,
            'guzzleException'=>$guzzleException
        ]);

    }

    #[Route('/ajaxLoadVideos', name: 'ajaxLoadVideos')]
    public function ajaxLoadVideosAction(Request $request, ManagerRegistry $doctrine, DiscogsService $discogsService)
    {
        $idDiscogs = $request->query->get('idDiscogs');
        $em = $doctrine->getManager();
        $typeDiscogsQuery = $request->query->get('typeDiscogs');
        $discogsCredentials = 'key='.$this->getParameter('discogs_consumer_key').'&secret='.$this->getParameter('discogs_consumer_secret');
        $baseDiscogsApi = 'https://api.discogs.com/';
        $recArray = [];
        $videosArray = [];
        $guzzleException= '';
        $videosArray['releases'] = [];
        $this->session->set('videosToPutInPlaylist','');


        // SI ON A DU CONTENU ALORS ON VA LISTER LES RELEASE PAR TYPE DOBJET
        $client = new Client();
        if ($typeDiscogsQuery == 'label') {
            $labelReleasesResponse = $client->request('GET', $baseDiscogsApi.'labels/'.$idDiscogs.'/releases?'.$discogsCredentials.'&per_page=50');
            $labelInfosResponse = $client->request('GET', $baseDiscogsApi.'labels/'.$idDiscogs.'?'.$discogsCredentials);

            if (!$labelInfosResponse->getStatusCode() == 200 || !$labelReleasesResponse->getStatusCode() == 200) {
                return new JsonResponse([
                    'status_code' => $labelReleasesResponse->getStatusCode(),
                    'status' => 'error',
                ]);
            };

            $labelReleasesContent = json_decode($labelReleasesResponse->getBody()->getContents(), true);
            $labelInfosContent = json_decode($labelInfosResponse->getBody()->getContents(), true);
            $labelRepository = $doctrine->getRepository(Label::class);

            dd($doctrine->getRepository(Artist::class)->findBy([
                'discogsId' => 4
            ]));

            if (!$doctrine->getRepository(Label::class)->findOneBy([ 'discogsId' => $labelInfosContent['id']])) {
                $discogsService->createNewLabel($labelInfosContent['id'], $labelInfosContent['name']);
            };
            $labelFromDb = $doctrine->getRepository(Label::class)->findOneBy([ 'discogsId' => $labelInfosContent['id']]);

        } elseif ($typeDiscogsQuery == 'artist') {
            $responseDiscogs = $client->request('GET', $baseDiscogsApi.'artists/'.$idDiscogs.'/releases?'.$discogsCredentials);
            $artistInfos = $client->request('GET', $baseDiscogsApi.'artists/'.$idDiscogs.'?'.$discogsCredentials);
            $artistInfos = json_decode($artistInfos->getBody()->getContents(),true);
            $recArray = json_decode($responseDiscogs->getBody()->getContents(),true);

            if (!$doctrine
                ->getRepository(Artist::class)
                ->findOneBy([ 'discogsId' => $artistInfos['id']])) {
                $newArtist = new Artist();
                $newArtist->setName($artistInfos['name']);
                $newArtist->setDiscogsId($artistInfos['id']);
                $em->persist($newArtist);
                $em->flush();
            };

        }


        $now = new \DateTime();
        if ($labelFromDb->getLastTimeFullyScraped()) {
            if ($now->diff($labelFromDb->getLastTimeFullyScraped())->days < 85) {
                $allReleases  = $labelFromDb->getReleases();
                foreach ($allReleases as $release) {
                    foreach ($release->getVideos() as $video){
                        if (!in_array($video,$videosArray)){
                            array_push($videosArray, $video);
                        }
                    }
                }
            }
            return new JsonResponse(['', $videosArray]);
        }


        // ICI ON VIENT CHERCHER LES VIDEOS UNES A UNES
        if (!empty($recArray)) {
            for ($j=1; $j<= $recArray['pagination']['pages']; $j++) {
                if ($j<>1) {
                    $resSpec = $client->request('GET', $baseDiscogsApi.'labels/'.$id.'/releases?'.$discogsCredentials.'&page='.$j.'&per_page=50');
                    $recArray = json_decode($resSpec->getBody()->getContents(),true);
                }
                $i=0;
                foreach ($recArray['releases'] as $release) {
                    $i++;
                    // si la release ne contient pas de track ou n'est pas présente en db on vient la créer
                    if (!$doctrine
                            ->getRepository(Release::class)
                            ->findOneBy([ 'discogsId' => $release['id']]) || $doctrine->getRepository(Release::class)->findOneBy([ 'discogsId' => $release['id']])->getTracks()->isEmpty()) {
                        try{
                            $client = new Client();
                            $resSpec = $client->request('GET', $baseDiscogsApi.'releases/'.$release['id'],
                                ['exceptions' => false]
                            );
                            $releaseInfos = json_decode($resSpec->getBody()->getContents(),true);

                            if ($releaseInfos['artists'][0]['name']=='Various') {
                                $artistsIdArray = [];
                                $artistIdNameArray = [];
                                // On stocke chaque artiste une seule fois dans un tableau
                                foreach ($releaseInfos['tracklist'] as $track) {
                                    if (!array_key_exists('artists', $track)) {
                                        continue;
                                    }
                                    foreach ($track['artists'] as $trackArtist) {
                                        if (!in_array($trackArtist['id'], $artistsIdArray)) {
                                            array_push($artistsIdArray, $trackArtist['id']);
                                            array_push($artistIdNameArray, ['id' => $trackArtist['id'], 'name' =>$trackArtist['name']]);
                                        }
                                    }
                                }
                                $releaseArtists = [] ;
                                foreach ($artistIdNameArray as $artist) {
                                    if (!$doctrine
                                        ->getRepository(Artist::class)
                                        ->findOneBy([ 'discogsId' => $artist['id']])) {
                                        $discogsService->createNewArtist($artist['id'], $artist['name']);
                                    }
                                    $releaseArtist = $doctrine->getRepository(Artist::class)->findOneBy([ 'discogsId' => $artist['id']]);
                                    array_push($releaseArtists, $releaseArtist);
                                }
                                if (!$doctrine->getRepository(Release::class)->findOneBy([ 'discogsId' => $releaseInfos['id']]) || $doctrine->getRepository(Release::class)->findOneBy([ 'discogsId' => $releaseInfos['id']])->getTracks()->isEmpty()) {
                                    $releaseInfos = $discogsService->setArrayKeyToNullIfNonExistent($releaseInfos);
                                    $discogsService->createNewRelease($releaseInfos['id'], $releaseInfos['title'], $releaseInfos['released'], $releaseInfos['videos'], $labelFromDb, $releaseArtists);
                                    $release = $doctrine->getRepository(Release::class)->findOneBy(['discogsId' => $releaseInfos['id']]);

                                    $trackArtists = [];
                                    foreach ($releaseInfos['tracklist'] as $track) {
                                        if (!array_key_exists('artists', $track)) {
                                            continue;
                                        }
                                        foreach ($track['artists'] as $artist) {
                                            foreach ($releaseArtists as $a) {
                                                if ($a->getDiscogsID()==$artist['id']) {
                                                    array_push($trackArtists, $a);
                                                }
                                            }
                                        }
                                        $discogsService->createNewTrack($track, $trackArtists, $release, $labelFromDb);
                                    }
                                }
                            }
                            else
                            {
                                $artistInfos = $client->request('GET', $baseDiscogsApi . 'artists/' . $releaseInfos['artists']['0']['id'] . '?' . $discogsCredentials);
                                $artistInfos = json_decode($artistInfos->getBody()->getContents(), true);
                                //on créer l'artiste si inexistant
                                if (!$doctrine
                                    ->getRepository(Artist::class)
                                    ->findOneBy([ 'discogsId' => $releaseInfos['artists'][0]['id']])) {
                                    $discogsService->createNewArtist($releaseInfos['artists'][0]['id'], $artistInfos['name']);
                                }
                                $releaseArtist = $doctrine->getRepository(Artist::class)
                                    ->findOneBy(['discogsId' => $releaseInfos['artists'][0]['id']]);
                                //puis la release
                                if (!$doctrine->getRepository(Release::class)->findOneBy([ 'discogsId' => $releaseInfos['id']])
                                    || $doctrine->getRepository(Release::class)->findOneBy([ 'discogsId' => $releaseInfos['id']])->getTracks()->isEmpty()
                                ){
                                    $releaseInfos = $discogsService->setArrayKeyToNullIfNonExistent($releaseInfos);
                                    $discogsService->createNewRelease($releaseInfos['id'], $releaseInfos['title'], $releaseInfos['released'], $releaseInfos['videos'], $labelFromDb, $releaseArtist);
                                    $release = $doctrine->getRepository(Release::class)->findOneBy(['discogsId' => $releaseInfos['id']]);
                                    foreach ($releaseInfos['tracklist'] as $track) {
                                        $discogsService->createNewTrack($track, $releaseArtist, $release, $labelFromDb);

                                    }
                                }
                            }
                       if ($i == count($recArray['releases']) && $j == $recArray['pagination']['pages']) {
                            $now = new \Datetime();
                            $labelFromDb->setLastTimeFullyScraped($now);
                            $em->persist($labelFromDb);
                            $em->flush();
                        }

                            sleep(2);
                        } catch (ClientException $exception) {
                            $guzzleException = $exception->getMessage();
                            sleep(10);
                        }
                    }




                    $this->session->set('videosToPutInPlaylist',$videosArray);
                }
            }

        }

        if ($recArray['pagination']['items'] == count($labelFromDb->getReleases())) {
            $now = new \Datetime();
            $labelFromDb->setLastTimeFullyScraped($now);
            $em->persist($labelFromDb);
            $em->flush();
        }

        return new JsonResponse([$guzzleException, $videosArray]);
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
