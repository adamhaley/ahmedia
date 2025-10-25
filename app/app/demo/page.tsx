'use client'

import { useEffect } from 'react'
import gsap from 'gsap'
import FileUpload from '@/components/demo/FileUpload'
import ChatInterface from '@/components/demo/ChatInterface'

export default function Demo() {
  useEffect(() => {
    gsap.from('.demo-header', {
      duration: 0.8,
      opacity: 0,
      y: 30,
      ease: 'power3.out',
      clearProps: 'all'
    })

    gsap.from('.demo-content', {
      duration: 0.8,
      opacity: 0,
      y: 20,
      delay: 0.2,
      ease: 'power3.out',
      stagger: 0.1,
      clearProps: 'all'
    })
  }, [])

  return (
    <div className="min-h-screen py-20 bg-dark-bg">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="demo-header max-w-4xl mx-auto text-center mb-12">
          <h1 className="text-5xl md:text-6xl font-bold text-white mb-6 drop-shadow-lg">
            RAG <span className="text-neon-blue neon-text">Demo</span>
          </h1>
          <p className="text-xl text-white mb-8">
            Experience our Retrieval-Augmented Generation system in action.
            Upload your documents and ask questions about them.
          </p>
        </div>

        {/* How it works */}
        <div className="demo-content max-w-4xl mx-auto mb-12">
          <div className="p-6 border border-neon-blue/40 rounded-lg bg-dark-bg/90 backdrop-blur-sm">
            <h2 className="text-2xl font-bold text-white mb-4">How It Works</h2>
            <div className="grid md:grid-cols-3 gap-6">
              <div className="text-center">
                <div className="w-12 h-12 bg-neon-blue/30 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-neon-blue">
                  <span className="text-neon-blue font-bold text-xl">1</span>
                </div>
                <h3 className="font-bold text-white mb-2">Upload Documents</h3>
                <p className="text-white/80 text-sm">
                  Drag and drop or select files to upload to the system
                </p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-neon-blue/30 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-neon-blue">
                  <span className="text-neon-blue font-bold text-xl">2</span>
                </div>
                <h3 className="font-bold text-white mb-2">AI Processing</h3>
                <p className="text-white/80 text-sm">
                  Documents are analyzed and indexed for intelligent retrieval
                </p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-neon-blue/30 rounded-full flex items-center justify-center mx-auto mb-3 border-2 border-neon-blue">
                  <span className="text-neon-blue font-bold text-xl">3</span>
                </div>
                <h3 className="font-bold text-white mb-2">Ask Questions</h3>
                <p className="text-white/80 text-sm">
                  Chat with your documents using natural language
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* File Upload Section */}
        <div className="demo-content mb-12">
          <FileUpload />
        </div>

        {/* Features */}
        <div className="demo-content max-w-4xl mx-auto">
          <div className="grid md:grid-cols-2 gap-6">
            <div className="p-6 border border-neon-blue/40 rounded-lg bg-dark-bg/80 backdrop-blur-sm">
              <h3 className="text-xl font-bold text-white mb-3">Features</h3>
              <ul className="space-y-2 text-white/90">
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Multi-format document support</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Intelligent context retrieval</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Natural language queries</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Real-time processing</span>
                </li>
              </ul>
            </div>

            <div className="p-6 border border-neon-blue/40 rounded-lg bg-dark-bg/80 backdrop-blur-sm">
              <h3 className="text-xl font-bold text-white mb-3">Use Cases</h3>
              <ul className="space-y-2 text-white/90">
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Knowledge base Q&A</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Document analysis</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Research assistance</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-neon-blue">•</span>
                  <span>Content summarization</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      {/* Chat Interface */}
      <ChatInterface />
    </div>
  )
}
