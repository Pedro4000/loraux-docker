<?php

namespace App\Service ;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RequestStack, Request, JsonResponse};
use Google_Client;
use Google_Service_Calendar;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class CalendarService extends AbstractController
{


    public function __construct(
        private RequestStack $requestStack, 
        private UrlGeneratorInterface $router,
        ) { }

    
    public function createGoogleClient($client = null)
    {
        $session = $this->requestStack->getSession();
        $session->set('auth_code', null);
        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $client->setRedirectUri('http://127.0.0.1:8000/google_redirect_for_calendar');
        $authUrl = $client->createAuthUrl();


        if ($session->get('access_token')) {
           $client->setAccessToken($session->get('access_token'));
           if ($client->isAccessTokenExpired()) {
               // Refresh the token if possible, else fetch a new one.
               if ($client->getRefreshToken()) {

                   $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
               } else {
                   $this->oAuth($client);
               }
           }
            $client->setAccessToken($session->get('access_token'));
            $service = new Google_Service_Calendar($client);
            $calendarId = 'primary';
            $results = $service->events->listEvents($calendarId);
            return $results->getItems();
        }
    }

    public function oAuth($client)
    {
        $session = $this->requestStack->getSession();
        $redirectUri = 'https%3A%2F%2Fdevelopers.google.com%2Foauthplayground';
        $stringUri = 'http://127.0.0.1:8000/google_redirect_for_calendar';
        $client->setRedirectUri($stringUri);

        if ($session->get('auth_code')) {
            $authCode = $session->get('auth_code') ;
            $client->authenticate($authCode);
            $client->getAccessToken();
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $session->set('access_token', $accessToken);
            // $this->importCalendar();
        } else {
            $authUrl = $client->createAuthUrl();
            $redirectUri = $this->router->generate('google_redirect_for_calendar');
            return [false, $authUrl];
        }
    }

    public function create()
    {
        /*
        return view('calendar.createEvent');
        */
    }


    
    public function store(Request $request)
    {
        $session = $this->requestStack->getSession();
        $startDateTime = $request->get('start_date');
        $endDateTime = $request->get('end_date');
        $client = new Google_Client();
        /*
        if ($session->get('access_token')) {
            $client->setAccessToken($session->get('access_token'));
            $service = new Google_Service_Calendar($client);

            $calendarId = 'primary';
            $event = new Google_Service_Calendar_Event([
                'summary' => $request->title,
                'description' => $request->description,
                'start' => ['dateTime' => $startDateTime],
                'end' => ['dateTime' => $endDateTime],
                'reminders' => ['useDefault' => true],
            ]);
            $results = $service->events->insert($calendarId, $event);
            
            if (!$results) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'message' => 'Event Created']);
        } else {
            return redirect()->route('oauthCallback');
        }
        */
    }


    public function show($eventId)
    {
        $session = $this->requestStack->getSession();
        $client = new Google_Client();
        /*

        if ($session->get('access_token')) {
            $client->setAccessToken($session->get('access_token'));

            $service = new Google_Service_Calendar($client);
            $event = $service->events->get('primary', $eventId);

            if (!$event) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $event]);

        } else {
            return redirect()->route('oauthCallback');
        }
        */
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $eventId)
    {
        $session = $this->requestStack->getSession();
        /*
        $client = new Google_Client();

        if ($session->get('access_token')) {
            $client->setAccessToken($session->get('access_token'));
            $service = new Google_Service_Calendar($client);

            $startDateTime = Carbon::parse($request->start_date)->toRfc3339String();

            $eventDuration = 30; //minutes

            if ($request->has('end_date')) {
                $endDateTime = Carbon::parse($request->end_date)->toRfc3339String();

            } else {
                $endDateTime = Carbon::parse($request->start_date)->addMinutes($eventDuration)->toRfc3339String();
            }

            // retrieve the event from the API.
            $event = $service->events->get('primary', $eventId);

            $event->setSummary($request->title);

            $event->setDescription($request->description);

            //start time
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime($startDateTime);
            $event->setStart($start);

            //end time
            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime($endDateTime);
            $event->setEnd($end);

            $updatedEvent = $service->events->update('primary', $event->getId(), $event);


            if (!$updatedEvent) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $updatedEvent]);

        } else {
            return redirect()->route('oauthCallback');
        }
        */
    }

    public function destroy($eventId)
    {
        /*
        $client = new Google_Client();

        if ($session->get('access_token')) {
            $client->setAccessToken($session->get('access_token'));
            $service = new Google_Service_Calendar($client);

            $service->events->delete('primary', $eventId);

        } else {
            return redirect()->route('oauthCallback');
        }
        */
    }

}