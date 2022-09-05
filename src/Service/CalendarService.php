<?php

namespace App\Service ;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;




class CalendarService extends AbstractController
{
    /**
     * @var Google_Client
     */
    protected $client;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {

        $this->session = $session;
        $this->router = $router;
        /*        dump(4);die;
        $client = new Google_Client();
        $client->setAuthConfig('client_secret.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);

        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $this->client = $client;*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Google_Exception
     */
    public function createGoogleClient($client = null)
    {
            $this->session->set('auth_code', null);
            $client = new Google_Client();
            $client->setAuthConfig('credentials.json');
            $client->addScope(Google_Service_Calendar::CALENDAR);

            $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
            $client->setHttpClient($guzzleClient);
            $client->setRedirectUri('http://127.0.0.1:8000/google_redirect_for_calendar');
            $authUrl = $client->createAuthUrl();


        if ($this->session->get('access_token')) {
           $client->setAccessToken($this->session->get('access_token'));
           if ($client->isAccessTokenExpired()) {
               // Refresh the token if possible, else fetch a new one.
               if ($client->getRefreshToken()) {

                   $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
               } else {
                   $this->oauth($client);
               }
           }
            $client->setAccessToken($this->session->get('access_token'));
            $service = new Google_Service_Calendar($client);
            $calendarId = 'primary';
            $results = $service->events->listEvents($calendarId);
            return $results->getItems();
        }
    }

    public function oauth($client)
    {
        $redirectUri = 'https%3A%2F%2Fdevelopers.google.com%2Foauthplayground';
        $stringUri = 'http://127.0.0.1:8000/google_redirect_for_calendar';
        $client->setRedirectUri($stringUri);

        if ($this->session->get('auth_code')) {
            $authCode = $this->session->get('auth_code') ;
            $client->authenticate($authCode);
            $client->getAccessToken();
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $this->session->set('access_token', $accessToken);
            $this->importCalendar();
        } else {
            $authUrl = $client->createAuthUrl();
            $redirectUri = $this->router->generate('google_redirect_for_calendar');
            return [false, $authUrl];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendar.createEvent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $startDateTime = $request->start_date;
        $endDateTime = $request->end_date;

        if ($this->session->get('access_token')) {
            $this->client->setAccessToken($this->session->get('access_token'));
            $service = new Google_Service_Calendar($this->client);

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
    }

    /**
     * Display the specified resource.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($eventId)
    {
        if ($this->session->get('access_token')) {
            $this->client->setAccessToken($this->session->get('access_token'));

            $service = new Google_Service_Calendar($this->client);
            $event = $service->events->get('primary', $eventId);

            if (!$event) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'data' => $event]);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $eventId)
    {
        if ($this->session->get('access_token')) {
            $this->client->setAccessToken($this->session->get('access_token'));
            $service = new Google_Service_Calendar($this->client);

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $eventId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($eventId)
    {
        if ($this->session->get('access_token')) {
            $this->client->setAccessToken($this->session->get('access_token'));
            $service = new Google_Service_Calendar($this->client);

            $service->events->delete('primary', $eventId);

        } else {
            return redirect()->route('oauthCallback');
        }
    }

}