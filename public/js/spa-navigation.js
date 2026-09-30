/**
 * Single-page navigation.
 *
 * Every in-app link used to be a full page load, which tore down the whole JS
 * context — and with it the WebRTC call. An RTCPeerConnection belongs to the
 * browsing context that created it and cannot be reattached, so navigating away
 * mid-call killed the call. This keeps the document alive and swaps only the
 * regions that make up the page, leaving the layout, the Livewire chrome and the
 * softphone untouched.
 *
 * Scoped to same-origin GET navigation, which is everything an agent clicks to
 * move around the CRM: the sidebar, the top-bar buttons, and the links inside
 * the page itself. Anything that is not that — form posts, logout, downloads,
 * `target=_blank`, external origins, modifier clicks — still does a normal full
 * load, so a mistake here cannot affect the rest of the app.
 *
 * A call is the one thing here that cannot be recovered from, so the shim never
 * trades one for the other: a click that cannot be soft-navigated while a call
 * is live is reported instead of being allowed to become a full page load.
 * `window.__ausoCallInProgress()` is installed by the phone widget; when the
 * phone is not mounted this degrades to plain navigation.
 *
 * Not `wire:navigate` because that is a Livewire 3 feature and this app is on
 * Livewire 2. Livewire 2 does expose the two hooks this needs:
 * `Livewire.components.removeComponent()` to tear down the components being
 * replaced, and `Livewire.rescan()` to bootstrap the new ones.
 */
