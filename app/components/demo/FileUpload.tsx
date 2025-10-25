'use client'

import { useState, useRef } from 'react'

export default function FileUpload() {
  const [uploadStatus, setUploadStatus] = useState<string[]>([])
  const [isDragOver, setIsDragOver] = useState(false)
  const fileInputRef = useRef<HTMLInputElement>(null)

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setIsDragOver(true)
  }

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setIsDragOver(false)
  }

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault()
    e.stopPropagation()
    setIsDragOver(false)

    const files = Array.from(e.dataTransfer.files)
    handleFiles(files)
  }

  const handleFileInput = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files)
      handleFiles(files)
    }
  }

  const handleFiles = (files: File[]) => {
    setUploadStatus([])
    files.forEach(uploadFile)
  }

  const uploadFile = async (file: File) => {
    const formData = new FormData()
    formData.append('file', file)

    const namespace = localStorage.getItem('namespace')
    if (namespace) {
      formData.append('namespace', namespace)
    }

    const statusMessage = `Processing ${file.name}...`
    setUploadStatus(prev => [...prev, statusMessage])

    const postUrl = 'https://n8n.ahmedia.ai/webhook/fded596d-4a61-4fd2-90a4-006df43136bf'

    try {
      const response = await fetch(postUrl, {
        method: 'POST',
        body: formData
      })

      if (response.ok) {
        const data = await response.json()
        if (data.namespace) {
          localStorage.setItem('namespace', data.namespace)
        }

        setUploadStatus(prev =>
          prev.map(msg =>
            msg.includes(file.name)
              ? `✓ ${file.name} uploaded successfully!`
              : msg
          )
        )
      } else {
        throw new Error('Upload failed')
      }
    } catch (error) {
      setUploadStatus(prev =>
        prev.map(msg =>
          msg.includes(file.name)
            ? `✗ Failed to upload ${file.name}`
            : msg
        )
      )
      console.error('Error:', error)
    }
  }

  return (
    <div className="w-full max-w-2xl mx-auto">
      <div
        className={`border-2 border-dashed rounded-lg p-8 text-center transition-all duration-300 backdrop-blur-sm ${
          isDragOver
            ? 'border-neon-blue bg-neon-blue/20'
            : 'border-neon-blue/50 bg-dark-bg/80'
        }`}
        onDragOver={handleDragOver}
        onDragLeave={handleDragLeave}
        onDrop={handleDrop}
      >
        <div className="mb-4">
          <svg
            className="mx-auto h-16 w-16 text-neon-blue"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
            />
          </svg>
        </div>

        <p className="text-white text-lg mb-2 font-semibold">Drag & Drop files here</p>
        <p className="text-white/80 mb-4">or</p>

        <label
          htmlFor="file-input"
          className="inline-block px-6 py-3 bg-neon-blue text-dark-bg font-bold rounded-lg cursor-pointer hover:bg-white transition-all duration-300 hover:shadow-[0_0_15px_rgba(0,255,255,0.5)]"
        >
          Choose Files
        </label>
        <input
          ref={fileInputRef}
          id="file-input"
          type="file"
          multiple
          className="hidden"
          onChange={handleFileInput}
        />
      </div>

      {uploadStatus.length > 0 && (
        <div className="mt-6 space-y-2">
          {uploadStatus.map((status, index) => (
            <div
              key={index}
              className={`p-3 rounded-lg backdrop-blur-sm ${
                status.includes('✓')
                  ? 'bg-neon-blue/20 border border-neon-blue text-neon-blue'
                  : status.includes('✗')
                  ? 'bg-red-500/20 border border-red-500 text-red-400'
                  : 'bg-dark-bg/80 border border-neon-blue/40 text-white'
              }`}
            >
              {status}
              {!status.includes('✓') && !status.includes('✗') && (
                <div className="mt-2 h-1 w-full bg-dark-bg/50 rounded overflow-hidden">
                  <div className="h-full bg-gradient-to-r from-neon-blue via-neon-blue/50 to-neon-blue animate-[progress-animation_2s_linear_infinite]" />
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  )
}
