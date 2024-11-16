<?php
namespace App\Services;

use Facebook\Facebook;

class FacebookService
{
    protected $fb;
    
    public function __construct()
    {
        $this->fb = new Facebook([
            'app_id' => config('services.facebook.client_id'),
            'app_secret' => config('services.facebook.client_secret'),
            'default_access_token' => config('services.facebook.access_token'),
        ]);
    }

    public function sharePost($title, $description, $url, $image = null)
    {
        $data = [
            'message' => $title . "\n\n" . $description,
            'link' => $url,
        ];

        if ($image) {
            $data['picture'] = $image;
        }

        return $this->fb->post('/' . config('services.facebook.page_id') . '/feed', $data);
    }
}

?>