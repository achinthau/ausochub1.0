<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserActivityController;
use App\Http\Livewire\Chat\Index as ChatIndex;
use App\Http\Livewire\ContactFeeds\Index as ContactFeedsIndex;
use App\Http\Livewire\Dashboard\Index;
use App\Http\Livewire\Leads\Create;
use App\Http\Livewire\Leads\Index as LeadsIndex;
use App\Http\Livewire\Leads\Show;
use App\Http\Livewire\Orders\Index as OrdersIndex;
use App\Http\Livewire\Reports\AbandonedCall;
use App\Http\Livewire\Reports\BreakSummary;
use App\Http\Livewire\Reports\AgentCallSummary;
use App\Http\Livewire\Reports\AgentLoginLogoutReport;
use App\Http\Livewire\Reports\DialerContactAttemptReport;
use App\Http\Livewire\Reports\NuisanceCustomers;
use App\Http\Livewire\Reports\UnsatisfiedCustomers;
// use App\Http\Livewire\Reports\CallDetail;
use App\Http\Livewire\Reports\CdrDetail;
use App\Http\Livewire\Reports\CdrListen;
use App\Http\Livewire\Reports\IvrDetail;
use App\Http\Livewire\Reports\AgentMissedCallSummary;
use App\Http\Livewire\Reports\AsteriskEvents;
use App\Http\Livewire\Reports\DailyCallSummary as ReportsDailyCallSummary;
use App\Http\Livewire\Reports\DailyCallSummaryHourly;
use App\Http\Livewire\Reports\DailyQueueSummary;
use App\Http\Livewire\Reports\AgentPerformanceReport;
use App\Http\Livewire\Reports\AgentPerformanceMetricsReport;
// use App\Http\Livewire\Reports\CallQueue;
use App\Http\Livewire\Reports\Index as ReportsIndex;
use App\Http\Livewire\Settings\Extensions\Index as ExtensionsIndex;
use App\Http\Livewire\Settings\Index as SettingsIndex;
use App\Http\Livewire\Settings\Moh\Index as MohIndex;
use App\Http\Livewire\Settings\Queues\Index as QueuesIndex;
use App\Http\Livewire\Settings\Skills\Index as SkillsIndex;
use App\Http\Livewire\Settings\Tickets\Departments\Index as TicketDepartmentsIndex;
use App\Http\Livewire\Settings\Tickets\ServiceCenter\Index as ServCenterIndex;
use App\Http\Livewire\Settings\Users\Index as UsersIndex;
use App\Http\Livewire\Tickets\Index as TicketsIndex;
use App\Http\Livewire\Tickets\IndexNew;
use App\Http\Livewire\CxTickets\Index as CxTicketsIndex;
use App\Http\Livewire\Reminders\Index as ReminderIndex;
use App\Http\Livewire\CxTickets\Survey\Index as SurveyIndex;
use App\Http\Livewire\Dialer\Index as DialerIndex;
use App\Http\Livewire\Dialer\Dashboard\Admin\Index as DialerAdminIndex;
use App\Http\Livewire\Dialer\Dashboard\Index as DialerAgentIndex;
use App\Http\Livewire\Dialer\Settings\Index as DialerSettingsIndex;
use App\Http\Livewire\Dialer\Dashboard\Dashboard as DialerDashboardIndex;
use App\Http\Livewire\Dialer\Settings\Campaign\Index as DialerCampSettingsIndex;
use App\Http\Livewire\Dialer\Settings\Feed\Index as DialerFeedSettingsIndex;
use App\Models\CallCenter\AbandonedCall as CallCenterAbandonedCall;
use App\Models\DailyCallSummary;
use App\Models\QueueCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use App\Http\Livewire\Whatsapp\Chat as WhatsappChat;
use App\Http\Livewire\Messenger\Chat as MessengerChat;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard.index');
    Route::get('/live-dashboard', function () {
        if (Gate::allows('live-dashboard-user')) {
            return view('livewire.dashboard.admin.live.uaindex');
        } elseif (Gate::allows('is-admin')) {
            return view('livewire.dashboard.admin.live.index');
        } else {
            return abort(403, 'Unauthorized action.');
        }
    })->name('live-dashboard.index');
    Route::prefix('leads')->group(function () {
        Route::get('/', LeadsIndex::class)->name('leads.index')->can('can-view-leads');
        // Route::get('/{lead}', Create::class)->name('leads.create')->can('can-view-leads');
        Route::get('/{lead}', Show::class)->name('leads.create')->can('can-view-leads');
        Route::get('/{lead}', Show::class)->name('leads.show')->can('can-view-leads');
    });

    // Route::get('/tickets', TicketsIndex::class)->name('tickets.index');
    Route::get('/tickets-v1', TicketsIndex::class)->name('tickets.index');
    Route::get('/tickets', IndexNew::class)->name('tickets.index');
    // Route::get('/tickets-v2', IndexNew::class)->name('tickets.index-2');
    Route::get('/orders', OrdersIndex::class)->name('orders.index');
    Route::get('/contact-feeds', ContactFeedsIndex::class)->name('contact-feeds.index')->can('is-admin');

    Route::prefix('reports')->group(function () {
        Route::get('/', ReportsIndex::class)->name('reports.index');
        // Route::get('/call-detail-report', CallDetail::class)->name('reports.call-detail')->can('is-admin');
        Route::get('/cdr-detail-report', CdrDetail::class)->name('reports.cdr-detail')->middleware('can:can-view-cdr-reports');
        Route::get('/cdr-listen-calls-report', CdrListen::class)->name('reports.cdr-listen-calls')->can('is-admin');
        Route::get('/ivr-detail-report', IvrDetail::class)->name('reports.ivr-detail')->can('is-admin');
        Route::get('/agent-missed-call-summary', AgentMissedCallSummary::class)->name('reports.agent-missed-call-summary')->can('is-admin');
        Route::get('/live-caller-dashboard', AsteriskEvents::class)->name('reports.asterisk-event')->can('is-admin');
        Route::get('/abandoned-call-report', AbandonedCall::class)->name('reports.abandoned-call')->can('is-admin');
        Route::get('/agent-break-summary-report', BreakSummary::class)->name('reports.agent-break-summary-report')->can('is-admin');
        Route::get('/agent-call-summary-report', AgentCallSummary::class)->name('reports.agent-call-summary-report')->can('is-admin');

        Route::get('/daily-queue-summary-report', DailyQueueSummary::class)->name('reports.daily-queue-summary-report')->can('is-admin');
        Route::get('/daily-queue-summary-hourly/{date}/{queue}', \App\Http\Livewire\Reports\DailyQueueSummaryHourly::class)->name('reports.daily-queue-summary-hourly')->can('is-admin');
        Route::get('/daily-queue-summary-hourly-analytics/{date}/{queue}', \App\Http\Livewire\Reports\DailyQueueSummaryHourlyAnalytics::class)->name('reports.daily-queue-summary-hourly-analytics')->can('is-admin');
        Route::get('/daily-call-summary-report', ReportsDailyCallSummary::class)->name('reports.daily-calls-summary-report')->can('is-admin');
        Route::get('/daily-call-summary-hourly/{date}', DailyCallSummaryHourly::class)->name('reports.daily-calls-summary-hourly')->can('is-admin');
        Route::get('/daily-call-summary-hourly-analytics/{date}', \App\Http\Livewire\Reports\DailyCallSummaryHourlyAnalytics::class)->name('reports.daily-calls-summary-hourly-analytics')->can('is-admin');
        Route::get('/agent-login-logout-report', AgentLoginLogoutReport::class)->name('reports.agent-login-logout-report')->can('is-admin');
        Route::get('/nuisance-customers-report', NuisanceCustomers::class)->name('reports.nuisance-customers-report')->can('is-admin');
        Route::get('/unsatisfied-customers-report', UnsatisfiedCustomers::class)->name('reports.unsatisfied-customers-report')->can('is-admin');
        Route::get('/dialer-contact-attempt-report', DialerContactAttemptReport::class)->name('reports.dialer-contact-attempt-report')->middleware('can:can-view-cdr-reports');
        Route::get('/agent-performance-report', AgentPerformanceReport::class)->name('reports.agent-performance-report')->middleware('can:can-view-cdr-reports');
        Route::get('/agent-performance-report-analytics', \App\Http\Livewire\Reports\AgentPerformanceAnalytics::class)->name('reports.agent-performance-report-analytics')->middleware('can:can-view-cdr-reports');
        Route::get('/agent-performance-metrics-report', AgentPerformanceMetricsReport::class)->name('reports.agent-performance-metrics-report')->middleware('can:can-view-cdr-reports');
        // Route::get('/call-queue-report', CallQueue::class)->name('reports.call-queue-report')->can('is-admin');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', SettingsIndex::class)->name('settings.index')->middleware('can:can-view-cdr-reports');
        Route::get('/users/', UsersIndex::class)->name('settings.users.index')->middleware('can:can-view-cdr-reports');
        Route::get('/extensions/', ExtensionsIndex::class)->name('settings.extensions.index')->middleware('can:can-view-cdr-reports');
        Route::get('/moh/', MohIndex::class)->name('settings.moh.index')->middleware('can:can-view-cdr-reports');
        Route::get('/skills/', SkillsIndex::class)->name('settings.skills.index')->middleware('can:can-view-cdr-reports');
        Route::get('/ticketdep/', TicketDepartmentsIndex::class)->name('settings.tickets.departments.index')->middleware('can:can-view-cdr-reports');
        Route::get('/service-center/', ServCenterIndex::class)->name('settings.tickets.serv-center.index')->middleware('can:can-view-cdr-reports');
    });


    Route::get('/test', function (Request $request) {
        return session()->getId();
    });

    Route::get('/chat', ChatIndex::class)->name('chat.index');
    Route::get('/whatsapp/chat', WhatsappChat::class)->name('whatsapp.chat');
    Route::get('/messenger/chat', MessengerChat::class)->name('messenger.chat');

    Route::get('/cx-tickets', CxTicketsIndex::class)->name('cx-tickets.index');
    Route::get('/cx-tickets/survey', SurveyIndex::class)->name('cx-tickets-survey.index');
    
    Route::get('/reminders', ReminderIndex::class)->name('reminder.index');

