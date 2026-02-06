document.addEventListener('DOMContentLoaded', () => {
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const chatMessages = document.getElementById('chat-messages');
    const chatContainer = document.querySelector('.chat-container');
    const closeButton = document.querySelector('.close-button');
    const reopenButton = document.querySelector('.reopen-chat-button');
    
    // Replace this with your actual webhook URL
    const WEBHOOK_URL = 'https://n8n.ahmedia.ai/webhook/rag_chat';
    const RESTAURANT_WEBHOOK_URL = 'https://n8n.ahmedia.ai/webhook/edfc23ee-48e4-4d8e-a51a-30be223586f7';
    const WEBHOOK_URL_TEST = 'https://n8n.ahmedia.ai/webhook-test/edfc23ee-48e4-4d8e-a51a-30be223586f7';
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
        
        // Parse the message text
        const parsedMessage = message
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\n/g, '<br>');
            
        messageDiv.innerHTML = parsedMessage;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Create a bot message element for streaming updates
    function createBotMessageElement() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message';
        chatMessages.appendChild(messageDiv);
        return messageDiv;
    }

    // Update bot message content with proper escaping
    function updateBotMessage(messageDiv, content) {
        // Escape HTML to prevent XSS, then convert newlines to <br>
        const escaped = content
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
        // Use textContent for the base, then handle line breaks
        messageDiv.textContent = '';
        const lines = escaped.split('\n');
        lines.forEach((line, i) => {
            messageDiv.appendChild(document.createTextNode(line.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>')));
            if (i < lines.length - 1) {
                messageDiv.appendChild(document.createElement('br'));
            }
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async function sendMessage(message) {
        if (!message.trim()) return;

        // Disable input while processing
        chatInput.disabled = true;
        sendButton.disabled = true;

        // Add user message to chat
        addMessage(message, true);

        // Show typing indicator
        const typingIndicator = createTypingIndicator();
        chatMessages.appendChild(typingIndicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            // Send message to webhook
            let namespace = localStorage.getItem('namespace');
            if (!namespace) {
                namespace = '__default__';
            }
            const response = await fetch(WEBHOOK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ namespace: namespace, message: message })
            });

            if (!response.ok) {
                throw new Error('Failed to send message');
            }

            // Remove typing indicator before showing response
            if (typingIndicator && typingIndicator.parentNode) {
                chatMessages.removeChild(typingIndicator);
            }

            // Always try streaming first - n8n returns application/json even for NDJSON streams
            if (response.body) {
                // Handle streaming response
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';
                let fullContent = '';

                // Create message element for streaming updates
                const botMessageDiv = createBotMessageElement();

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    // Handle both newline-delimited and concatenated JSON objects
                    buffer = buffer.replace(/\}\s*\{/g, '}\n{');
                    const lines = buffer.split('\n');
                    buffer = lines.pop() || ''; // Keep incomplete line in buffer

                    for (const line of lines) {
                        if (!line.trim()) continue;

                        try {
                            const parsed = JSON.parse(line);

                            // N8N NDJSON format: {"type":"item","content":"text"}
                            if (parsed.type === 'item' && parsed.content) {
                                fullContent += parsed.content;
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                            // SSE Progress format: {"progress":{"delta":"text"}}
                            else if (parsed.progress?.delta) {
                                fullContent += parsed.progress.delta;
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                            // Direct content field
                            else if (parsed.content) {
                                fullContent += parsed.content;
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                            // Fallback: use output field (original format)
                            else if (parsed.output) {
                                fullContent = parsed.output;
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                        } catch (e) {
                            // Not valid JSON, might be raw text chunk
                            fullContent += line;
                            updateBotMessage(botMessageDiv, fullContent);
                        }
                    }
                }

                // Process any remaining buffer
                if (buffer.trim()) {
                    // Handle multiple concatenated JSON objects in remaining buffer
                    const remainingLines = buffer.replace(/\}\s*\{/g, '}\n{').split('\n');
                    for (const line of remainingLines) {
                        if (!line.trim()) continue;
                        try {
                            const parsed = JSON.parse(line);
                            if (parsed.type === 'item' && parsed.content) {
                                fullContent += parsed.content;
                            } else if (parsed.progress?.delta) {
                                fullContent += parsed.progress.delta;
                            } else if (parsed.content) {
                                fullContent += parsed.content;
                            } else if (parsed.output) {
                                fullContent = parsed.output;
                            }
                        } catch (e) {
                            fullContent += line;
                        }
                    }
                    updateBotMessage(botMessageDiv, fullContent);
                }

                // If no content was received, show a fallback message
                if (!fullContent.trim()) {
                    updateBotMessage(botMessageDiv, 'No response received.');
                }
                console.log('Streaming complete:', fullContent);
            } else {
                // Fallback to non-streaming JSON response
                const data = await response.json();
                console.log(data.output);
                addMessage(data.output, false);
            }

            // Clear input after successful send
            chatInput.value = '';
        } catch (error) {
            // Remove typing indicator if it exists
            if (typingIndicator && typingIndicator.parentNode) {
                chatMessages.removeChild(typingIndicator);
            }

            console.error('Error sending message:', error);
            addMessage('Failed to send message. Please try again.', false);
        } finally {
            // Re-enable input
            chatInput.disabled = false;
            sendButton.disabled = false;
            chatInput.focus();
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
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(chatInput.value);
        }
    });
}); 
