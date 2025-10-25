'use client'

import { useState, useRef, useEffect } from 'react'

interface Message {
  text: string
  isUser: boolean
}

export default function ChatInterface() {
  const [messages, setMessages] = useState<Message[]>([])
  const [inputValue, setInputValue] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const [isOpen, setIsOpen] = useState(false)
  const messagesEndRef = useRef<HTMLDivElement>(null)

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' })
  }

  useEffect(() => {
    scrollToBottom()
  }, [messages])

  const sendMessage = async (message: string) => {
    if (!message.trim() || isLoading) return

    const userMessage: Message = { text: message, isUser: true }
    setMessages(prev => [...prev, userMessage])
    setInputValue('')
    setIsLoading(true)

    try {
      let namespace = localStorage.getItem('namespace')
      if (!namespace) {
        namespace = '__default__'
      }

      const response = await fetch(
        'https://n8n.ahmedia.ai/webhook/245a2818-056b-4f66-b730-116528e44bf7',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ namespace, message })
        }
      )

      if (!response.ok) {
        throw new Error('Failed to send message')
      }

      const data = await response.json()
      const botMessage: Message = { text: data.output, isUser: false }
      setMessages(prev => [...prev, botMessage])
    } catch (error) {
      console.error('Error sending message:', error)
      const errorMessage: Message = {
        text: 'Failed to send message. Please try again.',
        isUser: false
      }
      setMessages(prev => [...prev, errorMessage])
    } finally {
      setIsLoading(false)
    }
  }

  const handleKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      sendMessage(inputValue)
    }
  }

  return (
    <>
      {/* Chat Container */}
      <div
        className={`fixed bottom-5 right-5 w-[350px] h-[500px] bg-dark-bg border border-neon-blue rounded-lg shadow-[0_0_20px_rgba(0,255,255,0.2)] flex flex-col overflow-hidden z-50 transition-all duration-300 ${
          isOpen ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0 pointer-events-none'
        }`}
      >
        {/* Header */}
        <div className="p-4 border-b border-neon-blue/20 flex items-center justify-between bg-dark-bg/80">
          <h3 className="text-white font-bold">Chat Assistant</h3>
          <button
            onClick={() => setIsOpen(false)}
            className="text-neon-blue hover:text-white transition-colors"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {/* Messages */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-black/20">
          {messages.length === 0 && (
            <div className="text-center text-white/40 mt-8">
              <p>Start a conversation...</p>
              <p className="text-sm mt-2">Upload files first to ask questions about them</p>
            </div>
          )}

          {messages.map((message, index) => (
            <div
              key={index}
              className={`flex ${message.isUser ? 'justify-end' : 'justify-start'}`}
            >
              <div
                className={`max-w-[80%] p-3 rounded-lg ${
                  message.isUser
                    ? 'bg-neon-blue text-dark-bg shadow-[0_0_10px_rgba(0,255,255,0.3)]'
                    : 'bg-neon-blue/10 text-white border border-neon-blue/30'
                }`}
              >
                {message.text}
              </div>
            </div>
          ))}

          {isLoading && (
            <div className="flex justify-start">
              <div className="flex items-center gap-2 p-3 bg-neon-blue/10 border border-neon-blue/30 rounded-lg">
                <div className="flex gap-1">
                  <div className="w-2 h-2 bg-neon-blue rounded-full animate-[typing-animation_1.4s_infinite]" />
                  <div className="w-2 h-2 bg-neon-blue rounded-full animate-[typing-animation_1.4s_infinite_0.2s]" />
                  <div className="w-2 h-2 bg-neon-blue rounded-full animate-[typing-animation_1.4s_infinite_0.4s]" />
                </div>
              </div>
            </div>
          )}

          <div ref={messagesEndRef} />
        </div>

        {/* Input */}
        <div className="p-4 border-t border-neon-blue/20 bg-black/30">
          <div className="flex gap-2">
            <input
              type="text"
              value={inputValue}
              onChange={(e) => setInputValue(e.target.value)}
              onKeyPress={handleKeyPress}
              placeholder="Type your message..."
              disabled={isLoading}
              className="flex-1 px-4 py-2 bg-black/20 border border-neon-blue/30 rounded-full text-white placeholder-white/40 focus:outline-none focus:border-neon-blue focus:shadow-[0_0_10px_rgba(0,255,255,0.3)]"
            />
            <button
              onClick={() => sendMessage(inputValue)}
              disabled={isLoading || !inputValue.trim()}
              className="w-10 h-10 bg-neon-blue text-dark-bg rounded-full flex items-center justify-center hover:bg-white transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed hover:shadow-[0_0_15px_rgba(0,255,255,0.5)]"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      {/* Reopen Button */}
      <button
        onClick={() => setIsOpen(true)}
        className={`fixed bottom-5 right-5 w-14 h-14 bg-neon-blue text-dark-bg rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(0,255,255,0.3)] hover:scale-110 transition-all duration-300 z-40 ${
          isOpen ? 'opacity-0 pointer-events-none' : 'opacity-100'
        }`}
      >
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </button>
    </>
  )
}
