<?php
use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

    /**
     * indexAction function
     * Takes the input/Location name from user and hits the api using location name
     * @return void
     */
    public function indexAction()
    {
        if ($this->request->ispost()) {
            
            $key = '0bab7dd1bacc418689b143833220304';
            
            $query = urlencode($this->request->getPost('query'));
            
            $url = "http://api.weatherapi.com/v1/search.json?key=$key&q=$query";

            $searchResult = $this->curlAction($url);

            $this->view->searchResult = $searchResult;
        }
    }

    /**
     * singleLocationAction function
     * Takes name of location as input and hits the api using name and displays information
     * @param [type] $params
     * @return void
     */
    public function singleLocationAction($params)
    {
        $this->view->location = $params;
    }


    public function apiCallAction($location, $api)
    {
        $key = '0bab7dd1bacc418689b143833220304';

        $now = date("Y-m-d", strtotime("-7 days"));
        
        $url = "http://api.weatherapi.com/v1/$api.json?key=$key&q=$location";

        if ($api == 'history') {
            $url = "http://api.weatherapi.com/v1/$api.json?key=$key&q=$location&dt=$now";
        }

        if ($api == 'forecast') {
            $url = "http://api.weatherapi.com/v1/$api.json?key=$key&q=$location&days=3";
        }

        if ($api == 'air_quality') {
            $url = "http://api.weatherapi.com/v1/current.json?key=$key&q=$location&aqi=yes";
        }

        if ($api == "weather_Alert") {
            $url = "http://api.weatherapi.com/v1/forecast.json?key=$key&q=$location&alerts=yes";
        }

        $searchResult = $this->curlAction($url);
        
        $this->view->searchResult = $searchResult;
        $this->view->api = $api;
        $this->view->location = $location;
    }

    public function curlAction($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $searchResult = json_decode(curl_exec($ch));
        curl_close($ch);
        if (count((array)$searchResult)==0) {
            die("Invalid Request");
        }
        return $searchResult;
    }
}
