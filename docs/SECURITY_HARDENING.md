# Server security hardening – applied and recommended

Summary of security changes applied and optional next steps.

## Applied (this session)

### Application (.env and Laravel)
- **APP_DEBUG=false** – Disables verbose errors in production.
- **LOG_LEVEL=warning** – Reduces log volume and avoids logging sensitive detail at debug level.
- **SESSION_SECURE_COOKIE=true** – Session cookies sent only over HTTPS.
- **.env** – Permissions set to `640` (owner read/write, group read only).
- **storage/app/bigquery-credentials.json** – Permissions set to `640`.
- **storage & bootstrap/cache** – Permissions set to `775` (dirs) / `664` (files) for correct web server access.

### SSH
- **PermitRootLogin prohibit-password** – Root login only with SSH keys, not password.
- **X11Forwarding no** – X11 forwarding disabled.
- **sshd_config** backed up to `/etc/ssh/sshd_config.bak.*` before changes.
- SSH service reloaded.

### System
- **UFW** – Confirmed active; allows 22, 80, 443 only.
- **fail2ban** – Running with `sshd` jail.
- **apt** – Package lists updated; upgrades applied (including PHP 8.4.18). A kernel upgrade (6.8.0-100) is installed but requires a **reboot** to take effect.

---

## Recommended next steps

1. **Reboot** when convenient so the new kernel (6.8.0-100) is loaded:  
   `sudo reboot`
2. **Change any shared or weak passwords** (e.g. sudo, DB, .env secrets) and use strong, unique values.
3. **Rotate secrets** if they were ever pasted or logged (e.g. Mailgun, Stripe, BigQuery key).
4. **Forge / Nginx**: In Laravel Forge, ensure the site uses HTTPS and that security headers (e.g. HSTS, X-Frame-Options) are set in the Nginx template if desired.
5. **Backups**: Confirm Forge/server backups and DB backups run and are retained as required.

---

## Quick checks

- **UFW:** `sudo ufw status`
- **fail2ban:** `sudo fail2ban-client status`
- **Laravel:** `APP_DEBUG` and `LOG_LEVEL` in `.env`; session/cookie settings in `config/session.php`.
