-- Telegram multimodal capture: session + item storage for the n8n workflow.
-- See wiki plan: AH Media / Plans / Telegram multimodal capture in n8n

CREATE TABLE capture_sessions (
    session_id    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    chat_id       BIGINT NOT NULL,
    user_id       BIGINT NOT NULL,
    status        TEXT NOT NULL DEFAULT 'collecting'
                  CHECK (status IN ('collecting', 'complete', 'processing', 'processed', 'cancelled')),
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),
    last_activity TIMESTAMPTZ NOT NULL DEFAULT now(),
    completed_at  TIMESTAMPTZ
);

-- One active (collecting) session per chat_id + user_id.
CREATE UNIQUE INDEX capture_sessions_active_idx
    ON capture_sessions (chat_id, user_id)
    WHERE status = 'collecting';

CREATE TABLE capture_items (
    id                   BIGSERIAL PRIMARY KEY,
    session_id           UUID NOT NULL REFERENCES capture_sessions (session_id) ON DELETE CASCADE,
    chat_id              BIGINT NOT NULL,
    telegram_message_id  BIGINT NOT NULL,
    type                 TEXT NOT NULL
                         CHECK (type IN ('photo', 'voice', 'location', 'text')),
    file_id              TEXT,
    latitude             DOUBLE PRECISION,
    longitude            DOUBLE PRECISION,
    text                 TEXT,
    caption              TEXT,
    created_at           TIMESTAMPTZ NOT NULL DEFAULT now(),

    UNIQUE (chat_id, telegram_message_id)
);

CREATE INDEX capture_items_session_idx ON capture_items (session_id);