Route::get('/reminders/clear-and-show', function () {
    Redis::connection()->select(6);
    Redis::del('isReminder:' . auth()->id());
    return redirect()->route('reminder.index');
})->name('reminder.clear');


Route::prefix('dialer')->group(function () {
        Route::get('/', DialerIndex::class)->name('dialer.index');
        Route::get('/settings', DialerSettingsIndex::class)->name('dialer.settings.index');
        Route::get('/settings/camp', DialerCampSettingsIndex::class)->name('dialer.settings.camp.index');
        Route::get('/settings/feed', DialerFeedSettingsIndex::class)->name('dialer.settings.feed.index');
        Route::get('/dashboard', DialerDashboardIndex::class)->name('dialer.dashboard.index');
        Route::get('/admin', DialerAdminIndex::class)->name('dialer.admin.dashboard');
        Route::get('/agent', DialerAgentIndex::class)->name('dialer.agent.dashboard');
    });


Route::get('/redis/check', function () {
    $userId = Auth::id();
    return response()->json([
        'registered' => Redis::get("softphone_registered:$userId") ? true : false
    ]);
});

Route::post('/redis/set', function () {
    $userId = Auth::id();
    Redis::set("softphone_registered:$userId", 1);
    return response()->json(['status' => 'ok']);
});

