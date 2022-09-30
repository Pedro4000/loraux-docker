<?php

namespace App\Service ;

use App\Entity\{Artist, Label, Track, Release, DiscogsVideo};
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use Google\Service\Container\ReleaseChannelConfig;
use App\Repository\ArtistRepository;
use Symfony\Component\HttpFoundation\JsonResponse;


class DiscogsService
{
    private $em;


    public function __construct(ManagerRegistry $doctrine, string $discogsConsumerKey, string $discogs_consumer_secret){
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
        $this->discogsConsumerKey = $discogsConsumerKey;
        $this->discogs_consumer_secret = $discogs_consumer_secret;
        $this->alreadyCheckedArtists = [];
        $this->alreadyCheckedLabels = [];
    }

    public function scrapDiscogsArtist(int $discogsId) {

        $discogsCredentials = 'key='.$this->discogsConsumerKey.'&secret='.$this->discogs_consumer_secret;
        $baseDiscogsApi = 'https://api.discogs.com/';
        $page = 1;
        $guzzleClient = new Client();
        $artistRepository = $this->doctrine->getRepository(Label::class);
        $releaseRepository = $this->doctrine->getRepository(Release::class);
        $now = new \DateTimeImmutable();

        // create artist if not in db
        if (!$artistRepository->findOneBy([ 'discogsId' => $discogsId])) {
            $artistInfosResponse = $guzzleClient->request('GET', $baseDiscogsApi.'artists/'.$discogsId.'?'.$discogsCredentials);
            $artistInfosContent = json_decode($artistInfosResponse->getBody()->getContents(), true);
            $artist = self::createLabel($artistInfosContent['id'], $artistInfosContent['name']);
        } else {
            $artist = $artistRepository->findOneBy([ 'discogsId' => $discogsId]);
        };

        /*
        // ignore if was checked less than 50 days ago
        if ($artist->isFullyScrapped() && date_diff($artist->getFullyScrappedDate(), $now)->d < 50) {
            return $artist;
        }
        */

        $artistReleasesResponse = $guzzleClient->request('GET', $baseDiscogsApi.'artists/'.$discogsId.'/releases?page='.$page.'&'.$discogsCredentials);
        $remainingRequests = $artistReleasesResponse->getHeaders()['X-Discogs-Ratelimit-Remaining'];
        if (!$artistReleasesResponse->getStatusCode() == 200) {
            return new JsonResponse([
                'status_code' => $artistReleasesResponse->getStatusCode(),
                'status' => 'error',
            ]);
        };

        $artistReleasesContent = json_decode($artistReleasesResponse->getBody()->getContents(), true);

        $counter = 0;
        for ($currentPage = 1; $currentPage <= $artistReleasesContent['pagination']['pages']; $currentPage++) {
            // if discogs requests limit exceeded then we pause for a minute;
            // if first page then we already have the discogs artist releases response
            
            if ($currentPage != 1) {
                if($remainingRequests == 1) {
                    sleep(60);
                }
                $artistReleasesResponse = $guzzleClient->request('GET', $baseDiscogsApi.'artists/'.$discogsId.'/releases?page='.$currentPage.'&'.$discogsCredentials);
                $artistReleasesContent = json_decode($artistReleasesResponse->getBody()->getContents(), true);
            };
            
            for ($currentItemInPage = 0; $currentItemInPage < count($artistReleasesContent['releases']); $currentItemInPage++) {
                $counter++;

                if ($artistReleasesContent['releases'][$currentItemInPage]['type'] == 'master') {
                    $releaseId = $artistReleasesContent['releases'][$currentItemInPage]['main_release'];
                } elseif($artistReleasesContent['releases'][$currentItemInPage]['type'] == 'release') {
                    $releaseId = $artistReleasesContent['releases'][$currentItemInPage]['id'];
                }

                if ($releaseRepository->findOneBy(['discogsId' => $releaseId])) {
                    continue;
                } else {

                    if ($remainingRequests < 2) {
                        sleep(60);
                    }

                    $releaseReponse = $guzzleClient->request('GET', $baseDiscogsApi.'releases/'.$releaseId);
                    sleep(2);
                    $releaseContent = json_decode($releaseReponse->getBody()->getContents(),true);
                    $remainingRequests = $releaseReponse->getHeaders()['X-Discogs-Ratelimit-Remaining'];


                    self::createRelease($releaseId, 
                                        $releaseContent['title'],
                                        $releaseContent['released'] ?? null,
                                        $releaseContent['videos'] ?? [],
                                        $releaseContent['labels'],
                                        $releaseContent['artists']);
                }
            }
        }

        return $artist;        
    }

