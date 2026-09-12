<script>
/**
 * CSRF interceptor for <auso-phone> fetch calls.
 * Must run before auso-phone.min.js loads. Reads the encrypted XSRF-TOKEN
 * cookie Laravel sets and attaches it as X-XSRF-TOKEN on every request.
 */
(function () {
    if (window.__ausoCsrfInit) return;
    window.__ausoCsrfInit = true;

    var _origFetch = window.fetch;
    window.fetch = function (input, init) {
        init = init || {};
        init.headers = init.headers || {};

        // Read XSRF-TOKEN cookie (set by Laravel's EncryptCookies).
        var match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/);
        if (match) {
            var token = decodeURIComponent(match[1]);
            if (typeof init.headers.set === 'function') {
                init.headers.set('X-XSRF-TOKEN', token);
            } else {
                init.headers['X-XSRF-TOKEN'] = token;
            }
        }

        return _origFetch.call(this, input, init);
    };
})();
</script>

<style>
    #phone-widget { display: none; }
    #phone-widget.phone-open { display: flex; }
</style>

<div id="phone-widget"
    class="fixed z-[60] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
    style="left: 16px; top: 120px; width: 360px; height: 600px; max-height: calc(100vh - 100px);">

    {{-- Title bar (drag handle) --}}
    <div class="flex h-11 shrink-0 cursor-move select-none items-center justify-between border-b border-slate-200 bg-slate-50 px-4"
        id="phone-widget-title">
        <div class="flex items-center gap-2">
            <span class="flex h-2 w-2 rounded-full bg-teal-500"></span>
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-600">Phone</span>
        </div>
        <div class="flex items-center gap-1">
            <button type="button" class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700" id="phone-widget-minimize" title="Minimize">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                </svg>
            </button>
            <button type="button" class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700" id="phone-widget-close" title="Close">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div class="h-full min-h-0 overflow-auto bg-slate-50">
        <auso-phone
            id="auso-phone"
            credentials-url="{{ route('api.phone.credentials') }}"
            sip-credentials-url="{{ route('api.phone.credentials.manual') }}"
            lookup-url="{{ route('api.customers.lookup') }}"
            call-record-url="{{ route('api.phone.call-records') }}"
            company-name="{{ config('ausophone.branding.company_name') }}"
            logo="{{ config('ausophone.branding.logo') }}"
            primary-color="{{ config('ausophone.branding.primary_color') }}"
            auto-login
        ></auso-phone>
    </div>
</div>

<script>
(function () {
    if (window.__ausoPhoneWidgetInit) return;
    window.__ausoPhoneWidgetInit = true;

    var widget = document.getElementById('phone-widget');

    function show() {
        widget.classList.add('phone-open');
    }

    function hide() {
        widget.classList.remove('phone-open');
    }

    // Shared global toggle so the nav button can open/close this.
    window.toggleAusophone = function () {
        if (!widget) return;
        if (widget.classList.contains('phone-open')) { hide(); } else { show(); }
    };
    window.openAusophone = show;

    // Listeners for external triggers
    window.addEventListener('toggle-phone-widget', window.toggleAusophone);
    window.addEventListener('ausophone:incoming', show);

    // Close buttons
    var minimize = document.getElementById('phone-widget-minimize');
    var close = document.getElementById('phone-widget-close');
    if (minimize) minimize.addEventListener('click', hide);
    if (close) close.addEventListener('click', hide);

    // ---- Dragging via title bar (vanilla) ----
    var title = document.getElementById('phone-widget-title');
    var dragging = false, startX = 0, startY = 0, origX = 0, origY = 0;

    function onDown(e) {
        if (e.target.closest('button')) return;
        dragging = true;
        var cx = e.clientX != null ? e.clientX : (e.touches ? e.touches[0].clientX : 0);
        var cy = e.clientY != null ? e.clientY : (e.touches ? e.touches[0].clientY : 0);
        startX = cx; startY = cy;
        origX = widget.offsetLeft; origY = widget.offsetTop;
        document.addEventListener('mousemove', onMove);
        document.addEventListener('touchmove', onMove);
        document.addEventListener('mouseup', onUp);
        document.addEventListener('touchend', onUp);
        e.preventDefault();
    }
    function onMove(e) {
        if (!dragging) return;
        var cx = e.clientX != null ? e.clientX : (e.touches ? e.touches[0].clientX : 0);
        var cy = e.clientY != null ? e.clientY : (e.touches ? e.touches[0].clientY : 0);
        var dx = cx - startX;
        var dy = cy - startY;
        var left = Math.max(0, Math.min(window.innerWidth - 380, origX + dx));
        var top = Math.max(0, Math.min(window.innerHeight - 80, origY + dy));
        widget.style.left = left + 'px';
        widget.style.top = top + 'px';
    }
    function onUp() {
        dragging = false;
        document.removeEventListener('mousemove', onMove);
        document.removeEventListener('touchmove', onMove);
        document.removeEventListener('mouseup', onUp);
        document.removeEventListener('touchend', onUp);
    }
    if (title) {
        title.addEventListener('mousedown', onDown);
        title.addEventListener('touchstart', onDown);
    }
    widget.style.left = Math.max(16, window.innerWidth - 400) + 'px';
    widget.style.top = '120px';
})();
</script>

<script>
/**
 * WebRTC phone → CRM event bridge (runs only when PHONE=webrtc).
 *
 * Every event the browser softphone fires is reported to the CRM's
 * /api/phone/events/* receiver. The receiver forwards the call-affecting ones
 * onto the SAME call-server endpoints the desk softphones trigger
 * (/api/call-dialed, /api/call-answered, /api/call-disconnected, ...), so the
 * CRM behaves identically no matter which phone type is in use.
 */
(function () {
    if (window.__ausoPhoneEventBridge) return;
    window.__ausoPhoneEventBridge = true;

    // Mirrors PhoneEventController::EVENTS.
    var EVENT_NAMES = [
        'incoming', 'dialing', 'ringing', 'answered', 'hold', 'unhold',
        'mute', 'unmute', 'transfer_started', 'transfer_completed',
        'transfer_failed', 'hangup', 'registered', 'unregistered',
        'registration_failed', 'connecting', 'connected', 'disconnected'
    ];

    function extension() {
        try {
            return (window.AusoPhone && window.AusoPhone.status().extension) || null;
        } catch (e) {
            return null;
        }
    }

    EVENT_NAMES.forEach(function (name) {
        window.addEventListener('ausophone:' + name, function (ev) {
            var payload = Object.assign({}, ev.detail || {});
            payload.extension = payload.extension || extension();
            try {
                fetch('/api/phone/events/' + name, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    keepalive: true,
                    body: JSON.stringify(payload)
                }).catch(function () {});
            } catch (e) {}
        });
    });
})();
</script>
