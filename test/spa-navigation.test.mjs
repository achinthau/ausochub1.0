/**
 * Tests for public/js/spa-navigation.js.
 *
 * Run with: node --test test/spa-navigation.test.mjs
 */
import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { JSDOM, VirtualConsole } from 'jsdom';

const source = readFileSync('public/js/spa-navigation.js', 'utf8');

const LAYOUT = `<!DOCTYPE html><html><head><title>Old</title></head><body>
<div x-data="{ sidebarOpen: true }">
  <nav data-spa-nav class="relative z-40">
    <a href="/">Dashboard</a>
    <a href="/leads" id="self">Customers</a>
    <a href="/tickets">Tickets</a>
    <a href="/settings">Settings</a>
    <a href="https://example.com/x" id="external">External</a>
    <a href="/downloads" download id="dl">Download</a>
    <a href="/tickets" target="_blank" id="blank">New tab</a>
    <a href="/tickets" id="wire" wire:click="something">Livewire link</a>
    <a href="#section" id="frag">Jump</a>
  </nav>
  <div class="pt-16">
    <header data-spa-region><h1>Old heading</h1></header>
    <main data-spa-region id="old-main"><p>old body</p><a href="/tickets" id="in-page">Ticket 1</a></main>
  </div>
</div>
<div id="phone-widget">phone</div>
</body></html>`;

const TICKETS = `<!DOCTYPE html><html><head><title>Tickets</title></head><body>
<div x-data="{ sidebarOpen: true }">
  <nav data-spa-nav><a href="/tickets" class="active">Tickets</a><a href="/settings">Settings</a></nav>
  <div class="pt-16">
    <header data-spa-region><h1>Tickets</h1></header>
    <main data-spa-region><section wire:id="abc" wire:initial-data='{"fingerprint":{"name":"tickets.index"},"serverMemo":{},"effects":{}}'>rows</section></main>
  </div>
</div>
</body></html>`;

// A page with no $header, to exercise region removal.
const NOHEADER = `<!DOCTYPE html><html><head><title>Leads</title></head><body>
<div x-data="{ sidebarOpen: true }">
  <nav data-spa-nav><a href="/leads">Leads</a></nav>
  <div class="pt-16"><main data-spa-region>leads body</main></div>
</div>
</body></html>`;

const LOGIN = `<!DOCTYPE html><html><head><title>Login</title></head><body><form method="post" action="/login"></form></body></html>`;

/**
 * Boot the shim in a JSDOM window, with a stubbed Livewire + fetch.
 *
 * `pretendToBeVisual` is required: the shim calls requestAnimationFrame for its
 * progress bar, and without it JSDOM throws, which aborts `navigate()` before it
 * ever fetches. `runScripts: 'dangerously'` is required so the re-created
 * <script> elements the shim swaps in actually execute.
 */
function boot({ page = LAYOUT, response = TICKETS, ok = true, fail = false, inCall = false } = {}) {
  // jsdom cannot navigate, so the shim's full-page-load fallback surfaces as a
  // "Not implemented: navigation" jsdomError on the virtual console. That is how
  // these tests observe the fallback: the question that matters is never the
  // destination (jsdom keeps the old URL either way) but whether the document
  // was torn down at all, because that is what drops a call.
  let hardNavs = 0;
  const vc = new VirtualConsole();
  vc.on('jsdomError', (err) => {
    if (/navigation/i.test(err.message)) hardNavs += 1;
  });

  const dom = new JSDOM(page, {
    url: 'https://crm.test/leads',
    runScripts: 'dangerously',
    pretendToBeVisual: true,
    virtualConsole: vc,
  });
  const { window } = dom;

  const rescanned = [];
  const removed = [];
  window.Livewire = {
    rescan(node) {
      rescanned.push(node);
    },
    components: {
      hasComponent: (id) => id === 'gone',
      removeComponent(c) { removed.push(c.id); },
    },
  };

  // The shim asks this first, and falls back to probing AusoPhone. Only the
  // former is set here so the test states which layer is under test.
  if (inCall) window.__ausoCallInProgress = () => true;

  window.fetch = () => {
    if (fail) return Promise.reject(new Error('offline'));
    return Promise.resolve({
      ok,
      status: ok ? 200 : 500,
      text: () => Promise.resolve(response),
    });
  };

  window.console.warn = () => {};
  window.scrollTo = () => {};

  window.eval(source);
  return { dom, window, rescanned, removed, hardNavs: () => hardNavs };
}

