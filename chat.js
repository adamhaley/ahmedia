document.addEventListener('DOMContentLoaded', () => {
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const chatMessages = document.getElementById('chat-messages');
    const chatContainer = document.querySelector('.chat-container');
    const closeButton = document.querySelector('.close-button');
    const reopenButton = document.querySelector('.reopen-chat-button');
    
    // Replace this with your actual webhook URL
    const WEBHOOK_URL = 'https://n8n.ahmedia.ai/webhook/245a2818-056b-4f66-b730-116528e44bf7';

    function createTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator';
        
        // Create three dots
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('div');
            dot.className = 'typing-dot';
            indicator.appendChild(dot);
        }
        
        return indicator;
    }

    function addMessage(message, isUser = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
        messageDiv.textContent = message;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async function sendMessage(message) {
        if (!message.trim()) return;

        // Add user message to chat
        addMessage(message, true);

        // Show typing indicator
        const typingIndicator = createTypingIndicator();
        chatMessages.appendChild(typingIndicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            // Send message to webhook
            const response = await fetch(WEBHOOK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ namespace: localStorage.getItem('namespace'), message: message })
            });

            // Remove typing indicator
            chatMessages.removeChild(typingIndicator);

            if (!response.ok) {
                throw new Error('Failed to send message');
            }else{
                const data = await response.json();
                console.log(data.output);
                //add the response to the chat
                const messageDiv = document.createElement('div');
                messageDiv.className = `message bot-message`;
                messageDiv.textContent = data.output;
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            
            }

            // Clear input after successful send
            chatInput.value = '';
        } catch (error) {
            // Remove typing indicator
            chatMessages.removeChild(typingIndicator);
            console.error('Error sending message:', error);
            addMessage('Failed to send message. Please try again.', false);
        }
    }

    // Handle close button click
    closeButton.addEventListener('click', () => {
        chatContainer.style.display = 'none';
        reopenButton.style.display = 'flex';
    });

    // Handle reopen button click
    reopenButton.addEventListener('click', () => {
        chatContainer.style.display = 'flex';
        reopenButton.style.display = 'none';
    });

    // Handle send button click
    sendButton.addEventListener('click', () => {
        sendMessage(chatInput.value);
    });

    // Handle Enter key press
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage(chatInput.value);
        }
    });
}); 