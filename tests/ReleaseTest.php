<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\{DiscogsVideo, Artist, Release, Label, Track};
use Doctrine\Common\Collections\{ArrayCollection, Collection};


class ReleaseTest extends TestCase
{
    public function testIsTrue(): void
    {
        $release = new Release();
        $now = new \DateTimeImmutable();

        $labelA = new Label();
        $labelB = new Label();
        $labelC = new Label();
        $labelD = new Label();
        $labelA->setName('a');
        $labelB->setName('b');
        $labelC->setName('c');
        $labelD->setName('d');

        $artistA = new Artist();
        $artistB = new Artist();
        $artistC = new Artist();
        $artistD = new Artist();
        $artistA->setName('a');
        $artistB->setName('b');
        $artistC->setName('c');
        $artistD->setName('d');

        $trackA = new Track();
        $trackB = new Track();
        $trackC = new Track();
        $trackD = new Track();
        $trackA->setName('a');
        $trackB->setName('b');
        $trackC->setName('c');
        $trackD->setName('d');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoB = new DiscogsVideo();
        $discogsVideoC = new DiscogsVideo();
        $discogsVideoD = new DiscogsVideo();
        $discogsVideoA->setUrl('a');
        $discogsVideoB->setUrl('b');
        $discogsVideoC->setUrl('c');
        $discogsVideoD->setUrl('d');

        $release->addLabel($labelA)->addLabel($labelB)
                ->addArtist($artistA)->addArtist($artistB)
                ->addTrack($trackA)->addTrack($trackB)
                ->addDiscogsVideo($discogsVideoA)->addDiscogsVideo($discogsVideoB)
                ->setReleaseDate($now);

        $this->assertTrue($release->getLabels() == new ArrayCollection([$labelA, $labelB]));
        $this->assertTrue($release->getArtists() == new ArrayCollection([$artistA, $artistB]));
        $this->assertTrue($release->getTracks() == new ArrayCollection([$trackA, $trackB]));
        $this->assertTrue($release->getDiscogsVideos() == new ArrayCollection([$discogsVideoA, $discogsVideoB]));
        $this->assertTrue($release->getReleaseDate() == $now);

    }

    public function testIsFalse(): void
    {
        $release = new Release();
        $now = new \DateTimeImmutable();

        $labelA = new Label();
        $labelB = new Label();
        $labelC = new Label();
        $labelD = new Label();
        $labelA->setName('a');
        $labelB->setName('b');
        $labelC->setName('c');
        $labelD->setName('d');

        $artistA = new Artist();
        $artistB = new Artist();
        $artistC = new Artist();
        $artistD = new Artist();
        $artistA->setName('a');
        $artistB->setName('b');
        $artistC->setName('c');
        $artistD->setName('d');

        $trackA = new Track();
        $trackB = new Track();
        $trackC = new Track();
        $trackD = new Track();
        $trackA->setName('a');
        $trackB->setName('b');
        $trackC->setName('c');
        $trackD->setName('d');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoB = new DiscogsVideo();
        $discogsVideoC = new DiscogsVideo();
        $discogsVideoD = new DiscogsVideo();
        $discogsVideoA->setUrl('a');
        $discogsVideoB->setUrl('b');
        $discogsVideoC->setUrl('c');
        $discogsVideoD->setUrl('d');

        $release->addLabel($labelA)->addLabel($labelB)
                ->addArtist($artistA)->addArtist($artistB)
                ->addTrack($trackA)->addTrack($trackB)
                ->addDiscogsVideo($discogsVideoA)->addDiscogsVideo($discogsVideoB)
                ->setReleaseDate($now);

        $this->assertFalse($release->getLabels() == new ArrayCollection([$labelC, $labelD]));
        $this->assertFalse($release->getArtists() == new ArrayCollection([$artistC, $artistD]));
        $this->assertFalse($release->getTracks() == new ArrayCollection([$trackC, $trackD]));
        $this->assertFalse($release->getDiscogsVideos() == new ArrayCollection([$discogsVideoC, $discogsVideoD]));
        $this->assertFalse($release->getReleaseDate() == new \DateTimeImmutable);
    }
    
    public function testIsEmpty(): void
    {
        $release = new Release();
        $now = new \DateTimeImmutable();

        $labelA = new Label();
        $labelA->setName('a');

        $artistA = new Artist();
        $artistA->setName('a');

        $trackA = new Track();
        $trackA->setName('a');

        $discogsVideoA = new DiscogsVideo();
        $discogsVideoA->setUrl('a');

        $release->addLabel($labelA)->removeLabel($labelA)
                ->addArtist($artistA)->removeArtist($artistA)
                ->addTrack($trackA)->removeTrack($trackA)
                ->addDiscogsVideo($discogsVideoA)->removeDiscogsVideo($discogsVideoA);

        $this->assertEmpty($release->getLabels());
        $this->assertEmpty($release->getArtists());
        $this->assertEmpty($release->getTracks());
        $this->assertEmpty($release->getDiscogsVideos());
        $this->assertEmpty($release->getReleaseDate() );
    }
}
