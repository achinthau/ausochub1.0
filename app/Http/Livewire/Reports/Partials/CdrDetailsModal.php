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
    public $summary;
    public $reaction;
    public $isProcessing = false;

    protected $listeners = ['show' => 'showDetailsModal'];

    public function render()
    {
        return view('livewire.reports.partials.cdr-details-modal');
    }

    public function showDetailsModal($src, $dst, $direction, $extension, $uniqueid)
    {
        $this->showCdrDetailsModal = true;
        $this->uniqueid = $uniqueid;
        $this->transcription = null;
        $this->isProcessing = true;

        if ($direction == 'Dial') {
            $caller = $this->findName($extension);
            $callee = $this->findName($src);
        } elseif ($direction == 'Queue') {
            $callee = $this->findName($extension);
            $caller = $this->findName($src);
        }

        $this->callerName = $caller['name'];
        $this->callerEmail = $caller['email'];
        $this->callerAddress = $caller['address'];
        $this->callerWhatsapp = $caller['whatsapp'];
        $this->callerNic = $caller['nic'];

        $this->calleeName = $callee['name'];
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
                'name' => $number,
                'email' => null,
                'type' => 'system',
                'address' => null,
                'whatsapp' => null,
                'nic' => null,
            ];
        }

        if (strlen($number) === 3) {
            $user = User::where('extension', $number)->first();

            if ($user) {
                return [
                    'name' => $user->name,
                    'email' => null,
                    'type' => 'user',
                    'address' => null,
                    'whatsapp' => null,
                    'nic' => null,
                ];
            }

            return [
                'name' => "Ext {$number}",
                'email' => null,
                'type' => 'user',
                'address' => null,
                'whatsapp' => null,
                'nic' => null,
            ];
        }

        $lead = Lead::where('contact_number', $number)
            ->orWhere('contact_number_2', $number)
            ->first();

        if ($lead) {
            return [
                'name' => trim($lead->first_name . ' ' . $lead->last_name),
                'email' => $lead->email,
                'type' => 'lead',
                'address' => $lead->address_line_1 . ' ' . $lead->address_line_2 . ' ' . $lead->city,
                'whatsapp' => $lead->whatsapp,
                'nic' => $lead->nic,
            ];
        }

        return [
            'name' => "Unknown ({$number})",
            'email' => null,
            'type' => 'unknown',
            'address' => null,
            'whatsapp' => null,
            'nic' => null,
        ];
    }

    public function getCallTranscription($uniqueid)
    {
        $this->uniqueid = $uniqueid;
        $dbRecord = CallRecordingTranscript::where('uniqueid', $uniqueid)->first();
        if ($dbRecord) {
            $this->transcription = $dbRecord->transcript;
            $this->summary = $dbRecord->summary;
            $this->reaction = $dbRecord->reaction;
            $this->isProcessing = false;
            return;
        }

        $hasFile = Storage::disk('asterisk-media-server')->has($uniqueid . '.wav');
        if ($hasFile) {
            $this->transcription = 'Analyzing audio with Gemini 2.0...';
            $this->isProcessing = true;

            try {
                $path = Storage::disk('asterisk-media-server')->path($uniqueid . '.wav');
                $content = Storage::disk('asterisk-media-server')->get($uniqueid . '.wav');

                if (!$content) {
                    throw new \Exception("Could not retrieve audio file content from Asterisk server ($path).");
                }

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
                
                if (empty($keyPath) || !file_exists($keyPath)) {
                    throw new \Exception("Google Cloud key file not found or path not configured. Path: " . ($keyPath ?: 'EMPTY'));
                }

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

                // 1. Get Transcription
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

                $rawText = null;
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $rawText = $data['candidates'][0]['content']['parts'][0]['text'];
                    $rawText = trim(str_ireplace(['transcription:', '```', 'sinhala:'], '', $rawText));
                }

                if (!$rawText || strtolower($rawText) === 'no speech detected') {
                    $this->transcription = 'The AI could not detect any speech in this audio.';
                    $this->summary = null;
                    $this->reaction = null;
                } else {
                    $this->transcription = $rawText;

                    // 2. Generate summary and reaction from transcript
                    $summaryPrompt = "Summarize the following Sinhala call transcript in 1-2 sentences. Output in Sinhala.\nTranscript: {$rawText}";
                    $reactionPrompt = "Based on the following Sinhala call transcript, classify the caller's reaction as one of: happy, angry, or normal. Output only one word: happy, angry, or normal.\nTranscript: {$rawText}";

                    // Summary
                    $summaryResp = $client->post($url, [
                        'json' => [
                            'contents' => [
                                [
                                    'role' => 'user',
                                    'parts' => [
                                        [ 'text' => $summaryPrompt ]
                                    ]
                                ]
                            ],
                            'generationConfig' => [
                                'temperature' => 0.2,
                                'maxOutputTokens' => 256,
                            ]
                        ]
                    ]);
                    $summaryData = json_decode($summaryResp->getBody()->getContents(), true);
                    $summary = isset($summaryData['candidates'][0]['content']['parts'][0]['text']) ? trim($summaryData['candidates'][0]['content']['parts'][0]['text']) : null;

                    // Reaction
                    $reactionResp = $client->post($url, [
                        'json' => [
                            'contents' => [
                                [
                                    'role' => 'user',
                                    'parts' => [
                                        [ 'text' => $reactionPrompt ]
                                    ]
                                ]
                            ],
                            'generationConfig' => [
                                'temperature' => 0.0,
                                'maxOutputTokens' => 8,
                            ]
                        ]
                    ]);
                    $reactionData = json_decode($reactionResp->getBody()->getContents(), true);
                    $reaction = isset($reactionData['candidates'][0]['content']['parts'][0]['text']) ? strtolower(trim($reactionData['candidates'][0]['content']['parts'][0]['text'])) : null;
                    if (!in_array($reaction, ['happy', 'angry', 'normal'])) {
                        $reaction = 'normal';
                    }

                    $this->summary = $summary;
                    $this->reaction = $reaction;

                    CallRecordingTranscript::create([
                        'uniqueid' => $uniqueid,
                        'transcript' => $rawText,
                        'summary' => $summary,
                        'reaction' => $reaction,
                    ]);
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = $e->getResponse();
                $responseBody = $response->getBody()->getContents();
                $this->transcription = 'Google API Error: ' . $responseBody;
                $this->summary = null;
                $this->reaction = null;
            } catch (\Throwable $e) {
                $this->transcription = 'Technical Error: ' . $e->getMessage();
                $this->summary = null;
                $this->reaction = null;
            }

            $this->isProcessing = false;
            return;
        }

        $this->transcription = 'No transcription available for this call.';
        $this->summary = null;
        $this->reaction = null;
        $this->isProcessing = false;
    }

    public function loadTranscription()
    {
        if ($this->uniqueid) {
            $this->getCallTranscription($this->uniqueid);
        }
    }
}
