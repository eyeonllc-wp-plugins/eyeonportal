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

  function storageKey() {
    var centerId = EYEON_CHATBOT.centerId || window.location.hostname || 'default';
    return 'eyeon_chatbot_v1_' + centerId;
  }

  function panelStorageKey() {
    return storageKey() + '_panel';
  }

  function persistPanelState() {
    try {
      if (!window.localStorage) {
        return;
      }
      localStorage.setItem(panelStorageKey(), isOpen ? 'open' : 'closed');
    } catch (e) {
      // Ignore quota / private mode errors.
    }
  }

  function loadPanelState() {
    try {
      if (!window.localStorage) {
        closePanel();
        return;
      }

      if (localStorage.getItem(panelStorageKey()) === 'open') {
        openPanel();
        return;
      }
    } catch (e) {}

    closePanel();
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

  function appendRichText($container, text) {
    var itemPattern = /\[([^\]]+)\]\((deal|store|event|career|news):([^)]+)\)/g;
    var phonePattern = /\b(\d{3}\.\d{3}\.\d{4})\b/g;
    var tokens = [];
    var match;

    while ((match = itemPattern.exec(text)) !== null) {
      tokens.push({
        start: match.index,
        end: match.index + match[0].length,
        kind: 'item',
        label: match[1],
        itemType: match[2],
        slug: match[3],
      });
    }

    while ((match = phonePattern.exec(text)) !== null) {
      tokens.push({
        start: match.index,
        end: match.index + match[0].length,
        kind: 'phone',
        phone: match[1],
      });
    }

    tokens.sort(function (a, b) {
      return a.start - b.start;
    });

    var cursor = 0;
    tokens.forEach(function (token) {
      if (token.start < cursor) {
        return;
      }
      if (token.start > cursor) {
        $container.append(document.createTextNode(text.slice(cursor, token.start)));
      }
      if (token.kind === 'item') {
        var href = buildItemUrl(token.itemType, token.slug);
        if (href) {
          $container.append(
            $('<a></a>')
              .addClass('eyeon-chatbot__message-link')
              .attr('href', href)
              .attr('target', '_blank')
              .attr('rel', 'noopener noreferrer')
              .text(token.label)
          );
        } else {
          $container.append(document.createTextNode(token.label));
        }
      } else if (token.kind === 'phone') {
        var telHref = buildPhoneTelHref(token.phone);
        if (telHref) {
          $container.append(
            $('<a></a>')
              .addClass('eyeon-chatbot__message-link eyeon-chatbot__message-link--phone')
              .attr('href', telHref)
              .text(token.phone)
          );
        } else {
          $container.append(document.createTextNode(token.phone));
        }
      }
      cursor = token.end;
    });

    if (cursor < text.length) {
      $container.append(document.createTextNode(text.slice(cursor)));
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

  var UNORDERED_LIST_PATTERN = /^[-*+]\s+(.+)$/;
  var ORDERED_LIST_PATTERN = /^\d+\.\s+(.+)$/;

  function parseListLine(line) {
    var trimmed = $.trim(line);
    if (!trimmed) {
      return null;
    }

    var unordered = trimmed.match(UNORDERED_LIST_PATTERN);
    if (unordered) {
      return { type: 'ul', content: unordered[1] };
    }

    var ordered = trimmed.match(ORDERED_LIST_PATTERN);
    if (ordered) {
      return { type: 'ol', content: ordered[1] };
    }

    return null;
  }

  function appendParagraphLines($container, lines) {
    if (!lines.length) {
      return;
    }

    var $paragraph = $('<p class="eyeon-chatbot__paragraph"></p>');
    lines.forEach(function (line, index) {
      if (index > 0) {
        $paragraph.append('<br>');
      }
      appendRichText($paragraph, line);
    });
    $container.append($paragraph);
  }

  function appendListBlock($container, type, items) {
    var tag = type === 'ol' ? 'ol' : 'ul';
    var listClass =
      type === 'ol'
        ? 'eyeon-chatbot__list eyeon-chatbot__list--ordered'
        : 'eyeon-chatbot__list eyeon-chatbot__list--unordered';
    var $list = $('<' + tag + '></' + tag + '>').addClass(listClass);

    items.forEach(function (item) {
      var $item = $('<li></li>');
      appendRichText($item, item);
      $list.append($item);
    });

    $container.append($list);
  }

  function renderAssistantMessage(text) {
    var $msg = $('<div class="eyeon-chatbot__message eyeon-chatbot__message--assistant"></div>');
    var lines = String(text || '').split('\n');
    var $body = $('<div class="eyeon-chatbot__message-body"></div>');
    var index = 0;

    while (index < lines.length) {
      var trimmed = $.trim(lines[index]);
      if (!trimmed) {
        index++;
        continue;
      }

      var listLine = parseListLine(trimmed);
      if (listLine) {
        var listType = listLine.type;
        var items = [];

        while (index < lines.length) {
          var current = parseListLine($.trim(lines[index]));
          if (!current || current.type !== listType) {
            break;
          }
          items.push(current.content);
          index++;
        }

        appendListBlock($body, listType, items);
        continue;
      }

      var paragraphLines = [];
      while (index < lines.length) {
        var lineTrimmed = $.trim(lines[index]);
        if (!lineTrimmed) {
          break;
        }
        if (parseListLine(lineTrimmed)) {
          break;
        }
        paragraphLines.push(lineTrimmed);
        index++;
      }

      appendParagraphLines($body, paragraphLines);
    }

    if ($body.contents().length || $body.text()) {
      $msg.append($body);
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
    persistPanelState();
    $input.trigger('focus');
  }

  function closePanel() {
    isOpen = false;
    $panel.prop('hidden', true);
    persistPanelState();
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
  loadPanelState();
})(jQuery);
