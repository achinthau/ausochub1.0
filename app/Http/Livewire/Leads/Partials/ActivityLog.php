<?php

namespace App\Http\Livewire\Leads\Partials;

use App\Models\CallCount;
use App\Models\CallCountReport;
use App\Models\FeedContactValid;
use App\Models\Lead;
use App\Models\TicketsDaily;
use App\Models\QueueCount;
use App\Models\QueueCountReport;
use Carbon\Carbon;
use Livewire\Component;


class ActivityLog extends Component
{
    public Lead $lead;
    public  $timelineLogs;
    public  $tickets;
    public  $callLogs;
    public  $outCallLogs;
    public  $callLogsArchived;
    public  $outCallLogsArchived;
    public  $isLoad = false;
    public  $loadMoreStatus = false;
    public  $fliterStatus = [2];
    public $expandedCallUniqueId = null;
    public $feedContactIds = [];
    public $commentLogs;


    protected $listeners = [
        'refreshTimeline' => 'refreshTimeline',
        'updateFeedContactIds' => 'updateFeedContactIds',
    ];


    public function updateFeedContactIds($ids = [])
    {
        $ids = is_array($ids) ? $ids : explode(',', (string) $ids);

        $this->feedContactIds = array_values(array_filter(array_map('intval', $ids)));
    }





    public function render()
    {
        return view('livewire.leads.partials.activity-log');
    }

    public function toggleCallTickets($uniqueid)
    {
        $this->expandedCallUniqueId = $this->expandedCallUniqueId === $uniqueid ? null : $uniqueid;
    }

    protected function ticketStatusClasses($statusId): array
    {
        return match ((int) $statusId) {
            1 => ['bg-gray-200', 'text-gray-700'],
            2 => ['bg-blue-200', 'text-blue-700'],
            3 => ['bg-amber-200', 'text-amber-700'],
            4 => ['bg-green-200', 'text-green-700'],
            5 => ['bg-red-200', 'text-red-700'],
            default => ['bg-slate-200', 'text-slate-700'],
        };
    }

