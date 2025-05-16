curl -X POST https://api.cartesia.ai/tts/bytes \
-H "Cartesia-Version: 2024-06-10" \
-H "X-API-Key: $CARTESIA_API_KEY" \
-H "Content-Type: application/json" \
-d '{
  "model_id": "sonic-2",
  "transcript": "Hey guys... This is Adams AI clone... how do I sound? would you mistake me for the real thing?",
  "voice": {
    "mode": "id",
    "id": "ac15038c-c796-4b8f-b006-d63e7aa53e1c"
  },
  "output_format": {
    "container": "wav",
    "encoding": "pcm_f32le",
    "sample_rate": 44100
  },
  "language": "en"
}'
  
