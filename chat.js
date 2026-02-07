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

    // Check if namespace exists and update chat state accordingly
    function checkNamespaceAndUpdateChat() {
        const namespace = localStorage.getItem('namespace');
        if (!namespace) {
            chatInput.disabled = true;
            sendButton.disabled = true;
            chatInput.placeholder = 'Please upload a file first to start chatting...';
            return false;
        } else {
            chatInput.disabled = false;
            sendButton.disabled = false;
            chatInput.placeholder = 'Type your message...';
            return true;
        }
    }

    // Initial check
    checkNamespaceAndUpdateChat();

    // Listen for storage changes (when file upload sets namespace)
    window.addEventListener('storage', checkNamespaceAndUpdateChat);

    // Also check periodically in case storage event doesn't fire (same-tab updates)
    window.addEventListener('namespaceUpdated', checkNamespaceAndUpdateChat);
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

        // Check namespace before sending
        const namespace = localStorage.getItem('namespace');
        if (!namespace) {
            addMessage('Please upload a file first to start chatting.', false);
            return;
        }

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

            // Always try streaming first - n8n returns application/json even for NDJSON streams
            if (response.body) {
                // Handle streaming response
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';
                let fullContent = '';
                let firstChunkReceived = false;

                // Create message element for streaming updates (hidden until first content)
                const botMessageDiv = createBotMessageElement();
                botMessageDiv.style.display = 'none';

                function showFirstContent() {
                    if (!firstChunkReceived) {
                        firstChunkReceived = true;
                        if (typingIndicator && typingIndicator.parentNode) {
                            chatMessages.removeChild(typingIndicator);
                        }
                        botMessageDiv.style.display = '';
                    }
                }

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
                                showFirstContent();
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                            // SSE Progress format: {"progress":{"delta":"text"}}
                            else if (parsed.progress?.delta) {
                                fullContent += parsed.progress.delta;
                                showFirstContent();
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                            // Direct content field (but not if it's an output summary)
                            else if (parsed.content && !parsed.output) {
                                fullContent += parsed.content;
                                showFirstContent();
                                updateBotMessage(botMessageDiv, fullContent);
                            }
                            // Ignore "output" field - it's a summary of streamed content
                            // Ignore "begin" and other metadata types
                        } catch (e) {
                            // Not valid JSON - only append if it doesn't look like JSON or output summary
                            const trimmed = line.trim();
                            if (!trimmed.startsWith('{') && !trimmed.includes('"output"')) {
                                fullContent += line;
                                showFirstContent();
                                updateBotMessage(botMessageDiv, fullContent);
                            }
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
                            } else if (parsed.content && !parsed.output) {
                                fullContent += parsed.content;
                            }
                            // Ignore "output" field - it's a summary
                        } catch (e) {
                            // Only append non-JSON looking text
                            const trimmed = line.trim();
                            if (!trimmed.startsWith('{') && !trimmed.includes('"output"')) {
                                fullContent += line;
                            }
                        }
                    }
                    showFirstContent();
                    updateBotMessage(botMessageDiv, fullContent);
                }

                // Clean up any output summary JSON that leaked into the content
                const outputIndex = fullContent.indexOf('{"output"');
                if (outputIndex > 0) {
                    fullContent = fullContent.substring(0, outputIndex).trim();
                }

                // Final update with cleaned content
                showFirstContent();
                if (fullContent.trim()) {
                    updateBotMessage(botMessageDiv, fullContent);
                } else {
                    updateBotMessage(botMessageDiv, 'No response received.');
                }
            } else {
                // Fallback to non-streaming JSON response
                if (typingIndicator && typingIndicator.parentNode) {
                    chatMessages.removeChild(typingIndicator);
                }
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
