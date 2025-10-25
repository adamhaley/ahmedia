'use client'

import { useEffect } from 'react'
import gsap from 'gsap'
import FileUpload from '@/components/demo/FileUpload'
import ChatInterface from '@/components/demo/ChatInterface'

export default function Demo() {
  useEffect(() => {
    gsap.from('.demo-header', {
      duration: 1,
      opacity: 0,
      y: 50,
      ease: 'power3.out'
    })

    gsap.from('.demo-content', {
      duration: 1,
      opacity: 0,
      y: 30,
      delay: 0.3,
      ease: 'power3.out'
    })
  }, [])

  return (
    <div className="min-h-screen py-20">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="demo-header max-w-4xl mx-auto text-center mb-12">
          <h1 className="text-5xl md:text-6xl font-bold text-white mb-6">
            RAG <span className="text-neon-blue neon-text">Demo</span>
          </h1>
          <p className="text-xl text-white/70 mb-8">
            Experience our Retrieval-Augmented Generation system in action.
            Upload your documents and ask questions about them.
          </p>
        </div>

        {/* How it works */}
        <div className="demo-content max-w-4xl mx-auto mb-12">
          <div className="p-6 border border-neon-blue/30 rounded-lg bg-dark-bg/30">
            <h2 className="text-2xl font-bold text-white mb-4">How It Works</h2>
            <div className="grid md:grid-cols-3 gap-6">
              <div className="text-center">
                <div className="w-12 h-12 bg-neon-blue/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-neon-blue/50">
                  <span className="text-neon-blue font-bold text-xl">1</span>
                </div>
                <h3 className="font-bold text-white mb-2">Upload Documents</h3>
                <p className="text-white/60 text-sm">
                  Drag and drop or select files to upload to the system
                </p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-neon-blue/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-neon-blue/50">
                  <span className="text-neon-blue font-bold text-xl">2</span>
                </div>
                <h3 className="font-bold text-white mb-2">AI Processing</h3>
                <p className="text-white/60 text-sm">
                  Documents are analyzed and indexed for intelligent retrieval
                </p>
              </div>
              <div className="text-center">
                <div className="w-12 h-12 bg-neon-blue/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-neon-blue/50">
                  <span className="text-neon-blue font-bold text-xl">3</span>
                </div>
                <h3 className="font-bold text-white mb-2">Ask Questions</h3>
                <p className="text-white/60 text-sm">
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
            <div className="p-6 border border-neon-blue/20 rounded-lg bg-dark-bg/20">
              <h3 className="text-xl font-bold text-white mb-3">Features</h3>
              <ul className="space-y-2 text-white/70">
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

            <div className="p-6 border border-neon-blue/20 rounded-lg bg-dark-bg/20">
              <h3 className="text-xl font-bold text-white mb-3">Use Cases</h3>
              <ul className="space-y-2 text-white/70">
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
