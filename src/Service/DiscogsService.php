<?php

namespace App\Service ;

use App\Entity\{Artist, DiscogsClass, Label, Track, Release, DiscogsVideo};
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Google\Service\Container\ReleaseChannelConfig;
use App\Repository\ArtistRepository;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Component\HttpFoundation\JsonResponse;


class DiscogsService
{
    /**
     * @param array<int, Artist> $alreadyCheckedArtists
     * @param array<int, Label> $alreadyCheckedLabels
     */
    public function __construct(
        public ManagerRegistry $doctrine,
        private string $discogsConsumerKey, 
        private string $discogsConsumerSecret,
        private array $alreadyCheckedArtists = [],
        private array $alreadyCheckedLabels = [],
        ){ }

    public function scrapDiscogsArtist(int $discogsId): Artist | JsonResponse
    {
        $discogsCredentials = 'key='.$this->discogsConsumerKey.'&secret='.$this->discogsConsumerSecret;
        $baseDiscogsApi = 'https://api.discogs.com/';
        $page = 1;
        $guzzleClient = new Client();
        $artistRepository = $this->doctrine->getRepository(Artist::class);
        $releaseRepository = $this->doctrine->getRepository(Release::class);
        $em = $this->doctrine->getManager();

        // create artist if not in db
        if (!$artistRepository->findOneBy([ 'discogsId' => $discogsId])) {
            $artistInfosResponse = $guzzleClient->request('GET', $baseDiscogsApi.'artists/'.$discogsId.'?'.$discogsCredentials);
            $artistInfosContent = json_decode($artistInfosResponse->getBody()->getContents(), true);
            $artist = self::createArtist($artistInfosContent['id'], $artistInfosContent['name']);
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
        $remainingRequests = intval($artistReleasesResponse->getHeaders()['X-Discogs-Ratelimit-Remaining']);
        if (!$artistReleasesResponse->getStatusCode() == 200) {
            return new JsonResponse([
                'status_code' => $artistReleasesResponse->getStatusCode(),
                'status' => 'error',
            ]);
        };

        $artistReleasesContent = json_decode($artistReleasesResponse->getBody()->getContents(), true);
        $artistInfosResponse = $guzzleClient->request('GET', $baseDiscogsApi.'artists/'.$discogsId.'?'.$discogsCredentials);
        $artistInfosContent = json_decode($artistInfosResponse->getBody()->getContents(), true);

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
                } else {
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
                    $remainingRequests = intval($artistReleasesResponse->getHeaders()['X-Discogs-Ratelimit-Remaining']);


                    self::createRelease($releaseId, 
                                        $releaseContent['title'],
                                        $releaseContent['released'] ?? null,
                                        $releaseContent['videos'] ?? [],
                                        $releaseContent['labels'],
                                        $releaseContent['artists']);
                }
            }

        }
        $artist->setFullyScrapped(true)->setFullyScrappedDate(new \DateTimeImmutable());
        $em->persist($artist);
        $em->flush();

        return new JsonResponse([
            'status_code' => 200,
            'status' => 'cool',
        ]);
    }

    public function scrapDiscogsLabel(int $discogsId): mixed
    {
        $discogsCredentials = 'key='.$this->discogsConsumerKey.'&secret='.$this->discogsConsumerSecret;
        $baseDiscogsApi = 'https://api.discogs.com/';
        $page = 1;
        $guzzleClient = new Client();
        $labelRepository = $this->doctrine->getRepository(Label::class);
        $releaseRepository = $this->doctrine->getRepository(Release::class);
        $now = new \DateTimeImmutable();
        $em = $this->doctrine->getManager();

        // create label if not in db
        if (!$labelRepository->findOneBy([ 'discogsId' => $discogsId])) {
            $labelInfosResponse = $guzzleClient->request('GET', $baseDiscogsApi.'labels/'.$discogsId.'?'.$discogsCredentials);
            $labelInfosContent = json_decode($labelInfosResponse->getBody()->getContents(), true);
            $label = self::createLabel($labelInfosContent['id'], $labelInfosContent['name']);
        } else {
            $label = $labelRepository->findOneBy([ 'discogsId' => $discogsId]);
        };
        
        // ignore if was checked less than 50 days ago
        /*
        if ($label->isFullyScrapped() && date_diff($label->getFullyScrappedDate(), $now)->d < 50) {
            return $label;
        }
        */

        $labelReleasesResponse = $guzzleClient->request('GET', $baseDiscogsApi.'labels/'.$discogsId.'/releases?page='.$page.'&'.$discogsCredentials);
        $remainingRequests = intval($labelReleasesResponse->getHeaders()['X-Discogs-Ratelimit-Remaining']);
        if (!$labelReleasesResponse->getStatusCode() == 200) {
            return new JsonResponse([
                'status_code' => $labelReleasesResponse->getStatusCode(),
                'status' => 'error',
            ]);
        };
        

        $labelReleasesContent = json_decode($labelReleasesResponse->getBody()->getContents(), true);

        // set total items if wrong or empty
        if(!$label->getTotalItems() || $label->getTotalItems() != $labelReleasesContent['pagination']['items']) {
            $label->setTotalItems($labelReleasesContent['pagination']['items']);
            $em->persist($label);
            $em->flush();
        }

        // set fullyscrapped and fullyscrapped date if wrong or empty
        if(!$label->getFullyScrappedDate() && ($label->getTotalItems() == $labelReleasesContent['pagination']['items'])) {
            $label->setFullyScrappedDate($now);
            $label->setFullyScrapped(true);
            $em->persist($label);
            $em->flush();
        }

        $counter = 0;
        for ($currentPage = 1; $currentPage <= $labelReleasesContent['pagination']['pages']; $currentPage++) {
            // if discogs requests limit exceeded then we pause for a minute;
            // if first page then we already have the discogs label releases response
            if ($currentPage != 1) {
                $labelReleasesResponse = $guzzleClient->request('GET', $baseDiscogsApi.'labels/'.$discogsId.'/releases?page='.$currentPage.'&'.$discogsCredentials);
            };
            
            for ($currentItemInPage = 0; $currentItemInPage < count($labelReleasesContent['releases']); $currentItemInPage++) {
                $counter++;
                try {
                    $releaseId = $labelReleasesContent['releases'][$currentItemInPage]['id'];
                    if ($releaseRepository->findOneBy(['discogsId' => $releaseId])) {
                        continue;
                    } else {
                        $releaseId = $labelReleasesContent['releases'][$currentItemInPage]['id'];
    
                        $releaseReponse = $guzzleClient->request('GET', $baseDiscogsApi.'releases/'.$releaseId);
                        $releaseContent = json_decode($releaseReponse->getBody()->getContents(),true);
                        $remainingRequests = intval($releaseReponse->getHeaders()['X-Discogs-Ratelimit-Remaining']);
    
                        self::createRelease($releaseId, 
                                            $releaseContent['title'],
                                            $releaseContent['released'] ?? '',
                                            $releaseContent['videos']?? [],
                                            $releaseContent['labels'],
                                            $releaseContent['artists']);
                    }
                } catch (ClientException $e) {
                    if ($e->getResponse()->getStatusCode() == 429) {
                        $currentItemInPage--;
                    };
                    sleep(60);
                }
            }
        }

        $releases = $label->getReleases();
        $label->setNumberScrapped(count($releases));
        $em->persist($label);
        $em->flush();

        return [
            'status_code' => 200,
            'status' => 'cool',
            'counter' => $counter,
            'discogsObject' => self::formatDiscogsObjectToArrayForAjaxResponse($label),
        ];    
    }

    public function getDiscogsVideosURIToYoutubeId(string $url){
        
        $videoId = null;
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
            $videoId = $match[1];
        }
        return $videoId;
    }

    public function createArtist (int $discogsId, string $name): ?Artist
    {
        $em = $this->doctrine->getManager();
        $artist = new Artist();
        $artist->setDiscogsId($discogsId);
        $artist->setName($name);
        $artist->setFullyScrapped(false);

        $artist->setCreatedAt(new \DateTimeImmutable);
        $em->persist($artist);
        $em->flush();

        return $artist;
    }

    public function createTrack (array $track, ArrayCollection $trackArtists, Release $release): void
    {
        $em = $this->doctrine->getManager();
        $newTrack = new Track();
        if (is_array($trackArtists)) {
            foreach ($trackArtists as $a){
                $release->addArtist($a);
            }
        }
        $newTrack->setName($track['title']);
        $newTrack->setRelease($release);
        $em->persist($newTrack);
        $em->flush();
    }

    public function createLabel(int $discogsId, string $name): ?Label
    {
        $em = $this->doctrine->getManager();
        $label = new Label();
        $label->setName($name);
        $label->setDiscogsId($discogsId);
        $label->setFullyScrapped(false);
        $label->setCreatedAt(new \DateTimeImmutable);
        $em->persist($label);
        $em->flush();

        return $label;
    }

    public function createRelease(int $discogsId, string $name, string $releaseDate, array $videos, array $labels, array $artists): void
    {
        $em = $this->doctrine->getManager();

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
            $em->persist($discogsVideo);
            $release->addDiscogsVideo($discogsVideo);
        }
        $em->persist($release);
        $em->flush();
    }

    public function setArrayKeyToNullIfNonExistent($releaseInfos): ?array
    {
        if (!array_key_exists('released', $releaseInfos)) {
            $releaseInfos['released']=null;
        }
        if (!array_key_exists('videos', $releaseInfos)) {
            $releaseInfos['videos']=null;
        }
        return $releaseInfos;
    }

    // format db object to simple array
    public function formatDiscogsObjectToArrayForAjaxResponse(DiscogsClass $discogsClassLabelOrArtist): ?array
    {
        $discogsObject = [
            'id' => $discogsClassLabelOrArtist->getId(),
            'discogsId' => $discogsClassLabelOrArtist->getDiscogsId(),
            'name' => $discogsClassLabelOrArtist->getName(),
            'numberScrapped' => $discogsClassLabelOrArtist->getNumberScrapped(),
            'totalItems' => $discogsClassLabelOrArtist->getTotalItems(),
            'fullyScrapped' => $discogsClassLabelOrArtist->isFullyScrapped(),
            'fullyScrappedDate' => $discogsClassLabelOrArtist->getFullyScrappedDate()->format('d/m/Y'),
        ];

        return $discogsObject;
    }
}