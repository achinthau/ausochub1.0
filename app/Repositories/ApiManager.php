<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class ApiManager
{
    public static function updateSkill($data)
    {

        $client = new Client();
        $options = [
            'multipart' => $data
        ];
        
        try {
            $res = $client->request('POST', config('auso.api_url').'/Client/userAPI/queueLoginLogoutAPIL.php', $options);
            Log::info(config('auso.api_url').'/Client/userAPI/queueLoginLogoutAPIL.php');
            Log::info($options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function startBreak($data)
    {
        $client = new Client();
        $options = [
            'multipart' => $data
        ];
        
        try {
            $res = $client->request('POST', config('auso.api_url').'/Client/userAPI/breakAPI.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function createExtension($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/auExtenAPI/create_exten.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
    
    }

    public static function updateExtension($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/auExtenAPI/update_exten.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
    
    }

    public static function assignExtension($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/assignExtensionAPI/assignExtension.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public static function createMohName($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/mohAPI/createMOH.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public static function createSkill($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/queueCreateAPI/createQueue.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public static function listentCall($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/ongoingcalllisten.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public static function uploadMohFile($data)
    {
        $options = [
            'multipart' => $data
        ];

        $client = new Client();
        try {
            $res = $client->request('POST', config('auso.api_url').'/mohAPI/uploadMOH.php', $options);
            return $res->getStatusCode();
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
}
