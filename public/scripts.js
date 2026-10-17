(function () {
  const root = document.querySelector('.chat-widget');
  if (!root) return;

  const input = root.querySelector('.chat-input');
  const sendButton = root.querySelector('.chat-send-button');
  const messages = root.querySelector('.chat-messages');
  const closeButton = root.querySelector('.chat-close-button');
  const reopenButton = root.querySelector('.chat-reopen-button');
  const csrfToken = document.querySelector('meta[name="csrf-token"]');

  const faqs = JSON.parse(root.querySelector('.chat-faq-data').textContent);
  const starterFaqs = selectStarterFaqs();
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

  let hasOpenedBefore = false;
  let isBusy = false;
  let requestPending = false;
  let pendingReplies = 0;
  let replyQueue = Promise.resolve();

  function selectStarterFaqs() {
    const storageKey = 'ahmedia.chat.starterFaqIds';
    let savedIds = [];
    try {
      const saved = JSON.parse(sessionStorage.getItem(storageKey) || '[]');
      if (Array.isArray(saved)) savedIds = saved;
    } catch (error) {
      // The widget still works when browser storage is unavailable.
    }

    const selected = [...new Set(savedIds)]
      .map(function (id) { return faqs.find(function (faq) { return faq.id === id; }); })
      .filter(Boolean).slice(0, 2);
    const remaining = faqs.filter(function (faq) { return !selected.includes(faq); });
    while (selected.length < 2 && remaining.length) {
      selected.push(remaining.splice(Math.floor(Math.random() * remaining.length), 1)[0]);
    }

    try {
      sessionStorage.setItem(storageKey, JSON.stringify(selected.map(function (faq) { return faq.id; })));
    } catch (error) {
      // Keep the selected pair in memory for this page instead.
    }
    return selected;
  }

  function delay(ms) {
    return new Promise(function (resolve) { setTimeout(resolve, ms); });
  }

  // Reveals one word per tick at a fixed interval, rather than growing a
  // time/duration ratio that can jump several words at once on a tick --
  // that variable-size jump is what read as jittery. Longer replies get a
  // shorter per-word delay so they don't drag on forever.
  function revealWords(element) {
    if (reduceMotion.matches) return Promise.resolve();

    const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT);
    const words = [];
    while (walker.nextNode()) {
      const node = walker.currentNode;
      (node.nodeValue.match(/\S+\s*|\s+/g) || []).forEach(function (word) {
        words.push({ node: node, text: word });
      });
      node.nodeValue = '';
    }
    if (!words.length) return Promise.resolve();

    element.setAttribute('aria-busy', 'true');
    const wordDelay = Math.max(18, Math.min(55, 3500 / words.length));
    let revealed = 0;
    return new Promise(function (resolve) {
      const timer = setInterval(function () {
        const word = words[revealed++];
        word.node.nodeValue += word.text;
        messages.scrollTop = messages.scrollHeight;
        if (revealed === words.length) {
          clearInterval(timer);
          element.removeAttribute('aria-busy');
          resolve();
        }
      }, wordDelay);
    });
  }

  // Multi-paragraph answers reveal one block at a time (full paragraph typed
  // out, brief pause, next paragraph starts) instead of typing continuously
  // straight through paragraph breaks.
  function revealMessage(element) {
    const blocks = Array.from(element.children).filter(function (child) {
      return child.textContent.trim().length > 0;
    });
    if (blocks.length <= 1) return revealWords(element);

    blocks.forEach(function (block) { block.style.display = 'none'; });

    return blocks.reduce(function (promise, block, index) {
      return promise.then(function () {
        block.style.display = '';
        return revealWords(block);
      }).then(function () {
        if (index < blocks.length - 1) return delay(220);
      });
    }, Promise.resolve());
  }

  function addMessage(content, isUser, isHtml) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message ' + (isUser ? 'chat-user-message' : 'chat-bot-message chat-answer');

    if (isHtml) {
      // FAQ answers are trusted, admin-authored RichEditor HTML -- the same
      // trust level the FAQ page itself renders with {!! $item->answer !!}.
      messageDiv.innerHTML = content;
    } else {
      messageDiv.textContent = content;
    }

    if (isUser) {
      messages.appendChild(messageDiv);
      messages.scrollTop = messages.scrollHeight;
      return Promise.resolve();
    }

    pendingReplies++;
    syncBusy();
    replyQueue = replyQueue.then(function () {
      messages.appendChild(messageDiv);
      messages.scrollTop = messages.scrollHeight;
      return revealMessage(messageDiv);
    }).finally(function () {
      pendingReplies--;
      syncBusy();
    });
    return replyQueue;
  }

  function addTypingIndicator() {
    const indicator = document.createElement('div');
    indicator.className = 'chat-message chat-bot-message chat-typing';
    indicator.innerHTML = '<span></span><span></span><span></span>';
    messages.appendChild(indicator);
    messages.scrollTop = messages.scrollHeight;

    return indicator;
  }

  function setBusy(busy) {
    requestPending = busy;
    syncBusy();
  }

  function syncBusy() {
    isBusy = requestPending || pendingReplies > 0;
    input.disabled = isBusy;
    sendButton.disabled = isBusy;
    root.querySelectorAll('.chat-chip, .chat-option').forEach(function (button) {
      button.disabled = isBusy;
    });
  }

  // Chip/option answers already have the content on hand (no network round
  // trip), but still pause on a "thinking" indicator first so they don't
  // feel out of step with the answers that do go through /chat/ask.
  function respondAfterThinking(question, answer) {
    setBusy(true);
    const typingIndicator = addTypingIndicator();
    return delay(500).then(function () {
      typingIndicator.remove();
      return addMessage(answer, false, true);
    }).then(function () {
      renderConversionCta(question);
    }).finally(function () {
      setBusy(false);
    });
  }

  function renderChips() {
    if (!starterFaqs.length) return;
    const chipsRow = document.createElement('div');
    chipsRow.className = 'chat-chips';

    starterFaqs.forEach(function (faq) {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'chat-chip';
      chip.textContent = faq.question;
      chip.addEventListener('click', function () {
        if (isBusy) return;
        chipsRow.remove();
        addMessage(faq.question, true);
        respondAfterThinking(faq.question, faq.answer);
      });
      chipsRow.appendChild(chip);
    });

    messages.appendChild(chipsRow);
    messages.scrollTop = messages.scrollHeight;
  }

  function renderOptions(optionsBlock, matches) {
    matches.forEach(function (match) {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'chat-option';
      button.textContent = match.question;
      button.addEventListener('click', function () {
        if (isBusy) return;
        addMessage(match.question, true);
        optionsBlock.remove();
        respondAfterThinking(match.question, match.answer);
      });
      optionsBlock.appendChild(button);
    });
  }

  // The widget is designed for a single-turn Q&A, not an open-ended
  // conversation -- every answered FAQ (chip, option, or confident match)
  // ends with a direct path onward, rather than leaving the visitor to find
  // the FAQ page or contact form on their own.
  function renderConversionCta(question) {
    const cta = document.createElement('div');
    cta.className = 'chat-options chat-cta-row';

    const faqLink = document.createElement('a');
    faqLink.href = '/faq';
    faqLink.className = 'chat-option';
    faqLink.textContent = 'View the FAQs';
    cta.appendChild(faqLink);

    const projectLink = document.createElement('a');
    projectLink.href = '/#contact';
    projectLink.className = 'chat-option chat-cta-option';
    projectLink.textContent = 'Start a Project';
    cta.appendChild(projectLink);

    messages.appendChild(cta);
    messages.scrollTop = messages.scrollHeight;
  }

  function postJson(url, body) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
      },
      body: JSON.stringify(body),
    });
  }

  function renderLeadForm(question) {
    const form = document.createElement('form');
    form.className = 'chat-lead-form';
    form.innerHTML =
      '<input type="text" class="chat-lead-field" name="name" placeholder="Your name" required />' +
      '<input type="email" class="chat-lead-field" name="email" placeholder="Your email" required />' +
      // Hidden from real visitors via CSS (see .chat-lead-form input[name="website"]
      // in styles.css) -- a bot filling every field it sees will fill this
      // one too, which is exactly what marks it as a bot server-side.
      '<input type="text" class="chat-lead-honeypot" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" />' +
      '<button type="submit" class="chat-lead-submit">Send to Adam</button>' +
      '<p class="chat-field-error" hidden></p>';

    const errorEl = form.querySelector('.chat-field-error');
    const submitButton = form.querySelector('.chat-lead-submit');

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      errorEl.hidden = true;
      submitButton.disabled = true;

      postJson('/chat/lead', {
        name: form.name.value,
        email: form.email.value,
        question: question,
        website: form.website.value,
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { ok: response.ok, data: data };
          });
        })
        .then(function (result) {
          if (!result.ok) {
            const firstError = result.data.errors && Object.values(result.data.errors)[0];
            errorEl.textContent = (firstError && firstError[0]) || 'Something went wrong. Please send it again.';
            errorEl.hidden = false;
            submitButton.disabled = false;
            return;
          }

          form.remove();
          addMessage(result.data.message, false);
        })
        .catch(function () {
          errorEl.textContent = 'Something went wrong. Please send it again.';
          errorEl.hidden = false;
          submitButton.disabled = false;
        });
    });

    messages.appendChild(form);
    messages.scrollTop = messages.scrollHeight;
  }

  function renderFallback(question) {
    return addMessage("I can help with services, pricing, packages and how projects work. Ask about one of those, or send your question straight to Adam.", false)
      .then(function () {
        const options = document.createElement('div');
        options.className = 'chat-options';
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'chat-option';
        button.textContent = 'Send my question to Adam';
        button.addEventListener('click', function () {
          if (isBusy) return;
          options.remove();
          renderLeadForm(question);
        });
        options.appendChild(button);
        messages.appendChild(options);
        messages.scrollTop = messages.scrollHeight;
      });
  }

  function handleResponse(question, data) {
    if (data.outcome === 'greeting' || data.outcome === 'farewell') {
      return addMessage(data.reply, false);
    }

    if (data.outcome === 'confident' && data.matches.length > 0) {
      return addMessage(data.matches[0].answer, false, true).then(function () {
        renderConversionCta(question);
      });
    }

    if (data.outcome === 'ambiguous' && data.matches.length > 0) {
      const optionsBlock = document.createElement('div');
      optionsBlock.className = 'chat-options';
      return addMessage("Here's what I can help with:", false).then(function () {
        messages.appendChild(optionsBlock);
        renderOptions(optionsBlock, data.matches);
        messages.scrollTop = messages.scrollHeight;
      });
    }

    return renderFallback(question);
  }

  function askQuestion(question) {
    if (!question.trim() || isBusy || !csrfToken) return;

    root.querySelectorAll('.chat-chips, .chat-options').forEach(function (element) { element.remove(); });
    addMessage(question, true);
    setBusy(true);

    const typingIndicator = addTypingIndicator();

    // The FAQ match itself resolves near-instantly -- pairing it with a
    // fixed minimum delay keeps the "thinking" indicator from just flashing
    // by, which read as too abrupt.
    Promise.all([
      postJson('/chat/ask', { question: question }).then(function (response) {
        if (!response.ok) throw new Error('chat request failed');
        return response.json();
      }),
      delay(500),
    ])
      .then(function (results) {
        const data = results[0];
        typingIndicator.remove();
        return handleResponse(question, data);
      })
      .catch(function () {
        typingIndicator.remove();
        return addMessage("Something went wrong on our end. Ask again in a moment, or use the contact form below.", false);
      })
      .finally(function () {
        setBusy(false);
      });
  }

  function sendMessage(message) {
    const question = message.trim();
    if (!question || isBusy) return;

    input.value = '';
    askQuestion(question);
  }

  closeButton.addEventListener('click', function () {
    root.classList.remove('is-open');
  });

  reopenButton.addEventListener('click', function () {
    root.classList.add('is-open');

    if (!hasOpenedBefore) {
      hasOpenedBefore = true;
      addMessage("Hi! I can answer questions about AH Media: services, pricing, packages and how projects work.", false)
        .then(renderChips);
    }
  });

  sendButton.addEventListener('click', function () {
    sendMessage(input.value);
  });

  input.addEventListener('keypress', function (event) {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      sendMessage(input.value);
    }
  });
})();

