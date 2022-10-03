<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\CalendarService;
use App\Service\MailToNewMember;
use Google_Client;
use Google_Service;
use Google_Service_YouTube;
use Google_Service_YouTube_Playlist;
use Google_Service_YouTube_PlaylistSnippet;
use Google_Service_YouTube_PlaylistStatus;
use Google_Service_YouTube_PlaylistItem;
use Google_Service_YouTube_PlaylistItemSnippet;
use Google_Service_YouTube_ResourceId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class YoutubeController extends AbstractController
{

    public function __construct(
        private Google_Client $client, 
        private ParameterBagInterface $params,
        private RequestStack $requestStack,
        private $session = $requestStack->getSession(),
        ) { }


    /**
     * @Route("/createYoutubePlaylist", name="createYoutubePlaylist")
     * @param Request $request
     */
    public function createYoutubePlaylistAction(Request $request)
    {
        $client = new Google_Client();
        $client->setApplicationName('Loraux');
        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $client->setAuthConfig('credentials.json');
        $client->addScope("https://www.googleapis.com/auth/youtube");
        $client->setPrompt('select_account consent');

        $client->setRedirectUri('http://127.0.0.1:8000/createYoutubePlaylistCode');
        $authUrl = $client->createAuthUrl();

        return $this->redirect($authUrl);

    }

    /**
     * @Route("/createYoutubePlaylistCode", name="createYoutubePlaylistCode")
     * @param Request $request
     */
    public function createYoutubePlaylistCodeAction(Request $request)
    {
        $videoArrayForYoutube = [];
        $videosArrayFromDiscogs = $this->session->get('videosToPutInPlaylist');
        $request->getPathInfo();
        $google_code = $request->query->get('code');
        $this->session->set('auth_code',$google_code);

        $dateTimeNow = new \DateTime();
        $dateTime3339 = $dateTimeNow->format("Y-m-d\TH:i:sP");

        // Set up the google client with the general info
        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube'
        ]);
        $client->setRedirectUri('http://127.0.0.1:8000/createYoutubePlaylistCode');


        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);

        $accessToken = $client->fetchAccessTokenWithAuthCode($google_code);
        $client->setAccessToken($accessToken);
        $this->session->set('access_token', $accessToken);

        if ($this->session->get('access_token')) {
            $client->setAccessToken($this->session->get('access_token'));
            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    $this->oauth($client);
                }
            }
            $client->setAccessToken($this->session->get('access_token'));
            $service = new Google_Service_YouTube($client);
            $queryParams = [
                'mine' => true
            ];

            $response = $service->channels->listChannels('snippet,contentDetails,statistics', $queryParams);
            $channelid = $response[0]['id'];

/*            $queryParams = [
                'channelId' => $channelid,
                'maxResults' => 25
            ];

            $youtubePlaylists = $service->playlists->listPlaylists('snippet,contentDetails', $queryParams);*/

            //test pour la fonction search de l'api
/*            foreach ($videosArrayFromDiscogs as $discogsVideo) {
                $testUri = "https://www.youtube.com/watch?v=_B1qr5H_dDE";
                $queryParamsForSearch = [
                    'q'=> "https://www.youtube.com/watch?v=-dikWB6wm0A"
                ];

                $specificVideoSearch = $service->search->listSearch('snippet', $queryParamsForSearch);

                if ($specificVideoSearch['item']) {
                    $specificVideoId = $specificVideoSearch[0]['id']['videoId'];
                }
            }*/


/*            $queryParamsForVideo = [
                'id'=> $specificVideoId
            ];
            $specificVideo = $service->videos->listVideos('snippet,contentDetails,statistics', $queryParamsForVideo);*/


            // ici on va créer la playlist, il faut récupérer les infos du label pour pouvoir adapter le titre etc.
            // Define the $playlist object, which will be uploaded as the request body.
            $playlist = new Google_Service_YouTube_Playlist();

            // Add 'snippet' object to the $playlist object.
            $playlistSnippet = new Google_Service_YouTube_PlaylistSnippet();
            $playlistSnippet->setDefaultLanguage('fr');
            $playlistSnippet->setDescription('Playlist pour le label '.$videosArrayFromDiscogs['label']);
/*            $playlistSnippet->setTags(['sample playlist', 'API call']);*/
            $playlistSnippet->setTitle($videosArrayFromDiscogs['label']);
            $playlist->setSnippet($playlistSnippet);

            // Add 'status' object to the $playlist object.
            $playlistStatus = new Google_Service_YouTube_PlaylistStatus();
            $playlistStatus->setPrivacyStatus('public');
            $playlist->setStatus($playlistStatus);

            $responseForPlaylistCreation = $service->playlists->insert('snippet,status', $playlist);

            $newPlaylistId = $responseForPlaylistCreation['id'];

            // Partie ou on va ajouter les playlists items donc les vidéos, à la playlist
            // Define the $playlistItem object, which will be uploaded as the request body.

            foreach ($videosArrayFromDiscogs['releases'] as $videoFromDiscogs) {

                $videoId = explode('&',explode('v=',  $videoFromDiscogs['videoUri'])[1])[0];
                $playlistItem = new Google_Service_YouTube_PlaylistItem();
                // Add 'snippet' object to the $playlistItem object.
                $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
                $playlistItemSnippet->setPlaylistId($newPlaylistId);
                $playlistItemSnippet->setPosition(0);
                $resourceId = new Google_Service_YouTube_ResourceId();
                $resourceId->setKind('youtube#video');
                $resourceId->setVideoId($videoId);
                $playlistItemSnippet->setResourceId($resourceId);
                $playlistItem->setSnippet($playlistItemSnippet);
                $response = $service->playlistItems->insert('snippet', $playlistItem);
            }


            return $this->redirect("index");
        }
    }


}

