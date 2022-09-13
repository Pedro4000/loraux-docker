<?php

namespace App\Service ;

use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Track;
use App\Entity\Release;
use Doctrine\Persistence\ManagerRegistry;

class DiscogsService
{
    private $em;

    public function __construct(){
    }
    public function createNewArtist($discogsId, $name) {
        $newArtist = new Artist();
        $newArtist->setName($name);
        $newArtist->setDiscogsId($discogsId);
        $this->em->persist($newArtist);
        $this->em->flush();
    }

    public function createNewTrack($track, $trackArtists, $release, $label){
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

    public function createNewLabel($discogsId, $name){
        $newLabel = new Label();
        $newLabel->setName($name);
        $newLabel->setDiscogsId($discogsId);
        $this->em->persist($newLabel);
        $this->em->flush();
    }

    public function createNewRelease($discogsId, $name, $releaseDate, $videos, $label, $artist){
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
        $newRelease = new Release();
        $newRelease->setName($name);
        $newRelease->setDiscogsId($discogsId);
        $newRelease->setReleaseDate($formatedReleaseDate);
        if ($videos) {
            foreach ($videos as $video){
                array_push($videosArray, $video['uri']);
            }
        }
        $newRelease->setVideos($videosArray);
        $newRelease->addLabel($label);
        if (is_array($artist)) {
            foreach ($artist as $a){
                $newRelease->addArtist($a);
            }
        } else {$newRelease->addArtist($artist);}
        $this->em->persist($newRelease);
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