(function () {
  var state = {
    items: [],
    index: 0,
    isOpen: false,
    closeTimer: null,
    returnFocusTo: null,
  };

  var elements = {
    root: null,
    dialog: null,
    backdrop: null,
    image: null,
    caption: null,
    counter: null,
    footer: null,
    swipeHint: null,
    prev: null,
    next: null,
    close: null,
  };

  var touchState = {
    startX: 0,
    deltaX: 0,
  };

  function createLightbox() {
    if (elements.root) {
      return;
    }

    var root = document.createElement('div');
    root.className = 'mc-lightbox';
    root.setAttribute('hidden', 'hidden');
    root.innerHTML = '' +
      '<div class="mc-lightbox__backdrop" data-mc-lightbox-close="1"></div>' +
      '<div class="mc-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Galeria de imagenes del evento">' +
      '  <button type="button" class="mc-lightbox__close" data-mc-lightbox-close="1" aria-label="Cerrar galeria">×</button>' +
      '  <button type="button" class="mc-lightbox__nav mc-lightbox__nav--prev" aria-label="Imagen anterior">‹</button>' +
      '  <img class="mc-lightbox__image" src="" alt="" />' +
      '  <button type="button" class="mc-lightbox__nav mc-lightbox__nav--next" aria-label="Imagen siguiente">›</button>' +
      '  <div class="mc-lightbox__footer">' +
      '    <p class="mc-lightbox__caption"></p>' +
      '    <p class="mc-lightbox__counter"></p>' +
      '  </div>' +
      '  <p class="mc-lightbox__swipe-hint" aria-hidden="true">Desliza para navegar</p>' +
      '</div>';

    document.body.appendChild(root);

    elements.root = root;
    elements.dialog = root.querySelector('.mc-lightbox__dialog');
    elements.backdrop = root.querySelector('.mc-lightbox__backdrop');
    elements.image = root.querySelector('.mc-lightbox__image');
    elements.caption = root.querySelector('.mc-lightbox__caption');
    elements.counter = root.querySelector('.mc-lightbox__counter');
    elements.footer = root.querySelector('.mc-lightbox__footer');
    elements.swipeHint = root.querySelector('.mc-lightbox__swipe-hint');
    elements.prev = root.querySelector('.mc-lightbox__nav--prev');
    elements.next = root.querySelector('.mc-lightbox__nav--next');
    elements.close = root.querySelector('.mc-lightbox__close');

    root.addEventListener('click', function (event) {
      if (event.target && event.target.getAttribute('data-mc-lightbox-close') === '1') {
        closeLightbox();
      }
    });

    elements.prev.addEventListener('click', function () {
      move(-1);
    });

    elements.next.addEventListener('click', function () {
      move(1);
    });

    elements.image.addEventListener('load', function () {
      elements.image.classList.remove('is-changing');
    });

    elements.image.addEventListener('touchstart', function (event) {
      if (!event.touches || !event.touches.length) {
        return;
      }

      touchState.startX = event.touches[0].clientX;
      touchState.deltaX = 0;
    }, { passive: true });

    elements.image.addEventListener('touchmove', function (event) {
      if (!event.touches || !event.touches.length) {
        return;
      }

      touchState.deltaX = event.touches[0].clientX - touchState.startX;
    }, { passive: true });

    elements.image.addEventListener('touchend', function () {
      if (Math.abs(touchState.deltaX) < 48) {
        return;
      }

      hideSwipeHint();
      move(touchState.deltaX < 0 ? 1 : -1);
    });

    document.addEventListener('keydown', function (event) {
      if (!elements.root || elements.root.hasAttribute('hidden')) {
        return;
      }

      if (event.key === 'Tab') {
        keepFocusInDialog(event);
        return;
      }

      if (event.key === 'Escape') {
        event.preventDefault();
        closeLightbox();
      } else if (event.key === 'ArrowLeft') {
        event.preventDefault();
        move(-1);
      } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        move(1);
      }
    });
  }

  function move(step) {
    if (!state.items.length) {
      return;
    }

    state.index = (state.index + step + state.items.length) % state.items.length;
    render(true);
  }

  function render(withMotion) {
    var current = state.items[state.index];

    if (!current) {
      return;
    }

    if (withMotion) {
      elements.image.classList.add('is-changing');
      elements.footer.classList.add('is-updating');
    }

    elements.image.src = current.url || '';
    elements.image.alt = current.alt || '';
    elements.caption.textContent = current.alt || '';
    elements.counter.textContent = (state.index + 1) + ' / ' + state.items.length;

    window.setTimeout(function () {
      elements.footer.classList.remove('is-updating');
    }, 120);

    var hasMultiple = state.items.length > 1;
    elements.prev.disabled = !hasMultiple;
    elements.next.disabled = !hasMultiple;
    elements.prev.classList.toggle('is-hidden', !hasMultiple);
    elements.next.classList.toggle('is-hidden', !hasMultiple);
  }

  function openLightbox(items, startIndex) {
    createLightbox();

    state.items = Array.isArray(items) ? items.filter(function (item) {
      return item && item.url;
    }) : [];

    if (!state.items.length) {
      return;
    }

    if (state.closeTimer) {
      window.clearTimeout(state.closeTimer);
      state.closeTimer = null;
    }

    state.index = Math.min(Math.max(startIndex, 0), state.items.length - 1);
    render(false);

    showSwipeHint();

    state.isOpen = true;

    elements.root.removeAttribute('hidden');
    requestAnimationFrame(function () {
      elements.root.classList.add('is-visible');
    });

    document.body.classList.add('mc-lightbox-open');
    elements.close.focus();
  }

  function closeLightbox() {
    if (!elements.root || !state.isOpen) {
      return;
    }

    state.isOpen = false;

    elements.root.classList.remove('is-visible');
    document.body.classList.remove('mc-lightbox-open');

    if (state.closeTimer) {
      window.clearTimeout(state.closeTimer);
    }

    state.closeTimer = window.setTimeout(function () {
      elements.root.setAttribute('hidden', 'hidden');
      state.items = [];
      state.index = 0;
      elements.image.src = '';
      elements.image.classList.remove('is-changing');

      if (state.returnFocusTo && typeof state.returnFocusTo.focus === 'function') {
        state.returnFocusTo.focus();
      }

      state.returnFocusTo = null;
    }, 220);
  }

  function keepFocusInDialog(event) {
    var focusable = elements.dialog.querySelectorAll('button:not([disabled])');

    if (!focusable.length) {
      return;
    }

    var first = focusable[0];
    var last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
      return;
    }

    if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  function showSwipeHint() {
    if (!elements.swipeHint) {
      return;
    }

    elements.swipeHint.classList.remove('is-hidden');

    window.setTimeout(function () {
      hideSwipeHint();
    }, 1900);
  }

  function hideSwipeHint() {
    if (!elements.swipeHint) {
      return;
    }

    elements.swipeHint.classList.add('is-hidden');
  }

  function parseGallery(trigger) {
    var raw = trigger.getAttribute('data-mc-lightbox-gallery') || '[]';

    try {
      return JSON.parse(raw);
    } catch (error) {
      return [];
    }
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest('.event-item__gallery-more-trigger');

    if (!trigger) {
      return;
    }

    event.preventDefault();

    var items = parseGallery(trigger);
    var startIndex = parseInt(trigger.getAttribute('data-mc-lightbox-start-index') || '0', 10);

    if (Number.isNaN(startIndex)) {
      startIndex = 0;
    }

    state.returnFocusTo = trigger;

    openLightbox(items, startIndex);
  });
})();
