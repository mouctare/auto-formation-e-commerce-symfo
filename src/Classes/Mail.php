<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = '69fee46bf01f6cd3d5cea72e2546e86e';
    private $api_key_secret = 'caef4771428cee886a317e626980e733';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
     
      $body = [
            'Messages' => [
              [
                'From' => [
                  'Email' => "mouctard78@gmail.com",
                  'Name' => "La Boutique"
                ],
                'To' => [
                  [
                    'Email' => $to_email,
                    'Name' => $to_name
                  ]
                ],
                'TemplateID' => 2563577,
                'TemplateLanguage' => true,
                'Subject' => $subject,
                'Variables' => [
                    "content" => $content
                ]
                      ]
                    ]
                  ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && ($response->getData());
    }
}