(function () {
  'use strict';

  var NAV = 'nav[data-spa-nav]';
  var MAIN = 'main[data-spa-region]';
  var HEADER = 'header[data-spa-region]';
  var WIRE_ID = '[wire\\:id]';

  var inFlight = null;
  var scrollPositions = {};

  // ---- Call protection --------------------------------------------------

  /**
   * Is a call up that this tab is responsible for?
   *
   * A companion tab only mirrors a call another tab owns, so navigating it drops
   * nothing. Only the tab holding the SIP socket needs protecting.
   */
  function callInProgress() {
    try {
      // The widget owns the authoritative answer; it knows about the web
      // component as well as the library.
      if (typeof window.__ausoCallInProgress === 'function') {
        return window.__ausoCallInProgress();
      }
      var phone = window.AusoPhone;
      if (!phone || typeof phone.status !== 'function') return false;
      var status = phone.status();
      if (!status) return false;
      if (status.session && status.session.role === 'companion') return false;
      if (Array.isArray(status.calls)) return status.calls.length > 0;
      return Boolean(status.active_call);
    } catch (err) {
      return false;
    }
  }

  // ---- Link filtering ---------------------------------------------------

  /**
   * The link this click should navigate to, or null to let the browser handle it
   * normally.
   */
  function targetFor(event) {
    if (event.defaultPrevented) return null;
    // Middle click, right click and "open in new tab" belong to the browser.
    if (event.button !== 0) return null;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return null;

    var link = event.target.closest && event.target.closest('a[href]');
    if (!link) return null;
    if (link.target && link.target !== '_self') return null;
    if (link.hasAttribute('download')) return null;
    if (link.hasAttribute('data-spa-ignore')) return null;
    // A link that also carries a Livewire action is the component's business:
    // intercepting it here would navigate out from under the action as well.
    if (link.hasAttribute('wire:click') || link.hasAttribute('wire:mouseenter')) return null;

    var url;
    try {
      url = new URL(link.href, window.location.href);
    } catch (err) {
      return null;
    }
    if (url.origin !== window.location.origin) return null;
    if (url.protocol !== 'http:' && url.protocol !== 'https:') return null;
    // A fragment on the current page is an in-page jump; the browser scrolls to
    // it faster and more correctly than a re-render would.
    if (url.hash && isSamePage(url)) return null;

    return url;
  }

  function isSamePage(url) {
    return url.pathname === window.location.pathname && url.search === window.location.search;
  }

  // ---- Navigation -------------------------------------------------------

  function navigate(url, push) {
    if (inFlight) return inFlight;

    // Clicking the page you are already on used to fall through to the browser,
    // which reloaded the document and dropped the call. There is nothing to
    // render, so this is a no-op rather than a navigation.
    if (isSamePage(url)) return Promise.resolve();

    scrollPositions[window.location.href] = window.scrollY;
    startProgress();

    inFlight = fetch(url.href, {
      credentials: 'same-origin',
      headers: {
        // Deliberately NOT sending X-Requested-With: an expired session should
        // still redirect the way a normal navigation would, rather than coming
        // back as JSON. If it does, the sanity checks below take the full load.
        'X-Spa-Navigation': '1',
        Accept: 'text/html, application/xhtml+xml',
      },
    })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.text();
      })
      .then(function (html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        if (!doc.querySelector(MAIN)) {
          // A login screen, an error page, or JSON. Not something we can swap in.
          throw new Error('response is not a page this shim can swap');
        }
        apply(doc, url);
        if (push) window.history.pushState({ spa: true }, '', url.href);
        window.scrollTo(0, 0);
      })
      .catch(function (err) {
        if (callInProgress()) {
          // The fallback below is a full page load, which destroys the document
          // the call lives in. A call is not recoverable and a page is, so stay
          // put and say what happened rather than dropping the customer's call
          // to render a page that already failed to render once.
          console.warn('spa-navigation: navigation failed, holding the active call', err);
          fail('Could not open ' + url.pathname + '. Navigation was blocked to protect your active call.');
          return;
        }
        // Nothing live to protect, so behave exactly as a normal navigation.
        console.warn('spa-navigation: falling back to a full page load', err);
        window.location.href = url.href;
      })
      .then(function () {
        inFlight = null;
        endProgress();
      });

    return inFlight;
  }

  /** Swap the page regions for the ones in `doc` and bring them to life. */
  function apply(doc, url) {
    // Captured before the swap: `rescan` needs a container that still includes
    // the nav root, because the nav is itself a Livewire component root and
    // `rescan(node)` only looks *inside* the node it is given.
    var container =
      (document.querySelector(NAV) || document.querySelector(MAIN)).closest('[x-data]') ||
      document.body;

    swap(doc, NAV);
    swap(doc, HEADER);
    swap(doc, MAIN);

    // Synchronous, and before Alpine gets a look at the new markup: `$wire`
    // resolves `closest('[wire:id]').__livewire`, so Livewire has to claim the
    // new roots first. Already-initialised components are skipped, because
    // `rescan` ignores anything whose id is in the store and the constructor
    // strips `wire:initial-data` from components it has taken over.
    rescan(container);

    document.title = doc.title || document.title;
    document.dispatchEvent(new CustomEvent('spa:navigated', {
      detail: { url: url.href, path: url.pathname + url.search },
    }));
  }

  /** Replace one region, inserting or removing it as the new page requires. */
  function swap(doc, selector) {
    var incoming = doc.querySelector(selector);
    var current = document.querySelector(selector);

    if (!incoming) {
      // The new page has no such region, e.g. a page without a heading. `remove`
      // only tears down Livewire, so the node itself still has to go — otherwise
      // the page keeps the heading it just navigated away from.
      if (current) {
        remove(current);
        current.remove();
      }
      return;
    }

    var replacement = document.importNode(incoming, true);
    if (current) {
      remove(current);
      current.parentNode.insertBefore(replacement, current.nextSibling);
      // `insertBefore(nextSibling)` cannot be used with the node we are replacing
      // in one call, so finish the move here.
      current.remove();
      current = replacement;
    } else {
      current = insert(replacement, selector);
    }

    // Scripts inserted via importNode/replaceWith never execute, and a good
    // number of these pages initialise their own widgets inline. Deferred to a
    // task so Alpine's MutationObserver — which initialises on a microtask —
    // has walked the new tree first.
    setTimeout(function () { runScripts(current); }, 0);
  }

  /** Put a region that only the new page has into the right place. */
  function insert(node, selector) {
    var main = document.querySelector(MAIN);

    if (selector === HEADER) {
      // The heading always sits directly above the page content.
      if (main) return main.parentNode.insertBefore(node, main);
    } else if (selector === MAIN) {
      // Below the heading when there is one, otherwise below the sidebar.
      var host = document.querySelector(HEADER) || document.querySelector(NAV);
      if (host && host.parentNode) return host.parentNode.insertBefore(node, host.nextSibling);
    }

    return document.body.appendChild(node);
  }

  /**
   * Detach the Livewire components inside a region before it is thrown away.
   *
   * Livewire only cleans up components it discards during its own morphs, so
   * without this the components in the old page stay registered: still able to
   * issue requests, still holding listeners, and never re-initialised.
   */
  function remove(region) {
    if (!window.Livewire || !Livewire.components) return region;
    // The region may itself be a component root, and querySelectorAll does not
    // include the node it is called on.
    [region].concat(Array.prototype.slice.call(region.querySelectorAll(WIRE_ID)))
      .forEach(function (el) {
        var component = el.__livewire;
        if (component && Livewire.components.hasComponent(component.id)) {
          Livewire.components.removeComponent(component);
        }
      });
    return region;
  }

  /**
   * Bootstrap the Livewire components in the new markup.
   *
   * Alpine needs no help here: `Alpine.start()` installs a document-wide
   * MutationObserver that calls `initTree` on every added node, so the new
   * markup is initialised on its own. Calling `Alpine.initTree` as well would
   * initialise everything twice.
   */
  function rescan(node) {
    if (!window.Livewire || typeof Livewire.rescan !== 'function') return;
    try {
      Livewire.rescan(node);
    } catch (err) {
      console.warn('spa-navigation: Livewire could not initialise the new page', err);
    }
  }

  /** Re-create script elements so the browser actually runs them. */
  function runScripts(node) {
    if (!node) return;
    var scripts = node.querySelectorAll('script');
    Array.prototype.forEach.call(scripts, function (old) {
      var fresh = document.createElement('script');
      Array.prototype.forEach.call(old.attributes, function (attr) {
        fresh.setAttribute(attr.name, attr.value);
      });
      fresh.textContent = old.textContent;
      old.replaceWith(fresh);
    });
  }

  // ---- Progress indicator ----------------------------------------------

  var bar = null;

  function startProgress() {
    if (bar) return;
    bar = document.createElement('div');
    bar.setAttribute('data-spa-progress', '');
    bar.style.cssText =
      'position:fixed;top:0;left:0;height:2px;width:0;background:#0f766e;' +
      'z-index:9999;transition:width .2s ease;box-shadow:0 0 8px #0f766e';
    document.body.appendChild(bar);
    requestAnimationFrame(function () {
      if (bar) bar.style.width = '70%';
    });
  }

  function endProgress() {
    if (!bar) return;
    var done = bar;
    bar = null;
    done.style.width = '100%';
    setTimeout(function () { done.remove(); }, 250);
  }

  /**
   * Tell the agent a navigation was refused, in place of the page load that
   * would have taken their call with it.
   */
  function fail(message) {
    endProgress();
    var note = document.createElement('div');
    note.setAttribute('data-spa-error', '');
    note.style.cssText =
      'position:fixed;top:0;left:0;right:0;z-index:9999;padding:10px 16px;' +
      'background:#b91c1c;color:#fff;font:600 13px/1.4 system-ui,-apple-system,sans-serif;' +
      'text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.2)';
    note.textContent = message;
    document.body.appendChild(note);
    setTimeout(function () { note.remove(); }, 6000);
  }

  // ---- Wiring -----------------------------------------------------------

  document.addEventListener('click', function (event) {
    var url = targetFor(event);
    if (!url) return;
    event.preventDefault();
    navigate(url, true);
  });

  // Back and forward have to be handled too, or the URL and the rendered page
  // drift apart as soon as the agent uses the browser controls.
  window.addEventListener('popstate', function (event) {
    if (!(event.state && event.state.spa)) return;
    event.preventDefault();
    navigate(new URL(window.location.href), false).then(function () {
      var y = scrollPositions[window.location.href];
      window.scrollTo(0, typeof y === 'number' ? y : 0);
    });
  });

  // Exposed for debugging and for anything that needs to force a soft swap.
  window.AusoSpaNav = { navigate: navigate };
})();
