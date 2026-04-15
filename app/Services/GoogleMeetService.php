<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Oauth2;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleMeetService
{
    private GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();

        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));

        $this->client->addScope('openid');
        $this->client->addScope(GoogleCalendar::CALENDAR);
        $this->client->addScope(GoogleCalendar::CALENDAR_EVENTS);
        $this->client->addScope(Oauth2::USERINFO_EMAIL);

        $this->client->setAccessType('offline');
        // Account chooser + consent (refresh token). Avoid login_hint so teachers can pick the Gmail for Meet host.
        $this->client->setPrompt('select_account consent');
    }

    public function getAuthUrl(): string
    {
        $this->client->setLoginHint('');

        return $this->client->createAuthUrl();
    }

    public function authenticate(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception('Error fetching access token: ' . $token['error']);
        }

        return $token;
    }

    public function setAccessToken(array|string $token): array
    {
        if (is_string($token)) {
            $token = json_decode($token, true);
        }

        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                return $this->client->getAccessToken();
            }
        }

        return $token;
    }

    public function isTokenValid(User $user): bool
    {
        $token = $user->google_token;
        if (empty($token)) {
            return false;
        }

        try {
            $this->setAccessToken($token);
            return !$this->client->isAccessTokenExpired() || $this->client->getRefreshToken();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createMeetingWithLink(
        User $user,
        string $title,
        string $description,
        string $startTime,
        string $endTime
    ): array {
        $token = $user->google_token;
        if (empty($token)) {
            throw new \Exception('Google Calendar not connected. Please connect your Google account first.');
        }

        $tokenArray = is_string($token) ? json_decode($token, true) : $token;
        $refreshedToken = $this->setAccessToken($tokenArray);

        if (json_encode($refreshedToken) !== json_encode($tokenArray)) {
            $user->update(['google_token' => json_encode($refreshedToken)]);
        }

        $service = new GoogleCalendar($this->client);

        $tz = config('app.timezone', 'UTC');
        $startAt = Carbon::parse($startTime, $tz);
        $endAt = Carbon::parse($endTime, $tz);

        $event = new GoogleEvent([
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => $startAt->format('Y-m-d\TH:i:s'),
                'timeZone' => $tz,
            ],
            'end' => [
                'dateTime' => $endAt->format('Y-m-d\TH:i:s'),
                'timeZone' => $tz,
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => uniqid('meet_'),
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet',
                    ],
                ],
            ],
        ]);

        $calendarEvent = $service->events->insert('primary', $event, [
            'conferenceDataVersion' => 1,
        ]);

        $meetLink = $calendarEvent->getHangoutLink();

        if (!$meetLink) {
            throw new \Exception('Failed to generate Google Meet link. Please try again.');
        }

        return [
            'event_id' => $calendarEvent->getId(),
            'meet_link' => $meetLink,
            'html_link' => $calendarEvent->getHtmlLink(),
        ];
    }

    /**
     * Requires OAuth scope userinfo.email and a valid access token on the client.
     */
    public function fetchGoogleAccountEmail(): ?string
    {
        try {
            $oauth2 = new Oauth2($this->client);

            return $oauth2->userinfo->get()->getEmail() ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function persistRefreshedTokenIfChanged(User $user, array $tokenBeforeSetAccess): void
    {
        $current = $this->client->getAccessToken();
        if (! is_array($current)) {
            return;
        }
        if (json_encode($current) === json_encode($tokenBeforeSetAccess)) {
            return;
        }
        $user->update(['google_token' => json_encode($current)]);
    }

    /** @return array<string, mixed>|null */
    public function getClientAccessTokenArray(): ?array
    {
        $t = $this->client->getAccessToken();

        return is_array($t) ? $t : null;
    }
}