const settle = () => new Promise((r) => setTimeout(r, 30));

test('ignores clicks outside the sidebar nav', async () => {
  const { window } = boot();
  let defaultPrevented = false;
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  window.document.querySelector('#phone-widget').addEventListener('click', (e) => {
    defaultPrevented = e.defaultPrevented;
  });
  window.document.querySelector('#phone-widget').dispatchEvent(ev);
  assert.equal(defaultPrevented, false);
});

test('intercepts a same-origin sidebar link and swaps main', async () => {
  const { window, rescanned } = boot();
  const link = window.document.querySelector('nav[data-spa-nav] a[href="/tickets"]');
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  link.dispatchEvent(ev);
  assert.equal(ev.defaultPrevented, true);
  await settle();

  const main = window.document.querySelector('main[data-spa-region]');
  assert.match(main.innerHTML, /wire:id="abc"/);
  assert.equal(window.document.title, 'Tickets');
  assert.match(window.document.querySelector('nav[data-spa-nav]').innerHTML, /class="active"/);
  assert.ok(rescanned.length > 0, 'Livewire.rescan was called');
});

test('leaves the phone widget untouched', async () => {
  const { window } = boot();
  const phone = window.document.querySelector('#phone-widget');
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(window.document.querySelector('#phone-widget'), phone, 'same node instance');
  assert.equal(phone.textContent, 'phone');
});

test('tears down old components including the region root itself', async () => {
  const page = LAYOUT.replace('<nav data-spa-nav class="relative z-40">',
    '<nav data-spa-nav class="relative z-40" wire:id="gone" wire:initial-data="{}">');
  const { window, removed } = boot({ page });
  // `remove()` reads the component off the element, which a real Livewire run
  // has already put there; the stub registry only stands in for the store.
  window.document
    .querySelector('nav[data-spa-nav]')
    .__livewire = { id: 'gone' };
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.deepEqual(removed, ['gone'], 'nav root component was torn down');
});

test('removes the header region when the new page has none', async () => {
  const { window } = boot({ response: NOHEADER });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(window.document.querySelector('header[data-spa-region]'), null);
  assert.match(window.document.querySelector('main[data-spa-region]').innerHTML, /leads body/);
});

test('inserts the header when the old page had none', async () => {
  const page = LAYOUT.replace(/<header data-spa-region>.*?<\/header>/s, '');
  const { window } = boot({ page });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  const header = window.document.querySelector('header[data-spa-region]');
  const main = window.document.querySelector('main[data-spa-region]');
  assert.ok(header, 'header inserted');
  assert.ok(
    header.compareDocumentPosition(main) & window.Node.DOCUMENT_POSITION_FOLLOWING,
    'header sits above main'
  );
});

test('leaves external, download, and target=_blank links alone', async () => {
  const { window } = boot();
  for (const id of ['external', 'dl', 'blank']) {
    const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
    window.document.getElementById(id).dispatchEvent(ev);
    assert.equal(ev.defaultPrevented, false, id + ' was not intercepted');
  }
});

test('leaves modifier-key clicks alone', async () => {
  const { window } = boot();
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true, ctrlKey: true });
  window.document.querySelector('nav[data-spa-nav] a[href="/tickets"]').dispatchEvent(ev);
  assert.equal(ev.defaultPrevented, false);
});

test('falls back to a full page load on a non-page response', async () => {
  const { window, hardNavs } = boot({ response: LOGIN });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 1, 'a full page load was taken');
  assert.match(window.document.querySelector('main[data-spa-region]').innerHTML, /old body/);
});

test('falls back to a full page load on a server error', async () => {
  const { window, hardNavs } = boot({ ok: false });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 1, 'a full page load was taken');
});

test('falls back to a full page load when the network fails', async () => {
  const { window, hardNavs } = boot({ fail: true });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 1, 'a full page load was taken');
});

test('does not call Alpine.initTree, which would double-initialize', async () => {
  const { window } = boot();
  let initTreeCalls = 0;
  window.Alpine = { initTree: () => { initTreeCalls += 1; } };
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(initTreeCalls, 0);
});

test('pushes history so back and forward stay in sync', async () => {
  const { window } = boot();
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(window.location.pathname, '/tickets');
  assert.equal(window.history.state.spa, true);
});