(function () {
  const root = document.querySelector('.back-to-top-link');
  if (!root) return;

  const threshold = 200;
  const link = root.querySelector('a');
  let hideTimer;

  function syncBackToTop() {
    if (window.scrollY > threshold) {
      root.classList.add('in');
      clearTimeout(hideTimer);
      requestAnimationFrame(() => root.classList.add('visible'));
      return;
    }

    root.classList.remove('visible');
    hideTimer = window.setTimeout(() => root.classList.remove('in'), 180);
  }

  link.addEventListener('click', function (event) {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  window.addEventListener('scroll', syncBackToTop, { passive: true });
  syncBackToTop();
})();
// On subpages with a CTA band, every "Start a Project" link (/#contact) opens
// the contact form inline instead of navigating to the homepage form. Pages
// without the panel (the homepage, which has its own form) are untouched.
(function () {
  const panel = document.getElementById('cta-contact-form');
  if (!panel) return;

  const scrollBehavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth';

  function openForm() {
    panel.hidden = false;
    document.querySelectorAll('[aria-controls="cta-contact-form"]').forEach(function (trigger) {
      trigger.setAttribute('aria-expanded', 'true');
    });
    const firstField = panel.querySelector('input[name="name"]');
    if (firstField) firstField.focus({ preventScroll: true });
    panel.scrollIntoView({ behavior: scrollBehavior, block: 'start' });
  }

  // Delegated so links added later (the chat widget's "Start a Project") work too.
  document.addEventListener('click', function (event) {
    const link = event.target.closest('a[href="/#contact"]');
    if (!link) return;
    event.preventDefault();
    openForm();
  });

  // After a submission the page reloads with the panel already open; bring
  // the thank-you message or the validation errors into view.
  if (!panel.hidden) panel.scrollIntoView({ block: 'start' });
})();
