<?php

namespace App\Controller;

use App\Entity\{Artist, Label, Release, User};
use App\Service\{DiscogsService};
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RequestStack, Request, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\{ArtistRepository, LabelRepository};
use Symfony\Component\Form\Extension\Core\Type\{NumberType, SubmitType};

#[Route('/music/label', name: 'music.label.')]
class LabelController extends AbstractController
{
    
    #[Route('/index', name: 'index', methods: ['GET', 'HEAD'])]
    public function index(Request $request, ManagerRegistry $doctrine, LabelRepository $labelRepository) 
    {
//      dd($request->query->all());
    
        $page = $_GET['page'] ?? 1;
        $size = 20;

        $params = compact('page', 'size');

        $labels = $labelRepository->getLabelsByParams($params);

        return $this->render('label/index.html.twig',[
            'labels' => $labels,
        ]);
    }
 
    
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id, ManagerRegistry $doctrine, LabelRepository $labelRepository, DiscogsService $discogsService) 
    {
        $discogsCredentials = 'key='.$this->getParameter('discogs_consumer_key').'&secret='.$this->getParameter('discogs_consumer_secret');
        $baseDiscogsApi = 'https://api.discogs.com/';

        $label = $labelRepository->find($id);
        $releases = $label->getReleases();
        $videos = [];
        $videosString = '';
        foreach ($releases as $release) {
            foreach ($release->getDiscogsVideos() as $video) {
                $video->youtubeId = $discogsService->getDiscogsVideosURIToYoutubeId($video->getUrl());
                $videos[] = $video;
            }
        }
        $videosString = substr(trim($videosString), 0 , -1);
        
        return $this->render('video/player.html.twig',[
            'label' => $label,
            'videos' => $videos,
            'videosString' =>  $videosString,
        ]);
    }


    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, ManagerRegistry $doctrine, LabelRepository $labelRepository) 
    {
        dd($request->query->all());

        
    }    
    
}