test('re-executes inline scripts in the swapped content', async () => {
  const response = TICKETS.replace('<main data-spa-region>',
    '<main data-spa-region><script>window.__spaRan = (window.__spaRan || 0) + 1;<\/script>');
  const { window } = boot({ response });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(window.__spaRan, 1);
});

test('ignores popstate without the spa marker', () => {
  const { window } = boot();
  let called = 0;
  window.fetch = () => { called += 1; return Promise.resolve({ ok: true, text: () => Promise.resolve(TICKETS) }); };
  window.dispatchEvent(new window.PopStateEvent('popstate', { state: null }));
  assert.equal(called, 0);
});

// ---- Keeping the call alive ---------------------------------------------

test('clicking the page you are already on does not reload it', async () => {
  const { window, hardNavs } = boot();
  // The shim is on /leads, and the sidebar's "Customers" link points at /leads.
  // This used to fall through to the browser, which reloaded the document and
  // took the call with it.
  let fetched = 0;
  window.fetch = () => { fetched += 1; return Promise.resolve({ ok: true, text: () => Promise.resolve(TICKETS) }); };

  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  window.document.getElementById('self').dispatchEvent(ev);
  await settle();

  assert.equal(ev.defaultPrevented, true, 'the click was consumed');
  assert.equal(fetched, 0, 'nothing was fetched');
  assert.equal(hardNavs(), 0, 'no full page load');
  assert.match(window.document.querySelector('main[data-spa-region]').innerHTML, /old body/);
});

test('intercepts an in-page link outside the sidebar', async () => {
  const { window, hardNavs } = boot();
  // The page body is full of links — a lead, a ticket, a report row. None of
  // them are in the sidebar, and every one of them used to be a full page load.
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  window.document.getElementById('in-page').dispatchEvent(ev);
  assert.equal(ev.defaultPrevented, true);
  await settle();

  assert.match(window.document.querySelector('main[data-spa-region]').innerHTML, /wire:id="abc"/);
  assert.equal(hardNavs(), 0, 'no full page load');
});

test('leaves fragment links to the browser', () => {
  const { window } = boot();
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  window.document.getElementById('frag').dispatchEvent(ev);
  assert.equal(ev.defaultPrevented, false);
});

test('leaves links carrying a Livewire action to the component', () => {
  const { window } = boot();
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  window.document.getElementById('wire').dispatchEvent(ev);
  assert.equal(ev.defaultPrevented, false);
});

test('holds the call instead of reloading when a page cannot be swapped', async () => {
  const { window, hardNavs } = boot({ response: LOGIN, inCall: true });
  const ev = new window.MouseEvent('click', { bubbles: true, cancelable: true });
  window.document.querySelector('nav[data-spa-nav] a[href="/tickets"]').dispatchEvent(ev);
  await settle();

  assert.equal(hardNavs(), 0, 'the call was not traded for a page load');
  assert.match(window.document.querySelector('main[data-spa-region]').innerHTML, /old body/);
  const err = window.document.querySelector('[data-spa-error]');
  assert.ok(err, 'the agent was told why');
  assert.match(err.textContent, /active call/);
});

test('holds the call on a server error too', async () => {
  const { window, hardNavs } = boot({ ok: false, inCall: true });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 0);
  assert.ok(window.document.querySelector('[data-spa-error]'));
});

test('holds the call when the network fails', async () => {
  const { window, hardNavs } = boot({ fail: true, inCall: true });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 0);
  assert.ok(window.document.querySelector('[data-spa-error]'));
});

test('falls back to a full page load when no call is up', async () => {
  // The guard must not change ordinary navigation: an expired session still has
  // to reach the login page, and that has no main region to swap in.
  const { window, hardNavs } = boot({ response: LOGIN });
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 1, 'a full page load was taken');
});

test('does not protect a companion tab, which does not own the call', async () => {
  // A companion mirrors another tab's call. Unloading it drops nothing, so
  // refusing to navigate would be a pointless dead end for the agent.
  const { window, hardNavs } = boot({ response: LOGIN, inCall: false });
  window.__ausoCallInProgress = () => false;
  window.AusoPhone = {
    status: () => ({ calls: [{ id: 'x' }], session: { role: 'companion' } }),
  };
  window.document
    .querySelector('nav[data-spa-nav] a[href="/tickets"]')
    .dispatchEvent(new window.MouseEvent('click', { bubbles: true, cancelable: true }));
  await settle();
  assert.equal(hardNavs(), 1, 'a full page load was taken');
});
