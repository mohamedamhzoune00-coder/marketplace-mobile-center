# Marketplace Mobile Center — API

Backend Laravel 8 API لمنصة Marketplace خاصة بمركز تجاري متخصص في الهواتف والإكسسوارات وخدمات الإصلاح بمكناس.

## 🚀 التثبيت

```bash
git clone https://github.com/mohamedamhzoune00-coder/marketplace-mobile-center.git
cd marketplace-mobile-center
composer install
cp .env.example .env
php artisan key:generate
```

## ⚙️ متغيرات البيئة (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=marketplace_mobile_center
DB_USERNAME=root
DB_PASSWORD=

ADMIN_SEED_PASSWORD=UnMotDePasseFort123!@#
```

## 🗄️ قاعدة البيانات

```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan storage:link
```

## ▶️ تشغيل المشروع

```bash
php artisan serve
```

## 🧪 الاختبارات

```bash
php artisan test
```

## 👥 الأدوار

| الدور | الصلاحيات |
|---|---|
| `super_admin` | إدارة كاملة للمنصة |
| `vendeur` | يملك بوتيك واحدة، يدير منتجاتها وطلباتها |
| `visiteur` | يتصفح، يرسل طلبات شراء وبلاغات |

## 🔑 المصادقة (Sanctum)

| Endpoint | Method | وصف |
|---|---|---|
| `/api/register` | POST | تسجيل حساب جديد (role=visiteur افتراضياً) |
| `/api/login` | POST | تسجيل الدخول |
| `/api/logout` | POST | تسجيل الخروج (محمي) |
| `/api/user` | GET | بيانات المستخدم الحالي (محمي) |

## 📦 Endpoints الرئيسية

### Boutiques
- `GET /api/boutiques` — عام
- `GET /api/boutiques/{id}` — عام
- `POST /api/boutiques` — محمي (vendeur/super_admin)
- `PUT /api/boutiques/{id}` — محمي (المالك فقط)
- `DELETE /api/boutiques/{id}` — محمي (super_admin فقط)

### Produits
- `GET /api/produits` — عام
- `POST /api/produits` — محمي (vendeur، boutique_id مشتق تلقائياً)

### Demandes (طلبات الشراء)
- `POST /api/demandes` — محمي (visiteur فقط)
- `PATCH /api/demandes/{id}/accepter` — محمي (vendeur صاحب المتجر/admin)
- `PATCH /api/demandes/{id}/refuser` — محمي (vendeur صاحب المتجر/admin)

## 📄 مثال Request/Response

**POST /api/demandes**
```json
{
  "produit_id": 1,
  "nom_client": "Ahmed",
  "telephone": "0612345678",
  "quantite": 2
}
```

**Response (201):**
```json
{
  "message": "Demande créée avec succès",
  "data": {
    "id": 1,
    "nom_client": "Ahmed",
    "statut": "en_attente",
    "produit": { "id": 1, "nom": "iPhone 13", "prix": "4500.00" }
  }
}
```