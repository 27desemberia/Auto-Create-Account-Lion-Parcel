<?php

require_once 'vendor/autoload.php';
require_once 'simple_html_dom.php';
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;


class InformationHuman
{
    public function getInfoUser()
    {
        // $http = HttpClient::create();
        $client = new Client();
        // $http = HttpClient::create();
        $crawel = $client->request('GET', 'https://name-fake.com/id_ID');
        $firsName = $crawel->filter('#copy1')->text();
        $lastName = $crawel->filter('#copy2')->text();
        $email = $crawel->filter('#copy4')->text();
        $username = $crawel->filter('#copy3')->text();
        $password = $crawel->filter('#copy5')->text();
        return [
            'firstName' => $firsName,
            'lastName' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $password,

        ];
     
    }


}


// $test = new Travelio;
// var_dump($test->getInfoUser());
