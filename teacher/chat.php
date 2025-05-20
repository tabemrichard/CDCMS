<div class="chat-widget" id="chatWindow">
    <div class="chat-widget-header">
        <h5 class="chat-widget-title">Support Chat</h5>
        <button class="chat-widget-close" id="closeChat" aria-label="Close"></button>
    </div>
    
    <div class="chat-widget-body" id="chatBody">
        <div class="chat-widget-hidden chat-widget-margin-bottom" id="typingContainer">
            <div class="chat-widget-flex">
                <div class="chat-widget-flex-shrink-0 chat-widget-margin-right">
                    <div class="chat-widget-bot-avatar chat-widget-circle">
                        <img src="../assets/images/chat-profile.svg" alt="Bot" class="chat-widget-img-contain" width="30" height="30">
                    </div>
                </div>
                <div class="chat-widget-typing">
                    <span class="chat-widget-typing-dot"></span>
                    <span class="chat-widget-typing-dot"></span>
                    <span class="chat-widget-typing-dot"></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="chat-widget-footer">
        <div class="chat-widget-input-group">
            <input type="text" class="chat-widget-input" id="userInput" placeholder="Type your message here..." aria-label="Type your message">
            <button class="chat-widget-button chat-widget-send-button" id="sendMessage">
                <span class="chat-widget-send-icon"></span>
            </button>
        </div>
    </div>
</div>