    public function scrapDiscogsLabel(int $discogsId) {

        $discogsCredentials = 'key='.$this->discogsConsumerKey.'&secret='.$this->discogs_consumer_secret;
        $baseDiscogsApi = 'https://api.discogs.com/';
        $page = 1;
        $guzzleClient = new Client();
        $labelRepository = $this->doctrine->getRepository(Label::class);
        $releaseRepository = $this->doctrine->getRepository(Release::class);
        $now = new \DateTimeImmutable();

        // create label if not in db
        if (!$labelRepository->findOneBy([ 'discogsId' => $discogsId])) {
            $labelInfosResponse = $guzzleClient->request('GET', $baseDiscogsApi.'labels/'.$discogsId.'?'.$discogsCredentials);
            $labelInfosContent = json_decode($labelInfosResponse->getBody()->getContents(), true);
            $label = self::createLabel($labelInfosContent['id'], $labelInfosContent['name']);
        } else {
            $label = $labelRepository->findOneBy([ 'discogsId' => $discogsId]);
        };

        // ignore if was checked less than 50 days ago
        if ($label->isFullyScrapped() && date_diff($label->getFullyScrappedDate(), $now)->d < 50) {
            return $label;
        }

        $labelReleasesResponse = $guzzleClient->request('GET', $baseDiscogsApi.'labels/'.$discogsId.'/releases?page='.$page.'&'.$discogsCredentials);
        $remainingRequests = $labelReleasesResponse->getHeaders()['X-Discogs-Ratelimit-Remaining'];
        if (!$labelReleasesResponse->getStatusCode() == 200) {
            return new JsonResponse([
                'status_code' => $labelReleasesResponse->getStatusCode(),
                'status' => 'error',
            ]);
        };

        $labelReleasesContent = json_decode($labelReleasesResponse->getBody()->getContents(), true);

        
        for ($currentPage = 1; $currentPage <= $labelReleasesContent['pagination']['pages']; $currentPage++) {
            // if discogs requests limit exceeded then we pause for a minute;
            // if first page then we already have the discogs label releases response
            if ($currentPage != 1) {
                if($remainingRequests == 1) {
                    sleep(60);
                }
                $labelReleasesResponse = $guzzleClient->request('GET', $baseDiscogsApi.'labels/'.$discogsId.'/releases?page='.$currentPage.'&'.$discogsCredentials);
            };
            
            for ($currentItemInPage = 0; $currentItemInPage < count($labelReleasesContent['releases']); $currentItemInPage++) {
                if ($labelReleasesContent['releases'][$currentItemInPage]['type'] == 'master') {
                    $releaseId = $labelReleasesContent['releases'][$currentItemInPage]['main_release'];
                } else {
                    $releaseId = $labelReleasesContent['releases'][$currentItemInPage]['id'];
                }
                if ($releaseRepository->findOneBy(['discogsId' => $releaseId])) {
                    continue;
                } else {

                    if ($remainingRequests == 1) {
                        sleep(60);
                    }
                    $releaseId = $labelReleasesContent['releases'][$currentItemInPage]['id'];

                    $releaseReponse = $guzzleClient->request('GET', $baseDiscogsApi.'releases/'.$releaseId);
                    $releaseContent = json_decode($releaseReponse->getBody()->getContents(),true);
                    $remainingRequests = $releaseReponse->getHeaders()['X-Discogs-Ratelimit-Remaining'];

                    self::createRelease($releaseId, 
                                        $releaseContent['title'],
                                        $releaseContent['released'],
                                        $releaseContent['videos'],
                                        $releaseContent['labels'],
                                        $releaseContent['artists']);
                }
            }
        }
        return $label;        
    }

