<p align="center">
  <h1 align="center">💰 Finance Data Processing and Access Control System</h1>
  <p align="center">
    A full-stack finance management and access control system built with Laravel 13, Vue 3, Inertia.js, and Tailwind CSS.
    <br />
    Features role-based access control, two-factor authentication, transaction tracking, analytics dashboards, and a complete REST API.
  </p>
  <p align="center">
    <a href="#-quick-start"><img src="https://img.shields.io/badge/Quick_Start-blue?style=for-the-badge" alt="Quick Start" /></a>
    <a href="https://petstore.swagger.io/?url=https://raw.githubusercontent.com/parbati-19/finance_admin_dashboard/main/openapi.yaml"><img src="https://img.shields.io/badge/API_Docs-Swagger-85EA2D?style=for-the-badge&logo=swagger&logoColor=black" alt="API Docs" /></a>
  </p>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vuedotjs&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/Inertia.js-3-9553E9?style=flat-square&logo=inertia&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-8-646CFF?style=flat-square&logo=vite&logoColor=white" />
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" />
</p>

---

## 📑 Table of Contents

- [Tech Stack](#-tech-stack)
- [Features](#-features)
- [Quick Start](#-quick-start)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Authentication](#-authentication)
- [Authorization](#-authorization)
- [API Reference](#-api-reference)
  - [API Documentation (Postman / Swagger)](#-api-documentation--testing)
- [Database Schema](#-database-schema)
- [Testing](#-testing)
- [Code Quality](#-code-quality)
- [License](#-license)

---

## 🛠 Tech Stack

<table>
  <tr>
    <td><strong>Backend</strong></td>
    <td><img src="https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white" /> <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white" /></td>
  </tr>
  <tr>
    <td><strong>Frontend</strong></td>
    <td><img src="https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vuedotjs&logoColor=white" /> <img src="https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript&logoColor=white" /> <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white" /></td>
  </tr>
  <tr>
    <td><strong>SPA Bridge</strong></td>
    <td><img src="https://img.shields.io/badge/Inertia.js-3-9553E9?logo=inertia&logoColor=white" /></td>
  </tr>
  <tr>
    <td><strong>UI Components</strong></td>
    <td>shadcn-vue (New York v4 style) · Lucide Icons · TanStack Table</td>
  </tr>
  <tr>
    <td><strong>Auth</strong></td>
    <td><img src="https://img.shields.io/badge/Fortify-Web-FF2D20?logo=laravel&logoColor=white" /> <img src="https://img.shields.io/badge/Sanctum-API-FF2D20?logo=laravel&logoColor=white" /></td>
  </tr>
  <tr>
    <td><strong>Authorization</strong></td>
    <td><img src="https://img.shields.io/badge/Spatie-Permission-2596BE" /></td>
  </tr>
  <tr>
    <td><strong>Build & DX</strong></td>
    <td><img src="https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white" /> · Wayfinder · Pest 4 · ESLint · Prettier · Pint</td>
  </tr>
</table>

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 📊 Core Modules
- **Dashboard** — Summary cards, income/expense trends, interactive charts (daily/weekly/monthly), top expenses, recent transactions
- **Transactions** — Full CRUD with soft deletes, filterable by type, category, and date range
- **Analytics** — Period-based analytics (weekly, monthly, yearly) with income vs expense charts
- **User Management** — Full CRUD with soft deletes, restore, permanent delete, roles & permissions

</td>
<td width="50%">

### 🎨 UI/UX
- Responsive layout with collapsible sidebar
- 🌗 Light / Dark / System theme
- Toast notifications & breadcrumbs
- Settings pages (Profile, Security, Appearance)

### 🔐 Security
- Two-factor authentication (TOTP + recovery codes)
- Email verification & password reset
- Rate-limited login
- API token auth via Sanctum

</td>
</tr>
</table>

### 🛡️ Authorization

- Role-based access control (**RBAC**) with Spatie Laravel Permission
- Pre-configured roles: `Admin` · `Analyst` · `Visitor`
- Granular permissions: `view dashboard` · `view transactions` · `view users` · `view roles` · `view permissions`
- Role & permission management UI with sync capabilities
- Runtime role switching

---

## 🚀 Quick Start

### Prerequisites

> **PHP** >= 8.3 · **Composer** >= 2.x · **Node.js** >= 20.x · **npm** >= 10.x · **MySQL** / PostgreSQL / SQLite

### One-Command Setup

```bash
git clone https://github.com/parbati-19/finance_admin_dashboard.git
cd finance_admin_dashboard
composer setup
```

<details>
<summary>📋 <code>composer setup</code> runs the following automatically</summary>

1. `composer install` — installs PHP dependencies
2. Copies `.env.example` → `.env`
3. `php artisan key:generate` — generates the application key
4. `php artisan migrate` — runs database migrations
5. `npm install` — installs frontend dependencies
6. `npm run build` — compiles frontend assets

</details>

<details>
<summary>🔧 Manual setup (step-by-step)</summary>

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
```

</details>

### 🌱 Seed Data

| Role | Permissions | Description |
| --- | --- | --- |
| 🔴 **Admin** | All permissions | Full system access |
| 🟡 **Analyst** | `view transactions` | Transaction data access |
| 🟢 **Visitor** | `view dashboard` | Dashboard-only access |

> **Default login:** `test@example.com` / `password` (Admin role)

---

## 🎮 Usage

```bash
composer dev    # starts Laravel + Queue + Vite concurrently
```

| Service | URL |
| --- | --- |
| Laravel server | `http://localhost:8000` |
| Vite HMR | `http://localhost:5173` |
| Queue worker | Background |

Production build: `npm run build`

---

## 📁 Project Structure

<details>
<summary>Click to expand</summary>

```
├── app/
│   ├── Actions/Fortify/          # Auth actions (create user, reset password)
│   ├── Http/
│   │   ├── Controllers/          # Web controllers (Inertia responses)
│   │   │   ├── Api/              # API controllers (JSON responses)
│   │   │   └── Settings/         # User settings controllers
│   │   ├── Middleware/            # HandleInertiaRequests, HandleAppearance
│   │   ├── Requests/             # Form request validation classes
│   │   └── Resources/            # API resource transformers
│   ├── Models/
│   │   ├── Enums/                # TransactionType, UserStatus, UserType
│   │   ├── User.php
│   │   ├── UserDetail.php
│   │   └── TransactionRecord.php
│   └── Providers/
├── resources/
│   ├── js/
│   │   ├── components/           # Reusable Vue components + shadcn-vue UI
│   │   ├── composables/          # Vue composables (useToast, useTwoFactorAuth, etc.)
│   │   ├── layouts/              # App, Auth, and Settings layouts
│   │   ├── pages/                # Vue page components
│   │   └── types/                # TypeScript type definitions
│   └── css/
├── routes/
│   ├── web.php                   # Web routes (Inertia)
│   ├── api.php                   # API routes (Sanctum)
│   ├── settings.php              # Settings routes
│   └── console.php               # Artisan commands
├── database/
│   ├── migrations/               # 10 migration files
│   ├── factories/
│   └── seeders/
├── openapi.yaml                  # OpenAPI 3.0 specification
└── tests/
    ├── Feature/
    └── Unit/
```

</details>

---

## 🔑 Authentication

### Web Authentication (Fortify)

All web auth is handled by Laravel Fortify with Inertia-rendered Vue pages:

| Feature              | Route                         |
| -------------------- | ----------------------------- |
| Login                | `GET /login`                  |
| Register             | `GET /register`               |
| Forgot Password      | `GET /forgot-password`        |
| Reset Password       | `GET /reset-password/{token}` |
| Email Verification   | `GET /email/verify`           |
| Two-Factor Challenge | `GET /two-factor-challenge`   |
| Confirm Password     | `GET /user/confirm-password`  |

### API Authentication (Sanctum)

Token-based authentication for external API consumers:

```bash
# Login and get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password"}'

# Use token in subsequent requests
curl http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer {token}"

# Logout (revoke token)
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {token}"
```

---

## 🛡️ Authorization

### Roles & Permissions

Permissions are enforced at the route level via middleware and can be managed through the UI or API.

| Permission          | Description                     | Roles          |
| ------------------- | ------------------------------- | -------------- |
| `view dashboard`    | Access dashboard                | Admin, Visitor |
| `view transactions` | Access transactions & analytics | Admin, Analyst |
| `view users`        | Access user management          | Admin          |
| `view roles`        | Access role management          | Admin          |
| `view permissions`  | Access permission management    | Admin          |

### Sidebar Navigation

Navigation items are dynamically shown/hidden based on the authenticated user's permissions.

---

## 📡 API Reference

All API routes are prefixed with `/api` and return JSON responses.

> **📖 [View Interactive API Documentation (Swagger UI)](https://petstore.swagger.io/?url=https://raw.githubusercontent.com/parbati-19/finance_admin_dashboard/main/openapi.yaml)** — browse all 34 endpoints live, no setup required.

### Public Endpoints

| Method | Endpoint        | Description                             |
| ------ | --------------- | --------------------------------------- |
| `POST` | `/api/login`    | Authenticate and receive a bearer token |
| `POST` | `/api/register` | Register a new user account             |

### Protected Endpoints (require `Authorization: Bearer {token}`)

#### Auth

| Method | Endpoint      | Description                    |
| ------ | ------------- | ------------------------------ |
| `GET`  | `/api/user`   | Get authenticated user profile |
| `POST` | `/api/logout` | Revoke current token           |

#### Dashboard (`view dashboard` permission)

| Method | Endpoint         | Description                                                      |
| ------ | ---------------- | ---------------------------------------------------------------- |
| `GET`  | `/api/dashboard` | Financial summary, chart data, top expenses, recent transactions |

#### Transactions (`view transactions` permission)

| Method   | Endpoint                              | Description                   |
| -------- | ------------------------------------- | ----------------------------- |
| `GET`    | `/api/transaction-records`            | List transactions (paginated) |
| `POST`   | `/api/transaction-records`            | Create a transaction          |
| `GET`    | `/api/transaction-records/{id}`       | Get transaction details       |
| `PUT`    | `/api/transaction-records/{id}`       | Update a transaction          |
| `DELETE` | `/api/transaction-records/{id}`       | Delete a transaction          |
| `GET`    | `/api/transaction-records-categories` | List available categories     |
| `GET`    | `/api/transaction-analytics`          | Period-based analytics        |

#### Users (`view users` permission)

| Method   | Endpoint                       | Description                        |
| -------- | ------------------------------ | ---------------------------------- |
| `GET`    | `/api/users`                   | List users (paginated, filterable) |
| `POST`   | `/api/users`                   | Create a user                      |
| `GET`    | `/api/users/{id}`              | Get user details                   |
| `PUT`    | `/api/users/{id}`              | Update a user                      |
| `DELETE` | `/api/users/{id}`              | Soft-delete a user                 |
| `PATCH`  | `/api/users/{id}/restore`      | Restore a soft-deleted user        |
| `DELETE` | `/api/users/{id}/force-delete` | Permanently delete a user          |

#### Roles & Permissions (`view users` permission)

| Method   | Endpoint                      | Description                                |
| -------- | ----------------------------- | ------------------------------------------ |
| `GET`    | `/api/roles`                  | List all roles                             |
| `POST`   | `/api/roles`                  | Create a role                              |
| `PUT`    | `/api/roles/{id}`             | Update a role                              |
| `DELETE` | `/api/roles/{id}`             | Delete a role (fails if assigned to users) |
| `PUT`    | `/api/roles/{id}/permissions` | Sync permissions to a role                 |
| `GET`    | `/api/permissions`            | List all permissions                       |
| `POST`   | `/api/permissions`            | Create a permission                        |
| `PUT`    | `/api/permissions/{id}`       | Update a permission                        |
| `DELETE` | `/api/permissions/{id}`       | Delete a permission                        |

### 📋 API Documentation & Testing

An **OpenAPI 3.0** specification ([`openapi.yaml`](openapi.yaml)) is included in the project root with all 34 endpoints.

<p>
  <a href="https://petstore.swagger.io/?url=https://raw.githubusercontent.com/parbati-19/finance_admin_dashboard/main/openapi.yaml">
    <img src="https://img.shields.io/badge/Swagger_UI-View_API_Docs-85EA2D?style=for-the-badge&logo=swagger&logoColor=black" alt="Swagger UI" />
  </a>
</p>

<details>
<summary>🔧 Import into Postman</summary>

1. Open **Postman** → **Import** (top-left)
2. Drag & drop `openapi.yaml` or click **Upload Files**
3. Set the `baseUrl` variable to `http://localhost:8000`
4. Call `POST /api/login` first, copy the token, and add it as `Authorization: Bearer {token}`

</details>

<details>
<summary>🐳 Run Swagger UI locally (Docker)</summary>

```bash
docker run -p 8080:8080 \
  -e SWAGGER_JSON=/spec/openapi.yaml \
  -v $(pwd)/openapi.yaml:/spec/openapi.yaml \
  swaggerapi/swagger-ui
```

Open `http://localhost:8080` in your browser.

</details>

<details>
<summary>🔒 How to authenticate in Swagger UI</summary>

1. Click the **Authorize** button (🔒) at the top
2. Enter your bearer token (from `POST /api/login`)
3. Click **Authorize** — all subsequent requests will include the token

</details>

---

## 🗄️ Database Schema

### Models

```
User                     UserDetail                 TransactionRecord
├── id                   ├── id                     ├── id
├── name                 ├── user_id (FK)           ├── user_id (FK)
├── email (unique)       ├── user_type (enum)       ├── amount (decimal 12,2)
├── password             ├── company_name           ├── type (enum: Income/Expense)
├── status (0/1)         ├── company_pan            ├── category
├── email_verified_at    ├── contact_person         ├── date
├── two_factor_*         ├── company_address        ├── notes
├── remember_token       ├── company_phone          ├── deleted_at
├── deleted_at           ├── personal_phone         └── timestamps
└── timestamps           ├── company_email
                         ├── is_verified
                         ├── deleted_at
                         └── timestamps
```

### Enums

| Enum              | Values                          |
| ----------------- | ------------------------------- |
| `TransactionType` | `Income (1)`, `Expense (2)`     |
| `UserStatus`      | `Active (1)`, `Inactive (0)`    |
| `UserType`        | `Individual (0)`, `Company (1)` |

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with Composer (includes lint check)
composer test

# Run specific test file
php artisan test --filter=AuthenticationTest
```

### Test Suite

| Category  | Tests                                                                                                |
| --------- | ---------------------------------------------------------------------------------------------------- |
| Auth      | Login, Registration, Email Verification, Password Reset, Password Confirmation, Two-Factor Challenge |
| Dashboard | Access control, data rendering                                                                       |
| Settings  | Profile update, Security/password changes                                                            |

**Test Environment:** SQLite in-memory, array-based cache/session/mail, sync queue.

---

## ✅ Code Quality

```bash
# PHP linting (Laravel Pint)
composer lint              # Fix issues
composer lint:check        # Check only

# Frontend linting (ESLint)
npm run lint               # Fix issues
npm run lint:check         # Check only

# Frontend formatting (Prettier)
npm run format             # Fix formatting
npm run format:check       # Check only

# TypeScript type checking
npm run types:check

# Full CI check (all of the above)
composer ci:check
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).

---

<p align="center">
  Made with ❤️ using Laravel, Vue, and Tailwind CSS
</p>
