<div id="obscura-help" class="obscura-help">
  <button id="obscura-chat-toggle" class="obscura-chat-toggle" type="button" aria-expanded="false" aria-controls="obscura-chat-panel">Help 💬</button>
  <section id="obscura-chat-panel" class="obscura-chat-panel" aria-label="Obscura help chatbot" hidden>
    <div class="obscura-chat-header">Obscura Help <button type="button" id="obscura-chat-close" aria-label="Close chat">×</button></div>
    <div id="obscura-chat-messages" class="obscura-chat-messages" role="log" aria-live="polite"><p class="bot-msg">Hi! Ask me about Obscura's features.</p></div>
    <div class="obscura-chat-suggestions">
      <button type="button" data-question="How do I log in?">Login</button>
      <button type="button" data-question="Where is my profile?">Profile</button>
      <button type="button" data-question="How do I log out?">Logout</button>
    </div>
    <form id="obscura-chat-form" class="obscura-chat-form">
      <input id="obscura-chat-input" type="text" maxlength="500" autocomplete="off" placeholder="Ask about Obscura..." aria-label="Your question" required>
      <button type="submit">Send</button>
    </form>
  </section>
</div>
<link rel="stylesheet" href="chatbot.css">
<script src="chatbot.js" defer></script>
