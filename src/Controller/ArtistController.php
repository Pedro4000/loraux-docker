<?php

namespace App\Controller;

use App\Entity\{Artist, Label, Release, User};
use App\Form\SearchType;
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

#[Route('/music/artist', name: 'music.artist.')]
class ArtistController extends AbstractController
{

    public function __construct (
        private Google_Client $client, 
        private ParameterBagInterface $params, 
        private DiscogsService $discogsService, 
        private RequestStack $requestStack
    ) { }
    
    #[Route('/index', name: 'index', methods: ['GET', 'HEAD'])]
    public function index(Request $request, ManagerRegistry $doctrine, ArtistRepository $artistRepository) 
    {
        //      dd($request->query->all());


        $page = $_GET['page'] ?? 1;
        $size = 20;

        $searchForm = $this->createForm(SearchType::class, null);

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $query = $searchForm->getData()['query'];
            $params = compact('page', 'size', 'query');
        } else {
            $params = compact('page', 'size');
        }
        $artists = $artistRepository->getArtistsByParams($params);

        return $this->renderForm('artist/index.html.twig',[
            'artists' => $artists,
            'searchForm' => $searchForm,
        ]);
    }

    #[Route('/{id}', name: 'show')]
    public function artistShow(string $id, ManagerRegistry $doctrine, ArtistRepository $artistRepository, DiscogsService $discogsService) 
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
}