    public function createArtist(int $discogsId, string $name) {

        $artist = new Artist();
        $artist->setDiscogsId($discogsId);
        $artist->setName($name);
        $artist->setFullyScrapped(false);

        $artist->setCreatedAt(new \DateTimeImmutable);
        $this->em->persist($artist);
        $this->em->flush();

        return $artist;
    }

    public function createTrack($track, $trackArtists, $release){
        $newTrack = new Track();
        if (is_array($trackArtists)) {
            foreach ($trackArtists as $a){
                $release->addArtist($a);
            }
        } else {
            $release->addArtist($trackArtists);
        }
        $newTrack->setName($track['title']);
        $newTrack->setRelease($release);
        $this->em->persist($newTrack);
        $this->em->flush();
    }

    public function createLabel(int $discogsId, string $name){
        $label = new Label();
        $label->setName($name);
        $label->setDiscogsId($discogsId);
        $label->setFullyScrapped(false);
        $label->setCreatedAt(new \DateTimeImmutable);
        $this->em->persist($label);
        $this->em->flush();

        return $label;
    }

    public function createRelease($discogsId, $name, $releaseDate, $videos, $labels, $artists){

        $labelRepository = $this->doctrine->getRepository(Label::class);
        $releaseRepository = $this->doctrine->getRepository(Release::class);
        $artistRepository = $this->doctrine->getRepository(Artist::class);

        // we create each artist that we dont yet have in db and store them in an array
        foreach ($artists as $artist) {
            if (!array_key_exists($artist['id'], $this->alreadyCheckedArtists)) {
                $artistFromDb = $artistRepository->findOneBy(['discogsId' => $artist['id']]);
                if(!$artistFromDb) {
                    $artistFromDb = self::createArtist($artist['id'], $artist['name']);
                }
                $this->alreadyCheckedArtists[$artist['id']] = $artistFromDb;
            } else {
                $artistFromDb = $this->alreadyCheckedArtists[$artist['id']];
            }
        }
        // we create each label that we dont yet have in db and store them in an array
        foreach ($labels as $label) {
            if (!array_key_exists($label['id'], $this->alreadyCheckedLabels)) {
                $labelFromDb = $labelRepository->findOneBy(['discogsId' => $label['id']]);
                if(!$labelFromDb) {
                    $labelFromDb = self::createLabel($label['id'], $label['name']);
                }
                $this->alreadyCheckedLabels[$label['id']] = $labelFromDb;
            } else {
                $labelFromDb = $this->alreadyCheckedLabels[$label['id']];
            }
        }

        if (!$releaseDate){
            $formatedReleaseDate = null;
        } else{
            $formatedReleaseDate = \DateTime::createFromFormat('Y-m-d', $releaseDate);
            if (!$formatedReleaseDate) {
                $formatedReleaseDate = \DateTime::createFromFormat('Y', $releaseDate);
            }
        }

        $release = new Release();
        foreach ($artists as $artist) {
            $release->addArtist($this->alreadyCheckedArtists[$artist['id']]);
        }
        foreach($labels as $label) {
            $release->addLabel($this->alreadyCheckedLabels[$label['id']]);
        }
        $release->setName($name);
        $release->setDiscogsId($discogsId);
        $release->setReleaseDate($formatedReleaseDate);
        $release->setFullyScrappedDate(new \DateTimeImmutable);
        $release->setCreatedAt(new \DateTimeImmutable);
        $release->setFullyScrapped(false);

        foreach($videos as $video) {
            $discogsVideo = new DiscogsVideo();
            $discogsVideo->setUrl($video['uri']);
            $this->em->persist($discogsVideo);
            $release->addDiscogsVideo($discogsVideo);
        }
        $this->em->persist($release);
        $this->em->flush();
    }

    public function setArrayKeyToNullIfNonExistent($releaseInfos)
    {
        if (!array_key_exists('released', $releaseInfos)) {
            $releaseInfos['released']=null;
        }
        if (!array_key_exists('videos', $releaseInfos)) {
            $releaseInfos['videos']=null;
        }
        return $releaseInfos;
    }
}