<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserActivityController;
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
// use App\Http\Livewire\Reports\CallDetail;
use App\Http\Livewire\Reports\CdrDetail;
use App\Http\Livewire\Reports\CdrListen;
use App\Http\Livewire\Reports\IvrDetail;
use App\Http\Livewire\Reports\AgentMissedCallSummary;
use App\Http\Livewire\Reports\AsteriskEvents;
use App\Http\Livewire\Reports\DailyCallSummary as ReportsDailyCallSummary;
use App\Http\Livewire\Reports\DailyQueueSummary;
// use App\Http\Livewire\Reports\CallQueue;
use App\Http\Livewire\Reports\Index as ReportsIndex;
use App\Http\Livewire\Settings\Extensions\Index as ExtensionsIndex;
use App\Http\Livewire\Settings\Index as SettingsIndex;
use App\Http\Livewire\Settings\Moh\Index as MohIndex;
use App\Http\Livewire\Settings\Queues\Index as QueuesIndex;
use App\Http\Livewire\Settings\Skills\Index as SkillsIndex;
use App\Http\Livewire\Settings\Users\Index as UsersIndex;
use App\Http\Livewire\Tickets\Index as TicketsIndex;
use App\Http\Livewire\Tickets\IndexNew;
use App\Models\CallCenter\AbandonedCall as CallCenterAbandonedCall;
use App\Models\DailyCallSummary;
use App\Models\QueueCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

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
        Route::get('/cdr-detail-report', CdrDetail::class)->name('reports.cdr-detail')->can('can-view-reports');
        Route::get('/cdr-listen-calls-report', CdrListen::class)->name('reports.cdr-listen-calls')->can('is-admin');
        Route::get('/ivr-detail-report', IvrDetail::class)->name('reports.ivr-detail')->can('is-admin');
        Route::get('/agent-missed-call-summary', AgentMissedCallSummary::class)->name('reports.agent-missed-call-summary')->can('is-admin');
        Route::get('/live-caller-dashboard', AsteriskEvents::class)->name('reports.asterisk-event')->can('is-admin');
        Route::get('/abandoned-call-report', AbandonedCall::class)->name('reports.abandoned-call')->can('is-admin');
        Route::get('/agent-break-summary-report', BreakSummary::class)->name('reports.agent-break-summary-report')->can('is-admin');
        Route::get('/agent-call-summary-report', AgentCallSummary::class)->name('reports.agent-call-summary-report')->can('is-admin');

        Route::get('/daily-queue-summary-report', DailyQueueSummary::class)->name('reports.daily-queue-summary-report')->can('is-admin');
        Route::get('/daily-call-summary-report', ReportsDailyCallSummary::class)->name('reports.daily-calls-summary-report')->can('is-admin');
        Route::get('/agent-login-logout-report', AgentLoginLogoutReport::class)->name('reports.agent-login-logout-report')->can('is-admin');
        // Route::get('/call-queue-report', CallQueue::class)->name('reports.call-queue-report')->can('is-admin');
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', SettingsIndex::class)->name('settings.index')->can('is-admin');
        Route::get('/users/', UsersIndex::class)->name('settings.users.index')->can('is-admin');
        Route::get('/extensions/', ExtensionsIndex::class)->name('settings.extensions.index')->can('is-admin');
        Route::get('/moh/', MohIndex::class)->name('settings.moh.index')->can('is-admin');
        Route::get('/skills/', SkillsIndex::class)->name('settings.skills.index')->can('is-admin');
    });


    Route::get('/test', function (Request $request) {
        return session()->getId();
    });
    
});
