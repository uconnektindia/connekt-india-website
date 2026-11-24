<?php /** "Connekt" chatbot widget. Logic in js/chatbot.js, answers in data/chatbot-faq.json */ ?>
<button class="fab fab-chat" id="chatToggle" aria-label="Open chat with Connekt" aria-expanded="false" aria-controls="chatPanel">
  <span class="fab-dot" id="chatDot"></span>
  <?= icon('message', 'fab-ico ico-open', 26) ?>
  <?= icon('x', 'fab-ico ico-close', 26) ?>
</button>

<section class="chat" id="chatPanel" role="dialog" aria-modal="false" aria-label="Connekt chat" aria-hidden="true">
  <header class="chat-head">
    <span class="chat-avatar"><?= logo_svg('chat-logo') ?><i class="online"></i></span>
    <div class="chat-title">
      <strong>Connekt</strong>
      <span><i class="dot-on"></i> Online · replies instantly</span>
    </div>
    <button class="chat-x" id="chatClose" aria-label="Close chat"><?= icon('x', '', 22) ?></button>
  </header>
  <div class="chat-body" id="chatBody" aria-live="polite"></div>
  <div class="chat-chips" id="chatChips">
    <button type="button" data-q="services">Services</button>
    <button type="button" data-q="pricing">Pricing</button>
    <button type="button" data-q="our work clients">Our Work</button>
    <button type="button" data-q="careers hiring">Careers</button>
    <button type="button" data-q="contact">Contact</button>
    <button type="button" data-wa="1">Talk on WhatsApp</button>
  </div>
  <form class="chat-input" id="chatForm" autocomplete="off">
    <label class="sr-only" for="chatText">Type your message</label>
    <input id="chatText" type="text" maxlength="300" placeholder="Type your question…" enterkeyhint="send">
    <button type="submit" aria-label="Send"><?= icon('send', '', 20) ?></button>
  </form>
</section>