    public function refreshTimeline($feedContactIds = null)
    {
        if (is_array($feedContactIds)) {
            $this->updateFeedContactIds($feedContactIds);
        }

        $timelineLogs = collect([]);
        $lead = $this->lead;
        $dailyTickets = TicketsDaily::with('category', 'status', 'outlet', 'items', 'items.item')
            ->where('lead_id', $lead->id)
            ->latest()
            ->get();
        $ticketsByCallUniqueId = $dailyTickets->whereNotNull('call_uniqueid')->groupBy('call_uniqueid');

        if (in_array(2, $this->fliterStatus)) {
            $this->callLogs = QueueCount::with('agentInfo')->where(function ($query) use ($lead) {
                $query->orWhere('ani', $lead->contact_number)
                    ->orWhere('dnis', $lead->contact_number);
            })->where('status', 2)->orderBy('date')->get();

            $this->outCallLogs = CallCount::with('agentInfo')->where(function ($query) use ($lead) {
                $query->orWhere('dnis', $lead->contact_number);
            })->where('direction', 'out')->orderBy('date')->get();



            // Map call logs
            $timelineLogs = $timelineLogs->merge($this->callLogs->map(function ($item) use ($ticketsByCallUniqueId) {
                $linkedTickets = $ticketsByCallUniqueId->get($item->uniqueid, collect([]));
                return [
                    'kind' => 'call',
                    'uniqueid' => $item->uniqueid,
                    'title' => 'Incoming Call',
                    'created_at' => $item->date,
                    'created_date' => $item->date->format('F jS, Y'),
                    'created_time' => $item->date->format('h:i A'),
                    'created_by' => $item->agentInfo ? $item->agentInfo->full_name : 'System Agent',
                    'icon' => 'icon-phone',
                    'bg-color' => 'bg-blue-200',
                    'icon-color' => 'text-blue-600 dark:text-blue-400',
                    'last-reaction' => $item->customer_reaction,
                    'last-comment' => $item->comment,
                    'ticket_count' => $linkedTickets->count(),
                    'latest_ticket_id' => $linkedTickets->first()?->id,
                    'latest_ticket_component' => ($linkedTickets->first()?->ticket_category_id == 3) ? 'orders.show' : 'tickets.show',
                    'latest_ticket_status' => $linkedTickets->first()?->status?->title,
                    'latest_ticket_status_id' => $linkedTickets->first()?->ticket_status_id,
                    'linked_tickets' => $linkedTickets->map(function ($ticket) {
                        [$bgColor, $textColor] = $this->ticketStatusClasses($ticket->ticket_status_id);

                        return [
                            'id' => $ticket->id,
                            'title' => $ticket->ticket_title,
                            'status' => $ticket->status?->title,
                            'status_id' => $ticket->ticket_status_id,
                            'status_bg' => $bgColor,
                            'status_text' => $textColor,
                            'created_at' => $ticket->created_at?->format('Y-m-d H:i:s'),
                        ];
                    })->values()->all(),
                ];
            }));

            // Map out call logs
            $timelineLogs = $timelineLogs->merge($this->outCallLogs->map(function ($item) use ($ticketsByCallUniqueId) {
                $linkedTickets = $ticketsByCallUniqueId->get($item->uniqueid, collect([]));
                return [
                    'kind' => 'call',
                    'uniqueid' => $item->uniqueid,
                    'title' => 'Outgoing Call',
                    'created_at' => $item->date,
                    'created_date' => $item->date->format('F jS, Y'),
                    'created_time' => $item->date->format('h:i A'),
                    'created_by' => $item->agentInfo ? $item->agentInfo->full_name : 'System Agent',
                    'icon' => 'icon-phone-out',
                    'bg-color' => 'bg-blue-200',
                    'icon-color' => 'text-blue-600 dark:text-blue-400',
                    'last-reaction' => $item->customer_reaction,
                    'last-comment' => $item->comment,
                    'ticket_count' => $linkedTickets->count(),
                    'latest_ticket_id' => $linkedTickets->first()?->id,
                    'latest_ticket_component' => ($linkedTickets->first()?->ticket_category_id == 3) ? 'orders.show' : 'tickets.show',
                    'latest_ticket_status' => $linkedTickets->first()?->status?->title,
                    'latest_ticket_status_id' => $linkedTickets->first()?->ticket_status_id,
                    'linked_tickets' => $linkedTickets->map(function ($ticket) {
                        [$bgColor, $textColor] = $this->ticketStatusClasses($ticket->ticket_status_id);

                        return [
                            'id' => $ticket->id,
                            'title' => $ticket->ticket_title,
                            'status' => $ticket->status?->title,
                            'status_id' => $ticket->ticket_status_id,
                            'status_bg' => $bgColor,
                            'status_text' => $textColor,
                            'created_at' => $ticket->created_at?->format('Y-m-d H:i:s'),
                        ];
                    })->values()->all(),
                ];
            }));
        }


        if (in_array(3, $this->fliterStatus)) {
            $this->callLogsArchived = QueueCountReport::with('agentInfo')->where(function ($query) use ($lead) {
                $query->orWhere('ani', $lead->contact_number)
                    ->orWhere('dnis', $lead->contact_number);
            })->where('status', 2)->orderBy('date')->get();

            $this->outCallLogsArchived = CallCountReport::with('agentInfo')->where(function ($query) use ($lead) {
                $query->orWhere('dnis', $lead->contact_number);
            })->where('direction', 'out')->orderBy('date')->get();


            // Map call logs
            $timelineLogs = $timelineLogs->merge($this->callLogsArchived->map(function ($item) {
                return [
                    'title' => 'Incoming Call',
                    'created_at' => $item->date,
                    'created_date' => $item->date->format('F jS, Y'),
                    'created_time' => $item->date->format('h:i A'),
                    'created_by' => $item->agentInfo ? $item->agentInfo->full_name : 'System Agent',
                    'icon' => 'icon-phone',
                    'bg-color' => 'bg-blue-200',
                    'icon-color' => 'text-blue-600 dark:text-blue-400',
                    'last-reaction' => $item->customer_reaction,
                    'last-comment' => $item->comment,
                ];
            }));

            // Map out call logs
            $timelineLogs = $timelineLogs->merge($this->outCallLogsArchived->map(function ($item) {
                return [
                    'title' => 'Outgoing Call',
                    'created_at' => $item->date,
                    'created_date' => $item->date->format('F jS, Y'),
                    'created_time' => $item->date->format('h:i A'),
                    'created_by' => $item->agentInfo ? $item->agentInfo->full_name : 'System Agent',
                    'icon' => 'icon-phone-out',
                    'bg-color' => 'bg-blue-200',
                    'icon-color' => 'text-blue-600 dark:text-blue-400',
                    'last-reaction' => $item->customer_reaction,
                    'last-comment' => $item->comment,
                ];
            }));
        }


        // Map lead tickets (with null check)
        if (in_array(1, $this->fliterStatus) && $dailyTickets) {
            $timelineLogs = $timelineLogs->merge($dailyTickets->map(function ($item) {
                return [
                    'kind' => 'ticket',
                    'id' => $item->id,
                    'component' => $item->ticket_category_id == 3 ? 'orders.show' : 'tickets.show',
                    'action' => $item->ticket_category_id == 3 ? 'openTicket' : 'openTicket',
                    'title' => $item->ticket_category_id == 3 ? 'Order' : 'Ticket',
                    'outlet' => $item->outlet ? $item->outlet->title  : false,
                    'category' => $item->category->title,
                    'status' => $item->status->title,
                    'order_total' =>  $item->ticket_category_id == 3 ? $item->order_total  : 0,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'created_date' => $item->created_at->format('F jS, Y'),
                    'created_time' => $item->created_at->format('h:i A'),
                    'created_by' => 'System Agent',
                    'icon' => $item->ticket_category_id == 3 ? 'icon-order' : 'icon-ticket',
                    'bg-color' =>  $item->ticket_category_id == 3 ? 'bg-green-200' : 'bg-red-200',
                    'icon-color' =>  $item->ticket_category_id == 3 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
                    'last-reaction' => $item->customer_reaction,
                    'last-comment' => $item->comment,
                ];
            }));
        }

        // Map the comments log for the follow-up records currently loaded.
        if (in_array(4, $this->fliterStatus)) {
            $this->commentLogs = FeedContactValid::with('updater')
                ->whereIn('id', $this->feedContactIds)
                ->get();

            $commentEntries = collect();

            foreach ($this->commentLogs as $contact) {
                foreach ($this->parseCommentEntries($contact) as $entry) {
                    $commentEntries->push([
                        'kind' => 'comment',
                        'feed_contact_id' => $contact->id,
                        'title' => 'Comment',
                        'created_at' => $entry['created_at'],
                        'created_date' => $entry['created_at']->format('F jS, Y'),
                        'created_time' => $entry['created_at']->format('h:i A'),
                        'created_by' => $entry['agent_name'],
                        'icon' => 'icon-comment',
                        'bg-color' => 'bg-purple-200',
                        'icon-color' => 'text-purple-600 dark:text-purple-400',
                        'last-reaction' => null,
                        'last-comment' => $entry['comment'],
                    ]);
                }
            }

            $timelineLogs = $timelineLogs->merge($commentEntries);
        }

        // Sort the merged collection by created_at. When the comments log is
        // selected all comments must be visible, so skip the default truncation.
        $timelineLogs = $timelineLogs->sortByDesc('created_at');

        if (!in_array(4, $this->fliterStatus)) {
            $timelineLogs = $timelineLogs->take(10);
        }

        $this->timelineLogs = $timelineLogs->values();
        $this->isLoad = true;
    }

