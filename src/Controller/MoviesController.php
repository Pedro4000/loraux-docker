<?php

namespace App\Controller;

use App\Entity\{Artist, Label, Release, User};
use Google_Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class MoviesController extends AbstractController
{
    public function __construct (
        private RequestStack $requestStack
    ) { }

    #[Route('/movies', name: 'movies')]
    /**
     * @param Request $request
     */
    public function MoviesAction(Request $request): int
    {
        $session = $this->requestStack->getSession();
        return 3;
    }

}
