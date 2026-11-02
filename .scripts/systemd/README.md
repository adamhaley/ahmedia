# Production queue worker (one-time, manual)

The queue worker runs contact submission classification and sends the
(queued, delayed) new-lead emails. Without it, both just sit in the `jobs`
table. `deploy.sh` only restarts it (`queue:restart`); it doesn't install it.

Modelled on dreamworldcirque's `.scripts/systemd/`, which runs its own
worker from the same `ahmedia` user. The two don't interfere: each app has
its own database, and its queue and cache (restart signal, unique-job
locks, the `openai` rate limiter) live there.

## Install

```bash
scp .scripts/systemd/ahmedia-queue.service root@ahmedia.ai:/etc/systemd/system/
ssh root@ahmedia.ai "systemctl daemon-reload && systemctl enable --now ahmedia-queue"
ssh root@ahmedia.ai "systemctl status ahmedia-queue --no-pager"
```

Confirm it shows `active (running)`. Logs: `journalctl -u ahmedia-queue -f`.