    /**
     * Split a comments string into individual log entries.
     *
     * New entries are stored as "[YYYY-MM-DD hh:mm A | Agent <id> - <name>] text".
     * Anything without that prefix (older comments) is treated as a single
     * legacy entry using the record's last update details.
     *
     * @return array<int, array{created_at: Carbon, agent_name: string, comment: string}>
     */
    protected function parseCommentEntries(FeedContactValid $contact): array
    {
        $raw = trim((string) $contact->comments);

        if ($raw === '') {
            return [];
        }

        $fallbackAt = $contact->attempted_at ?: $contact->updated_at ?: $contact->created_at ?: now();
        $fallbackAgent = $contact->updater ? $contact->updater->name : 'System Agent';

        if (!$fallbackAt instanceof Carbon) {
            try {
                $fallbackAt = Carbon::parse($fallbackAt);
            } catch (\Throwable $e) {
                $fallbackAt = now();
            }
        }

        $entries = [];
        $current = null;

        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{1,2}:\d{2} [AP]M) \| (.*?)\]\s?(.*)$/', $line, $matches)) {
                if ($current !== null) {
                    $entries[] = $current;
                }

                try {
                    $createdAt = Carbon::parse($matches[1]);
                } catch (\Throwable $e) {
                    $createdAt = $fallbackAt;
                }

                $current = [
                    'created_at' => $createdAt,
                    'agent_name' => trim($matches[2]) !== '' ? trim($matches[2]) : $fallbackAgent,
                    'comment' => $matches[3],
                ];
            } elseif ($current !== null) {
                $current['comment'] .= ($current['comment'] === '' ? '' : PHP_EOL) . $line;
            } else {
                $current = [
                    'created_at' => $fallbackAt,
                    'agent_name' => $fallbackAgent,
                    'comment' => $line,
                ];
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        return array_values(array_filter(
            $entries,
            fn ($entry) => trim((string) $entry['comment']) !== ''
        ));
    }

    public function loadMore()
    {
        $this->loadMoreStatus = true;
        $this->refreshTimeline();
    }
}