//for disabled filament login
Route::redirect('/admin/login', '/login')
    ->name('filament.auth.login');


});

Route::get('/test-network', function (Request $request) {
    $out = [];
    $mtu = $request->query('mtu');
    if ($mtu && is_numeric($mtu)) {
        $out[] = "=== trying to change MTU to $mtu ===";
        $out[] = shell_exec("sudo ip link set dev eth0 mtu $mtu 2>&1");
    }

    $out[] = "=== testing google.com curl (baseline check) ===";
    $ch = curl_init("https://www.google.com/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    if ($res === false) {
        $out[] = "google.com curl error: " . curl_error($ch) . " (code: " . curl_errno($ch) . ")";
    } else {
        $out[] = "google.com curl success: status " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . ", " . strlen($res) . " bytes";
    }
    curl_close($ch);

    $out[] = "\n=== testing graph.facebook.com curl ===";
    $ch = curl_init("https://graph.facebook.com/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    if ($res === false) {
        $out[] = "curl error: " . curl_error($ch) . " (code: " . curl_errno($ch) . ")";
    } else {
        $out[] = "curl success: " . substr($res, 0, 100);
    }
    curl_close($ch);

    $out[] = "\n=== testing graph.facebook.com curl with SSL bypass ===";
    $ch = curl_init("https://graph.facebook.com/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $chOptions = [
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ];
    curl_setopt_array($ch, $chOptions);
    $res = curl_exec($ch);
    if ($res === false) {
        $out[] = "curl SSL bypass error: " . curl_error($ch) . " (code: " . curl_errno($ch) . ")";
    } else {
        $out[] = "curl SSL bypass success: " . substr($res, 0, 100);
    }
    curl_close($ch);

    $out[] = "\n=== testing TCP socket connection to graph.facebook.com:443 ===";
    $start = microtime(true);
    $fp = @fsockopen("graph.facebook.com", 443, $errno, $errstr, 5);
    if (!$fp) {
        $out[] = "TCP connection failed: $errstr ($errno) in " . round((microtime(true) - $start) * 1000) . " ms";
    } else {
        $out[] = "TCP connection succeeded in " . round((microtime(true) - $start) * 1000) . " ms";
        fclose($fp);
    }

    $out[] = "\n=== network interfaces ===";
    $out[] = shell_exec('ip addr show 2>&1');

    return response(implode("\n", $out), 200, ['Content-Type' => 'text/plain']);
});

Route::get('/db-check', function () {
    try {
        $messages = \App\Models\MessengerMessage::query()
            ->select('sender_id', 'sender_name', \Illuminate\Support\Facades\DB::raw('count(*) as count'), \Illuminate\Support\Facades\DB::raw('max(sent_at) as last_sent'))
            ->groupBy('sender_id', 'sender_name')
            ->get();
        return response()->json($messages);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
