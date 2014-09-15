<?php
/**
* Copyright 2013 ShareInSocial (by CoffeeCode).
* http://shareinsocial.com/
*/


if (!function_exists('curl_init')) {
    throw new Exception('ShareInSocial needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('ShareInSocial needs the JSON PHP extension.');
}

/**
* Thrown when an API call returns an exception.
* Version V. 0.1
* @author CoffeeCode <office@coffecode.ro>
*/
class Shareinsocial
{
    /**
     * Domain.
     */
    const DOMAIN = 'http://localhost/shareinsocial.com/public/api';

    /**
     * Version.
     */
    const VERSION = 'v1';

    /**
     * @var string
     */
    const HTTP_METHOD_POST = 'POST';

    /**
     * @var string
     */
    const HTTP_METHOD_GET = 'GET';

    /**
     * @var string
     */
    const HTTP_METHOD_DELETE = 'DELETE';

    /**
     * The Application token
     *
     * @var string
     */
    private $token;

    /**
     * Get api base url.
     */
    public static function getBaseUrl()
    {
        return self::DOMAIN . '/' . self::VERSION . '/';
    }

    /**
     * Initialize a ShareInSocial Application.
     *
     * @param string $token
     * @return array 
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get all campaigns
     * @return array     
     */
    public function getCampaigns()
    {
        $url = $this->createUrlByMethod('campaigns');

        return $this->call($url);
    }

    /**
     * Create a new campaign
     *
     * @param string $title
     * @param string $content
     * @param string $timeZone
     * @return array 
     */
    public function createCampaign($title, $content, $timeZone)
    {
        $url = $this->createUrlByMethod('campaigns');
        $params = array(
            'title' => $title,
            'content' => $content,
            'timeZone' => $timeZone,
        );

        return $this->call($url, self::HTTP_METHOD_POST, $params);
    }

    /**
     * Get campaign details
     *
     * @param int $campaignId
     * @return array 
     */
    public function getCampaignDetails($campaignId)
    {
        $url = 'campaigns/' . $campaignId;
        $url = $this->createUrlByMethod($url);

        return $this->call($url);
    }

    /**
     * Get campaign profiles
     *
     * @param int $campaignId
     * @return array 
     */
    public function getCampaignProfiles($campaignId)
    {
        $url = 'campaigns/' . $campaignId . ' /profiles';
        $url = $this->createUrlByMethod($url);

        return $this->call($url);
    }

    /**
     * Get profile details
     *
     * @param int $profileId
     * @return array 
     */
    public function getProfileDetails($profileId)
    {
        $url = 'profiles/' . $profileId;
        $url = $this->createUrlByMethod($url);

        return $this->call($url);
    }

    /**
     * Create schedule for a campaign
     *
     * @param int $campaignId
     * @param string $startDate
     * @param string $endDate
     * @param string $hours
     * @param string $days
     * @return array 
     */
    public function createScheduleForCampaign($campaignId, $startDate, $endDate, $hours, $days)
    {
        $params = array(
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hours' => $hours,
            'days' => $days,
        );
        $url = 'campaigns/' . $campaignId . '/schedules';
        $url = $this->createUrlByMethod($url);

        return $this->call($url, self::HTTP_METHOD_POST, $params);
    }

    /**
     * Get schedules from a campaign
     *
     * @param int $campaignId
     * @return array 
     */
    public function getSchedulesFromCampaign($campaignId)
    {
        $url = 'campaigns/' . $campaignId . '/schedules';
        $url = $this->createUrlByMethod($url);

        return $this->call($url);
    }

    /**
     * Remove profile from a campaign
     */
    public function removeProfileFromCampaign()
    {
        //to do
    }

    /**
     * Remove schedule from a campaign
     *
     * @param int $campaignId
     * @param int $scheduleId
     * @return array 
     */
    public function removeScheduleFromCampaign($campaignId, $scheduleId)
    {
        $url = 'campaigns/' . $campaignId . '/schedules/' . $scheduleId;
        $url = $this->createUrlByMethod($url);

        return $this->call($url, self::HTTP_METHOD_DELETE);
    }

    /**
     * Curl method
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @return array 
     */
    public function call($url, $method = self::HTTP_METHOD_GET, $params = array())
    {
        $urlParameters['token'] = $this->token; 

        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
        );

        if ($method == self::HTTP_METHOD_GET) {
            $urlParameters = array_merge($params, $urlParameters);
        }

        if ($method == self::HTTP_METHOD_POST) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $params;
        }

        if ($method == self::HTTP_METHOD_DELETE) {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        $url .= '?' . http_build_query($urlParameters);

        $options[CURLOPT_URL] = $url;

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, 1);
    }

    /**
     *
     * @param string $method
     * @return array 
     */
    private function createUrlByMethod($method)
    {
        return self::getBaseUrl() . $method; 
    }
}
