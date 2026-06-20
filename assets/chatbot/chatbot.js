(function ($) {
  'use strict';

  if (typeof EYEON_CHATBOT === 'undefined') {
    return;
  }

  var STORAGE_TTL_MS = 24 * 60 * 60 * 1000;
  var MAX_STORED_MESSAGES = 50;
  var API_HISTORY_LIMIT = 6;

  var history = [];
  var isOpen = false;
  var isSending = false;

  var $root = $('#eyeon-chatbot-root');
  var $launcher = $('#eyeon-chatbot-launcher');
  var $panel = $('#eyeon-chatbot-panel');
  var $messages = $('#eyeon-chatbot-messages');
  var $form = $('#eyeon-chatbot-form');
  var $input = $('#eyeon-chatbot-input');
  var $send = $('#eyeon-chatbot-send');
  var $close = $('#eyeon-chatbot-close');

  if (EYEON_CHATBOT.accentColor) {
    $root.css('--eyeon-chat-accent', EYEON_CHATBOT.accentColor);
    $launcher.css('background-color', EYEON_CHATBOT.accentColor);
  }

  function storageKey() {
    var centerId = EYEON_CHATBOT.centerId || window.location.hostname || 'default';
    return 'eyeon_chatbot_v1_' + centerId;
  }

  function persistHistory() {
    try {
      if (!window.localStorage) {
        return;
      }
      var payload = {
        expiresAt: Date.now() + STORAGE_TTL_MS,
        messages: history.slice(-MAX_STORED_MESSAGES),
      };
      localStorage.setItem(storageKey(), JSON.stringify(payload));
    } catch (e) {
      // Ignore quota / private mode errors.
    }
  }

  function clearStoredHistory() {
    try {
      if (window.localStorage) {
        localStorage.removeItem(storageKey());
      }
    } catch (e) {}
  }

  function loadStoredHistory() {
    try {
      if (!window.localStorage) {
        return;
      }
      var raw = localStorage.getItem(storageKey());
      if (!raw) {
        return;
      }

      var payload = JSON.parse(raw);
      if (!payload || !payload.expiresAt || Date.now() > payload.expiresAt) {
        clearStoredHistory();
        return;
      }

      if (!Array.isArray(payload.messages)) {
        clearStoredHistory();
        return;
      }

      history = payload.messages
        .filter(function (item) {
          return (
            item &&
            (item.role === 'user' || item.role === 'assistant') &&
            typeof item.content === 'string' &&
            item.content.trim() !== ''
          );
        })
        .slice(-MAX_STORED_MESSAGES);

      history.forEach(function (item) {
        appendMessage(item.role, item.content);
      });
    } catch (e) {
      clearStoredHistory();
    }
  }

  function buildPhoneTelHref(formattedPhone) {
    var digits = String(formattedPhone || '').replace(/\D/g, '');
    if (digits.length === 10) {
      return 'tel:+1' + digits;
    }
    if (digits.length === 11 && digits.charAt(0) === '1') {
      return 'tel:+' + digits;
    }
    return digits ? 'tel:' + digits : '';
  }

  function appendTextWithPhoneLinks($container, text) {
    var phonePattern = /\b(\d{3}\.\d{3}\.\d{4})\b/g;
    var lastIndex = 0;
    var match;

    while ((match = phonePattern.exec(text)) !== null) {
      if (match.index > lastIndex) {
        $container.append(document.createTextNode(text.slice(lastIndex, match.index)));
      }
      var href = buildPhoneTelHref(match[1]);
      if (href) {
        $container.append(
          $('<a></a>')
            .addClass('eyeon-chatbot__message-link eyeon-chatbot__message-link--phone')
            .attr('href', href)
            .text(match[1])
        );
      } else {
        $container.append(document.createTextNode(match[1]));
      }
      lastIndex = phonePattern.lastIndex;
    }

    if (lastIndex < text.length) {
      $container.append(document.createTextNode(text.slice(lastIndex)));
    }
  }

  function buildItemUrl(type, slug) {
    var bases = EYEON_CHATBOT.linkBases || {};
    var base = bases[type];
    if (!base || !slug) {
      return '';
    }
    return base + encodeURIComponent(slug);
  }

  function renderAssistantMessage(text) {
    var $msg = $('<div class="eyeon-chatbot__message eyeon-chatbot__message--assistant"></div>');
    var lines = String(text || '').split('\n');
    var $body = $('<div class="eyeon-chatbot__message-body"></div>');
    var $links = $('<div class="eyeon-chatbot__message-links"></div>');
    var hasLinks = false;

    lines.forEach(function (line) {
      var trimmed = $.trim(line);
      if (!trimmed) {
        return;
      }

      var linkMatch = trimmed.match(/^\[([^\]]+)\]\((deal|store|event|career|news):([^)]+)\)$/);
      if (linkMatch) {
        var href = buildItemUrl(linkMatch[2], linkMatch[3]);
        if (href) {
          hasLinks = true;
          $links.append(
            $('<a></a>')
              .addClass('eyeon-chatbot__message-link')
              .attr('href', href)
              .attr('target', '_blank')
              .attr('rel', 'noopener noreferrer')
              .text(linkMatch[1])
          );
        }
        return;
      }

      if ($body.contents().length) {
        $body.append('<br>');
      }
      appendTextWithPhoneLinks($body, trimmed);
    });

    if ($body.children().length || $body.text()) {
      $msg.append($body);
    }
    if (hasLinks) {
      $msg.append($links);
    }
    if (!$msg.children().length) {
      $msg.text(text);
    }
    return $msg;
  }

  function appendMessage(role, text) {
    var cls = role === 'user' ? 'eyeon-chatbot__message--user' : 'eyeon-chatbot__message--assistant';
    var $msg =
      role === 'assistant'
        ? renderAssistantMessage(text)
        : $('<div class="eyeon-chatbot__message ' + cls + '"></div>').text(text);
    $messages.append($msg);
    $messages.scrollTop($messages[0].scrollHeight);
    return $msg;
  }

  function showWelcome() {
    if ($messages.children().length === 0 && EYEON_CHATBOT.welcomeMessage) {
      appendMessage('assistant', EYEON_CHATBOT.welcomeMessage);
    }
  }

  function setTyping(show) {
    $('#eyeon-chatbot-typing').remove();
    if (show) {
      $messages.append(
        '<div id="eyeon-chatbot-typing" class="eyeon-chatbot__message eyeon-chatbot__message--typing">Typing...</div>'
      );
      $messages.scrollTop($messages[0].scrollHeight);
    }
  }

  function openPanel() {
    isOpen = true;
    $panel.prop('hidden', false);
    showWelcome();
    $input.trigger('focus');
  }

  function closePanel() {
    isOpen = false;
    $panel.prop('hidden', true);
  }

  function trimHistoryForApi() {
    return history.slice(-API_HISTORY_LIMIT);
  }

  function capStoredHistory() {
    if (history.length > MAX_STORED_MESSAGES) {
      history = history.slice(-MAX_STORED_MESSAGES);
    }
  }

  function sendMessage(message) {
    if (!message || isSending) {
      return;
    }

    isSending = true;
    $send.prop('disabled', true);
    appendMessage('user', message);
    history.push({ role: 'user', content: message });
    capStoredHistory();
    persistHistory();
    setTyping(true);

    $.ajax({
      url: EYEON_CHATBOT.ajaxurl,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'eyeon_chat_request',
        nonce: EYEON_CHATBOT.nonce,
        message: message,
        history_json: JSON.stringify(trimHistoryForApi().slice(0, -1)),
      },
    })
      .done(function (response) {
        setTyping(false);
        var reply =
          response && response.success && response.data && response.data.reply
            ? response.data.reply
            : EYEON_CHATBOT.offlineMessage;
        appendMessage('assistant', reply);
        history.push({ role: 'assistant', content: reply });
        capStoredHistory();
        persistHistory();
      })
      .fail(function (xhr) {
        setTyping(false);
        var msg = EYEON_CHATBOT.offlineMessage;
        if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.msg) {
          msg = xhr.responseJSON.data.msg;
        }
        appendMessage('assistant', msg);
        history.push({ role: 'assistant', content: msg });
        capStoredHistory();
        persistHistory();
      })
      .always(function () {
        isSending = false;
        $send.prop('disabled', false);
        $input.trigger('focus');
      });
  }

  $launcher.on('click', function () {
    if (isOpen) {
      closePanel();
    } else {
      openPanel();
    }
  });

  $close.on('click', function () {
    closePanel();
  });

  $form.on('submit', function (e) {
    e.preventDefault();
    var message = $.trim($input.val());
    if (!message) {
      return;
    }
    $input.val('');
    sendMessage(message);
  });

  loadStoredHistory();
})(jQuery);
