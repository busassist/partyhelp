# Storage Configuration

## DigitalOcean Spaces (Media Uploads)

Venue and admin media uploads (room images, etc.) are stored in DigitalOcean Spaces via the S3-compatible API.

### Configuration

Add these to your `.env` on the server:

```
MEDIA_DISK=spaces

DO_SPACES_KEY=your_access_key
DO_SPACES_SECRET=your_secret_key
DO_SPACES_ENDPOINT=https://syd1.digitaloceanspaces.com
DO_SPACES_REGION=syd1
DO_SPACES_BUCKET=partyhelp-bucket
DO_SPACES_URL=https://partyhelp-bucket.syd1.digitaloceanspaces.com
```

**Security:** Never commit credentials to git. Regenerate the Spaces API key in the DigitalOcean control panel if it was ever exposed.

### Bucket Setup

1. Create a Space in Sydney (syd1) region.
2. In the Space **Settings**, set **File Listing** to **Public** so files are publicly accessible (required for thumbnails and app display).
3. If you get "Access Denied" when loading images, ensure the Space is set to Public: DigitalOcean Dashboard → your Space → Settings → File Listing → Public.
4. Create a **Spaces access key** (API → Spaces access keys). Use the Access Key ID and Secret shown when you create the key—not a DigitalOcean personal token. The key name/label is different from the actual Access Key ID.

### Troubleshooting InvalidAccessKeyId

If you see `InvalidAccessKeyId`, ensure you're using a **Spaces access key** from API → Spaces access keys, not a personal access token. Generate a new key and copy both the Access Key ID and Secret immediately—they are shown only once.

### Fixing Access Denied on Existing Files

If thumbnails show "Access Denied" for files already in Spaces, run:

```bash
php artisan media:fix-visibility
```

This sets public visibility on all media files. If it still fails, the Space must be set to **Public** in the DigitalOcean dashboard (Settings → File Listing → Public).

### Migrating Existing Uploads

If you have media files in `storage/app/public/media/` from before Spaces was configured:

```bash
php artisan media:migrate-to-spaces
```

Dry run (preview only):

```bash
php artisan media:migrate-to-spaces --dry-run
```

### Local Development

For local development without Spaces, set:

```
MEDIA_DISK=public
```

This uses the local `storage/app/public` disk. Run `php artisan storage:link` so URLs work.
