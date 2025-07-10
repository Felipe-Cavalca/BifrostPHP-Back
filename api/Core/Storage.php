<?php

namespace Bifrost\Core;

use Bifrost\Core\Settings;
use Bifrost\DataTypes\FilePath;
use Bifrost\DataTypes\Base64;
use Bifrost\DataTypes\ResponseStorage;
use Bifrost\DataTypes\Url;

class Storage
{
    private Settings $settings;
    private Url $url;
    private string $auth;

    public function __construct()
    {
        $this->settings = new Settings();
        $this->url = new Url($this->settings->BFR_API_STORAGE_HOST);
        $this->auth = $this->settings->BFR_API_STORAGE_API_KEY;
    }

    public function set(FilePath $file, Base64 $base64): ResponseStorage
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "base64Content" => (string) $base64
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->auth,
            "Content-Type: application/json",
            "Sync-Upload: true"
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception("Curl error: " . curl_error($ch));
        }

        curl_close($ch);

        return new ResponseStorage(json_decode($response, true));
    }

    public function get(FilePath $file): ResponseStorage
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->auth,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return new ResponseStorage(json_decode($response, true));
    }

    public function delete(FilePath $file): ResponseStorage
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . $file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->auth,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return new ResponseStorage(json_decode($response, true));
    }
}
