<?php

namespace App\Service ;

use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Track;
use App\Entity\Release;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\DiscogsVideo;

class DiscogsService
{
    private $em;

    public function __construct(ManagerRegistry $doctrine){
        $this->em = $doctrine->getManager();
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

    public function createRelease($discogsId, $name, $releaseDate, $videos, $labels, $artist){

        if ($this->em->getRepository(Release::class)->findOneBy(['discogsId'=>$discogsId])){
            return;
        }
        $videosArray = [];
        if (!$releaseDate){
            $formatedReleaseDate=null;
        } else{
            $formatedReleaseDate = \DateTime::createFromFormat('Y-m-d', $releaseDate);
            if (!$formatedReleaseDate) {
                $formatedReleaseDate = \DateTime::createFromFormat('Y', $releaseDate);
            }
        }
        $release = new Release();
        $release->setName($name);
        $release->setDiscogsId($discogsId);
        $release->setReleaseDate($formatedReleaseDate);
        $release->setFullyScrappedDate(new \DateTimeImmutable);
        $release->setCreatedAt(new \DateTimeImmutable);
        $release->setFullyScrapped(false);
        if ($videos) {
            foreach ($videos as $video){
                array_push($videosArray, $video['uri']);
            }
        }
        foreach($videosArray as $video) {
            $discogsVideo = new DiscogsVideo();
            $discogsVideo->setUrl($video);
            $this->em->persist($discogsVideo);
            $release->addDiscogsVideo($discogsVideo);
        }
        foreach($release->getLabels() as $label) {
            $release->addLabel($labels);
        }
        if (is_array($artist)) {
            foreach ($artist as $a){
                $release->addArtist($a);
            }
        } else {$release->addArtist($artist);}
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