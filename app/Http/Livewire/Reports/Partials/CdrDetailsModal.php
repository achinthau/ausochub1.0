<?php

namespace App\Http\Livewire\Reports\Partials;

use App\Models\Cdr;
use App\Models\Lead;
use App\Models\User;
use Livewire\Component;
use App\Models\CallRecordingTranscript;
use Illuminate\Support\Facades\Storage;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\File;

class CdrDetailsModal extends Component
{
    public $showCdrDetailsModal = false;
    public $callerName;
    public $calleeName;
    public $calleeNic;
    public $calleeAddress;
    public $calleeWhatsapp;
    public $calleeEmail;
    public $callerEmail;
    public $callerAddress;
    public $callerWhatsapp;
    public $callerNic;
    public $uniqueid;
    public $transcription;
    public $isProcessing = false;

    protected $listeners = ['show' => 'showDetailsModal'];
    public function render()
    {
        return view('livewire.reports.partials.cdr-details-modal');
    }

    public function showDetailsModal($src, $dst,$direction,$extension,$uniqueid)
{
    // dd($src.$dst);

        $this->showCdrDetailsModal = true;
        $this->uniqueid = $uniqueid;
        $this->transcription = null;
        $this->isProcessing = true;

    // dd($direction);
    if($direction == 'Dial')
    {        
        // dd($extension);
        $caller = $this->findName($extension);
        $callee = $this->findName($src);

        // dd($caller);
    } elseif($direction == 'Queue')
    {
        $callee = $this->findName($extension);
        $caller = $this->findName($src);
    }
    
    

    $this->callerName  = $caller['name'];
    $this->callerEmail = $caller['email'];
    $this->callerAddress = $caller['address'];
    $this->callerWhatsapp = $caller['whatsapp'];
    $this->callerENicl = $caller['nic'];

    $this->calleeName  = $callee['name'];
    $this->calleeEmail = $callee['email'];
    $this->calleeAddress = $callee['address'];
    $this->calleeWhatsapp = $callee['whatsapp'];
    $this->calleeNic = $callee['nic'];

    
}


    protected function findName($number)
{
    $number = trim($number);

    
    if (!preg_match('/^\d+$/', $number)) {
        return [
            'name'  => $number,
            'email' => null,
            'type'  => 'system',
            'address' => null,
            'whatsapp' => null,
            'nic' => null,
        ];
    }

    
    if (strlen($number) === 3) {
        $user = User::where('extension', $number)->first();

        if ($user) {
            return [
                'name'     => $user->name,
                'email'    =>  null,
                'type'     => 'user',
                'address'  => null,
                'whatsapp' => null,
                'nic'      => null,
            ];
        }

        return [
            'name'     => "Ext {$number}",
            'email'    => null,
            'type'     => 'user',
            'address'  => null,
            'whatsapp' => null,
            'nic'      => null,
        ];
    }

    
    $lead = Lead::where('contact_number', $number)
                ->orWhere('contact_number_2', $number)
                ->first();

    if ($lead) {
        return [
            'name'     => trim($lead->first_name.' '.$lead->last_name),
            'email'    => $lead->email,
            'type'     => 'lead',
            'address'  => $lead->address_line_1.' '.$lead->address_line_2.' '.$lead->city,
            'whatsapp' => $lead->whatsapp,
            'nic'      => $lead->nic,
        ];
    }

    
    return [
        'name'     => "Unknown ({$number})",
        'email'    => null,
        'type'     => 'unknown',
        'address'  => null,
        'whatsapp' => null,
        'nic'      => null,
    ];
}

    public function getCallTranscription($uniqueid)
    {
        $this->uniqueid = $uniqueid;
        $dbRecord = CallRecordingTranscript::where('uniqueid', $uniqueid)->first();
        if($dbRecord){
            $this->transcription = $dbRecord->transcript;
            $this->isProcessing = false;
            return;
        }

    if (!$dbRecord) {
        $hasFile = Storage::disk('asterisk-media-server')->has($uniqueid . '.wav');
        if($hasFile){

            $filepath = 'monitor_1/' . $uniqueid . '.wav';
            $this->transcription = 'Analyzing audio with Gemini 2.0...';
            $this->isProcessing = true;

            try {
            $path = Storage::disk('public')->path($filepath);
            $content = file_get_contents($path);
            $base64Audio = base64_encode($content);
            
            // Detect mime type
            $mimeType = File::mimeType($path);
            
            // Fix for microphone recordings which often get detected as octet-stream or video/webm
            if ($mimeType === 'application/octet-stream' || empty($mimeType)) {
                $mimeType = 'audio/webm';
            }
            
            // Gemini is very strict: if it's a webm audio, it MUST be audio/webm, not video/webm
            if (str_contains($mimeType, 'webm')) {
                $mimeType = 'audio/webm';
            }

            // Clean up mime type (remove codecs info if present)
            if (str_contains($mimeType, ';')) {
                $mimeType = explode(';', $mimeType)[0];
            }
            
            // Setup Credentials
            $keyPath = config('services.google.cloud_key_path');
            
            $scopes = [
                'https://www.googleapis.com/auth/cloud-platform',
                'https://www.googleapis.com/auth/generative-language',
            ];
            $creds = new ServiceAccountCredentials($scopes, $keyPath);
            
            $stack = HandlerStack::create();
            $middleware = new AuthTokenMiddleware($creds);
            $stack->push($middleware);

            $client = new Client([
                'handler' => $stack,
                'auth' => 'google_auth',
                'timeout' => 300, 
            ]);

            // Using Gemini 2.0 Flash on the Generative Language API
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

            $response = $client->post($url, [
                'json' => [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $base64Audio
                                    ]
                                ],
                                [
                                    'text' => 'This is a Sinhala audio clip. Please transcribe it to Sinhala text exactly as it is spoken. Do not translate it. Output ONLY the transcription and nothing else. If you can\'t hear anything, say "No speech detected".'
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.0,
                        'maxOutputTokens' => 2048,
                    ]
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'];
                
                // Cleanup
                $rawText = trim(str_ireplace(['transcription:', '```', 'sinhala:'], '', $rawText));
                
                if (strtolower($rawText) === 'no speech detected') {
                    $this->transcription = 'The AI could not detect any speech in this audio.';
                } else {
                    $this->transcription = $rawText;
                    // $this->singlishTranscription = SinhalaTransliterator::transliterate($this->transcription);
                    CallRecordingTranscript::create([
                        'uniqueid' => $uniqueid,
                        'transcript' => $rawText,
                    ]);
                }
            } else {
                throw new \Exception("AI response format error. Data: " . json_encode($data));
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();
            $this->transcription = 'Google API Error: ' . $responseBody;
        } catch (\Exception $e) {
            $this->transcription = 'Technical Error: ' . $e->getMessage();
        }

        $this->isProcessing = false;

            return;
        }
        

        $this->transcription = 'No transcription available for this call.';
        $this->isProcessing = false;
        return;
    }
}

    public function loadTranscription()
    {
        if ($this->uniqueid) {
            $this->getCallTranscription($this->uniqueid);
        }
    }

}
