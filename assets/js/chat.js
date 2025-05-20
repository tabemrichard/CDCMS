document.addEventListener('DOMContentLoaded', async function() {
    const chatToggle = document.getElementById('chatToggle');
    const closeChat = document.getElementById('closeChat');
    const chatWindow = document.getElementById('chatWindow');
    const sendButton = document.getElementById('sendMessage');
    const userInput = document.getElementById('userInput');
    const chatBody = document.getElementById('chatBody');
    const typingContainer = document.getElementById('typingContainer');
    
    const botImageUrl = '../assets/images/chat-profile.svg';
    
    // Start Fetching responses
    const responsesURL = '../assets/js/responses.json';
    let responses;

    try {
        const response = await fetch(responsesURL);
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        responses = await response.json();
    } catch (error) {
        console.error("Error fetching JSON:", error);
    }
    // End fetching
    
    // Toggle (open/close) chat window when the chat button clicked
    chatToggle.addEventListener('click', function() {
        chatWindow.classList.toggle('chat-widget-active');
    });
    
    // Close chat window when the chat button 'x' is clicked
    closeChat.addEventListener('click', function() {
        chatWindow.classList.remove('chat-widget-active');
    });
    
    function sendMessage() {
        const message = userInput.value.trim();
        if (message === '') return;
        
        addUserMessage(message);
        userInput.value = '';
        botReply(message);
    }

    function botReply(message) {
        typingContainer.classList.remove('chat-widget-hidden');
        typingContainer.classList.add('chat-widget-visible');
        chatBody.scrollTop = chatBody.scrollHeight;
        
        setTimeout(function() {
            typingContainer.classList.remove('chat-widget-visible');
            typingContainer.classList.add('chat-widget-hidden');
            
            let key = message.toLowerCase();
            let response = '';
            
            if (responses[key]) {
                response = responses[key];
            } else {
                for (const [k, v] of Object.entries(responses)) {
                    if (key.includes(k) && k !== 'default') {
                        response = v;
                        break;
                    }
                }
                
                if (response === '') {
                    response = responses['default'];
                }
            }
            
            addBotMessage(response);
        }, 1000);
    }

    function addUserMessage(message) {
        const messageContainer = document.createElement("div");
        messageContainer.className = "chat-widget-flex chat-widget-flex-end chat-widget-margin-bottom";

        const messageBubble = document.createElement("div");
        messageBubble.className = "chat-widget-user-bubble chat-widget-text-break";
        messageBubble.textContent = message; // Completely safe, no HTML injection

        messageContainer.appendChild(messageBubble);
        chatBody.appendChild(messageContainer);
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    
    function addBotMessage(message) {
        const messageHTML = `
            <div class="chat-widget-flex chat-widget-margin-bottom">
                <div class="chat-widget-flex-shrink-0 chat-widget-margin-right">
                    <div class="chat-widget-bot-avatar chat-widget-circle">
                        <img src="${botImageUrl}" alt="Bot" class="chat-widget-img-contain" width="30" height="30">
                    </div>
                </div>
                <div class="chat-widget-bot-bubble chat-widget-text-break">
                    ${message}
                </div>
            </div>
        `;
        
        chatBody.insertAdjacentHTML('beforeend', messageHTML);
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    
    sendButton.addEventListener('click', sendMessage);
    
    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    if (responses && responses['hello']) {
        addBotMessage(responses['hello']);
    }

    // Close chat when click outside
    document.addEventListener('click', event => {
        const target = event.target.closest('#chatWindow');
        const target2 = event.target.closest('#chatToggle');
        if (!target && !target2) {
            chatWindow.classList.remove('chat-widget-active');
        }
    });
});
