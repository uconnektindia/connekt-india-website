<?php /** Floating WhatsApp button: rendered early in <body> so it shows instantly, above the preloader */ ?>
<a class="fab fab-wa" id="fabWa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
  <span class="fab-pulse"></span>
  <?= icon('whatsapp', 'fab-ico', 28) ?>
  <span class="fab-tip">Chat with us</span>
</a>
