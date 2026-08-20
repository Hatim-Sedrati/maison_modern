# Maison Modern

Moroccan fashion e-commerce storefront built with Laravel, Livewire, Tailwind CSS, and Filament.

Customers shop as guests. Only the administrator authenticates. Payment is Cash on Delivery. WhatsApp is a confirmation channel, not the order database.

## Requirements

- PHP 8.4 (8.3+ supported by Composer)
- Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`
- Composer 2.9+
- Node.js 22+ and npm 10+
- MySQL 8.0+

Queues are not required. `QUEUE_CONNECTION=sync` is the expected production setting.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set MySQL credentials in `.env`, then:

```bash
php artisan migrate
php artisan storage:link
npm install
npm run build
php artisan make:filament-user
php artisan serve
```

Admin: `/admin`  
Storefront: `/`

Do not seed fake catalog data. Create categories and products in Filament.

## Environment variables

Required:

| Variable | Notes |
|---|---|
| `APP_NAME` | `Maison Modern` |
| `APP_ENV` | `local` or `production` |
| `APP_KEY` | `php artisan key:generate` |
| `APP_DEBUG` | `true` locally only. **Must be `false` in production.** |
| `APP_URL` | Public URL, `https://your-domain.com` in production |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Database host |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |

Shop:

| Variable | Notes |
|---|---|
| `SHOP_DELIVERY_FEE` | Server-side delivery fee in MAD, e.g. `30.00` |
| `WHATSAPP_NUMBER` | International number, e.g. `212612345678`. Leave empty to hide the WhatsApp CTA. |

Cloudinary (optional locally; recommended in production):

| Variable | Notes |
|---|---|
| `CLOUDINARY_URL` | Optional `cloudinary://` URL |
| `CLOUDINARY_CLOUD_NAME` | Cloud name |
| `CLOUDINARY_API_KEY` | API key |
| `CLOUDINARY_API_SECRET` | API secret — never commit this |
| `CLOUDINARY_SECURE` | `true` |

Never commit `.env` or real credentials.

## Production settings

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
LOG_LEVEL=info
QUEUE_CONNECTION=sync
```

Point the web server document root at `public/`.

After deploy:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you change `.env`, run `php artisan config:clear` before recaching.

## Storage and images

- `php artisan storage:link` is required for locally stored images.
- When Cloudinary credentials are present, product images upload to Cloudinary and local temp files are removed.
- Without Cloudinary credentials, images stay on the `public` disk.

## WhatsApp

After checkout, customers can confirm the order in WhatsApp when `WHATSAPP_NUMBER` is set. The message includes brand, order number, name, totals, items, and Cash on Delivery. It does not include address, phone, or email.

## Security

- Guests cannot access `/admin`.
- There is no customer login or registration.
- Prices, stock, and totals are calculated on the server.
- Order confirmation URLs are signed.
- Historical order items keep product name, price, quantity, and variant snapshots.

## Tests

```bash
php artisan test
npm run build
```

## Production checklist

Application:

- [ ] Laravel boots
- [ ] Database connects
- [ ] Migrations work
- [ ] Vite build works
- [ ] `APP_DEBUG=false`

Storefront:

- [ ] Homepage
- [ ] Categories
- [ ] Product pages
- [ ] Search
- [ ] Cart
- [ ] Checkout
- [ ] Confirmation
- [ ] 404
- [ ] Mobile

Catalog:

- [ ] Create product
- [ ] Edit product
- [ ] Images
- [ ] Variants
- [ ] Stock
- [ ] Categories

Orders:

- [ ] Order creation
- [ ] Cash on Delivery
- [ ] Stock decrement
- [ ] Status updates
- [ ] Historical snapshots

Integrations:

- [ ] Cloudinary
- [ ] WhatsApp

Security:

- [ ] Admin protected
- [ ] Customer authentication absent
- [ ] Secrets not committed
- [ ] Validation active
