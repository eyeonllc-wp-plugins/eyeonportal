(function ($) {
  'use strict';

  if (typeof EYEON_CHATBOT === 'undefined') {
    return;
  }

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

      if ($body.children().length) {
        $body.append('<br>');
      }
      $body.append(document.createTextNode(trimmed));
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
      role === 'assistant' ? renderAssistantMessage(text) : $('<div class="eyeon-chatbot__message ' + cls + '"></div>').text(text);
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

  function trimHistory() {
    if (history.length > 6) {
      history = history.slice(-6);
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
    trimHistory();
    setTyping(true);

    $.ajax({
      url: EYEON_CHATBOT.ajaxurl,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'eyeon_chat_request',
        nonce: EYEON_CHATBOT.nonce,
        message: message,
        history_json: JSON.stringify(history.slice(0, -1)),
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
        trimHistory();
      })
      .fail(function (xhr) {
        setTyping(false);
        var msg = EYEON_CHATBOT.offlineMessage;
        if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.msg) {
          msg = xhr.responseJSON.data.msg;
        }
        appendMessage('assistant', msg);
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
})(jQuery);
