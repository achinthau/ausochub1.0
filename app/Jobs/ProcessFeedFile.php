<?php

namespace App\Jobs;

use App\Models\CxTicket;
use App\Models\Feed;
use App\Models\FeedContact;
use App\Models\FeedContactValid;
use App\Models\FeedContactInValid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessFeedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $feedId;
    protected string $type;
    protected int $batchSize = 5000;

    public function __construct(int $feedId, string $type)
    {
        $this->feedId = $feedId;
        $this->type = $type;
    }

    function excelDateToYmd($excelDate) {
    if (!is_numeric($excelDate)) {
        return null;
    }

    // Excel’s epoch starts at 1900-01-01
    // Subtract 2 to fix Excel leap year bug (1900 treated as leap year)
    $unixTimestamp = ($excelDate - 25569) * 86400;
    return gmdate('Y-m-d', $unixTimestamp);
}

    public function handle()
    {
        $feed = Feed::find($this->feedId);
        if (!$feed) {
//             Log::error("ProcessFeedFile: Feed not found with ID {$this->feedId}");
            return;
        }

//         Log::info("Validating contacts for Feed ID: {$this->feedId}");

        $totalValid = 0;
        $totalInvalid = 0;

        FeedContact::where('feed_id', $this->feedId)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($contacts) use (&$totalValid, &$totalInvalid) {
                $valid = [];
                $invalid = [];
                $satisfactionFeeds = [];

                foreach ($contacts as $contact) {
                    $phone1 = $contact->contact_no_01 ?? '';
                    $phone2 = $contact->contact_no_02 ?? '';
                    $cleanPhone = preg_replace('/\D/', '', $phone1);
                    $cleanPhone2 = preg_replace('/\D/', '', $phone2);

                    // Normalize Sri Lankan numbers
                    if (str_starts_with($cleanPhone, '94')) {
                        $cleanPhone = '0' . substr($cleanPhone, 2);
                    } elseif (str_starts_with($cleanPhone, '9') && strlen($cleanPhone) === 9) {
                        $cleanPhone = '0' . $cleanPhone;
                    } elseif (str_starts_with($cleanPhone, '0')) {
                        // Already starts with 0, assume valid format if length checks pass later
                    }

                    if (str_starts_with($cleanPhone2, '94')) {
                        $cleanPhone2 = '0' . substr($cleanPhone2, 2);
                    } elseif (str_starts_with($cleanPhone2, '9') && strlen($cleanPhone2) === 9) {
                        $cleanPhone2 = '0' . $cleanPhone2;
                    } elseif (str_starts_with($cleanPhone2, '0')) {
                        // Already starts with 0
                    }

                    $data = [
                        'feed_id' => $contact->feed_id,
                        'contact_no_01' => $cleanPhone,
                        'contact_no_02' => $cleanPhone2,
                        'priority_field' => $contact->priority_field,
                        'lang' => $contact->lang,
                        'data' => $contact->data,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $isValid = false;
                    if ($cleanPhone && preg_match('/^\d{9,10}$/', $cleanPhone)) {
                        $isValid = true;
                    } elseif ($cleanPhone2 && preg_match('/^\d{9,10}$/', $cleanPhone2)) {
                        $isValid = true;
                    }

                    if ($isValid) {
                        $valid[] = $data;
                        if ($this->type == 'satisfaction') {
                            $json = json_decode($contact->data, true) ?? [];

                            // Extract and prepare CxTicket fields directly here
                            $category = $json['category'] ?? '';
                            $product = $json['product'] ?? null;
                            $model = $json['model'] ?? null;
                            $workOrderNo = $contact->priority_field ?? '';
                            $serviceCenter = $json['service_center'] ?? '';
                            $warrantyStatus = $json['warranty_status'] ?? '';
                            $rawSoldDate = $json['sold_date'] ?? null;
                            $soldDate = $this->excelDateToYmd($rawSoldDate);
                            $customerName = $json['customer_name'] ?? null;
                            $customerAddress = $json['customer_address'] ?? '';
                            $contact01 = $cleanPhone ?? null;
                            $contact02 = $cleanPhone2 ?? null;
                            $technicianName = $json['technician_name'] ?? '';
                            $technicianContact = $json['technician_contact'] ?? '';

                            $cxTickets[] = [
                                'category' => $category,
                                'product' => $product,
                                'model' => $model,
                                'work_order_no' => $workOrderNo,
                                'service_center' => $serviceCenter,
                                'warranty_status' => $warrantyStatus,
                                'sold_date' => $soldDate,
                                'customer_name' => $customerName,
                                'customer_address' => $customerAddress,
                                'customer_contact_01' => $contact01,
                                'customer_contact_02' => $contact02,
                                'technician_name' => $technicianName,
                                'technician_contact' => $technicianContact,
                                'status' => 'Closed',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    } else {
                        $invalid[] = $data;
                    }

//                     Log::debug("Contact {$contact->id} | Original: {$phone1} → Normalized: {$cleanPhone} | " .
//                         ($isValid ? 'VALID' : 'INVALID'));
                }

                if (!empty($valid)) {
                    FeedContactValid::insert($valid);
                    $totalValid += count($valid);
//                     Log::info(count($valid) . " valid contacts inserted.");
                }

                if (!empty($invalid)) {
                    FeedContactInValid::insert($invalid);
                    $totalInvalid += count($invalid);
//                     Log::info(count($invalid) . " invalid contacts inserted.");
                }

                if (!empty($cxTickets)) {

                    // Bulk insert 
                    try {
                        CxTicket::insert($cxTickets);
//                         Log::info(count($cxTickets) . " CxTicket records inserted for satisfaction feed.");
                    } catch (\Exception $e) {
//                         Log::error("Failed to insert CxTickets: " . $e->getMessage());
                        // Optionally, handle individually or skip
                        // foreach ($cxTickets as $ticket) {
                        //     try {
                        //         CxTicket::create($ticket);
                        //     } catch (\Exception $innerE) {
                        //         Log::warning("Skipped individual CxTicket insert: " . $innerE->getMessage());
                        //     }
                        // }
                    }
                }
            });

        $feed->update(['status' => 'completed']);
//         Log::info("Feed ID {$this->feedId} validation finished. Valid: {$totalValid}, Invalid: {$totalInvalid}");
    }

    
}
