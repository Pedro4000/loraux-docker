<?php

namespace App\Tests;

use App\Entity\{DiscogsVideo, Artist, Release, Label};
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;


class DiscogsVideoTest extends TestCase
{
    public function testIsTrue(): void
    {
        $discogsVideo = new DiscogsVideo();
        $now = new \DateTimeImmutable('now');

        $artistA = new Artist();
        $artistB = new Artist();
        $artistA->setName('un');
        $artistB->setName('deux');
        
        $releaseA = new Release();
        $releaseA->setDiscogsId(1);

        $labelA = new Label();
        $labelA->setName('un');

        $discogsVideo->setUrl('url')
               ->addArtist($artistA)->addArtist($artistB)
               ->setRelease($releaseA)
               ->setLabel($labelA);

        $this->assertTrue($discogsVideo->getUrl() == 'url');
        $this->assertTrue($discogsVideo->getArtists() == new ArrayCollection([$artistA, $artistB]));
        $this->assertTrue($discogsVideo->getRelease() == $releaseA);
        $this->assertTrue($discogsVideo->getLabel() == $labelA);

    }

    public function testIsFalse(): void
    {
        $discogsVideo = new DiscogsVideo();
        $now = new \DateTimeImmutable('now');
        
        $artistA = new Artist();
        $artistB = new Artist();
        $artistC = new Artist();
        $artistD = new Artist();
        $artistA->setName('un');
        $artistB->setName('deux');
        $artistC->setName('trois');
        $artistD->setName('quatre');
        
        $releaseA = new Release();
        $releaseB = new Release();
        $releaseC = new Release();
        $releaseD = new Release();
        $releaseA->setDiscogsId(1);
        $releaseB->setDiscogsId(2);
        $releaseC->setDiscogsId(3);
        $releaseD->setDiscogsId(4);

        $labelA = new Label();
        $labelB = new Label();
        $labelC = new Label();
        $labelD = new Label();
        $labelA->setName('un');
        $labelB->setName('deux');
        $labelC->setName('trois');
        $labelD->setName('quatre');

        $discogsVideo->setUrl('url')
               ->addArtist($artistA)->addArtist($artistB)
               ->setRelease($releaseA)
               ->setLabel($labelA);

        $this->assertFalse($discogsVideo->getUrl() == 'fakeurl');
        $this->assertFalse($discogsVideo->getArtists() == new ArrayCollection([$artistB, $artistC]));
        $this->assertFalse($discogsVideo->getRelease() == $releaseB);
        $this->assertFalse($discogsVideo->getLabel() == $labelB);
    }
    
    public function testIsEmpty(): void
    {
        $discogsVideo = new DiscogsVideo();
        $artistA = new Artist();

        $discogsVideo->addArtist($artistA)->removeArtist($artistA);

        $this->assertEmpty($discogsVideo->getUrl());
        $this->assertEmpty($discogsVideo->getArtists());
        $this->assertEmpty($discogsVideo->getRelease());
        $this->assertEmpty($discogsVideo->getLabel());
    }

}
    