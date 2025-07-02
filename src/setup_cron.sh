#!/bin/bash
CRON_JOB="0 * * * * php $(pwd)/cron.php"

# Prevent duplicate CRON jobs
(crontab -l 2>/dev/null | grep -v 'cron.php'; echo "$CRON_JOB") | crontab -

echo "✅ CRON job added: will run every hour"
