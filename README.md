# 🏙️ UrusProperti

**Empowering Property Owners with Intelligence, Precision, and Visual Excellence.**

[![Laravel 12](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue 3.5](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Vite 8](https://img.shields.io/badge/Vite-8.0-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Tailwind 4](https://img.shields.io/badge/Tailwind-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-93%20Passed-22C55E?style=for-the-badge&logo=checkmarx&logoColor=white)](#-testing)

<p align="center">
  <b>UrusProperti</b> is a full-stack property management platform built for professional landlords and property managers.
  It delivers real-time financial reporting, occupancy analytics, tenant lifecycle management, and AI-driven insights
  through a premium glassmorphism dark-mode interface.
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-tech-stack">Tech Stack</a> •
  <a href="#-project-structure">Structure</a> •
  <a href="#-getting-started">Getting Started</a> •
  <a href="#-testing">Testing</a> •
  <a href="#-deployment">Deployment</a>
</p>

---

## ✨ Features

### 📊 Dashboard & Reporting
- **Real-Time Dashboard** — Revenue stats, occupancy rates, and recent receipts at a glance
- **Financial Reports** — Income vs. expense trends with Chart.js visualizations
- **Occupancy Analytics** — Track vacancy rates across all properties and units
- **Schedule View** — Timeline of upcoming lease expirations and rent cycles
- **Profit & Loss API** — JSON endpoints powering dynamic front-end charts

### 🏠 Property Management
- **Multi-Property Support** — Manage unlimited properties with individual locations, rules, and units
- **Unit & Room Types** — Define room templates with daily/weekly/monthly/annual pricing
- **Property Rules** — Attach custom rule sets per property (pets, noise, parking, etc.)
- **Location Tracking** — Full address breakdown (country → subdistrict → postal code)

### 👤 Tenant Management
- **Full Tenant Profiles** — Contact info, emergency contacts, banking details, lease terms
- **Unit Assignment** — Link tenants to specific property units with automatic cycle tracking
- **Search & Filter** — Search across tenant names, emails, mobiles, unit names, and property names
- **Soft Deletes** — Safe tenant removal with data recovery capability

### 🧾 Financial Operations
- **Receipt Generation** — Create detailed receipts with discount types, tax, down payments
- **Expense Tracking** — Categorized expenses (marketing, grocery, electricity, water, salary, fixing, supplies, others)
- **Multi-Currency** — Currency support per property and tenant

### 🤖 AI & Multi-Agent Intelligence
- **Primary Agent Orchestrator** — Powered by **Gemini 1.5 Flash**, managing complex reasoning loops.
- **Specialized Sub-Agents** — Independent agents for `Tenants`, `Expenses`, and `Properties` that the Orchestrator calls via **Function Calling**.
- **RAG Pipeline (Retrieval Augmented Generation)** — Uses **Vertex AI `text-embedding-004`** to perform semantic vector searches across unstructured tenant notes and financial records.
- **Autonomous Reasoning** — The system can correlate data across domains (e.g., identifying if a late-paying tenant is located in a high-maintenance property).
- **Parallel Execution** — Supports parallel function calling to reduce latency when fetching data from multiple sub-agents.

### 🔐 Authentication & Security
- **Laravel Fortify** — Full authentication suite (login, register, password reset, email verification)
- **Two-Factor Authentication** — Optional 2FA with recovery codes
- **Password Confirmation** — Sensitive actions require re-authentication

---

## 🛠 Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Backend** | Laravel 12 | Core framework, Eloquent ORM, routing |
| **Frontend** | Vue 3.5 (Composition API) | Reactive SPA components |
| **Bridge** | Inertia.js 3 | Server-driven SPA without API boilerplate |
| **Auth** | Laravel Fortify | Authentication, 2FA, email verification |
| **UI Library** | Shadcn-Vue | Accessible, composable UI primitives |
| **Styling** | Tailwind CSS 4 | Utility-first CSS with custom glassmorphism utilities |
| **Build** | Vite 8 | Lightning-fast HMR and production bundling |
| **Charts** | Chart.js | Financial and occupancy data visualization |
| **Database** | SQLite (dev) / PostgreSQL (prod) | AlloyDB-ready for Google Cloud deployment |
| **AI Model** | Gemini 2.5 Flash | Primary Reasoning & Orchestration |
| **Embeddings** | Text-Embedding-004 | Vector representation of property data |
| **Orchestration** | Vertex AI SDK | Tool-use and Function Calling |
| **Vector DB** | pgvector (AlloyDB) | High-performance semantic search |
| **Testing** | PHPUnit 11 | 93 tests, 494 assertions |
| **Deployment** | Google Cloud Run | Serverless container orchestration |

---

## 📁 Project Structure

```
property/
├── app/
│   ├── Actions/Fortify/        # User registration & profile actions
│   ├── Concerns/               # Shared validation traits
│   ├── Http/Controllers/       # 8 resource controllers + AI controllers
│   └── Models/                 # 9 Eloquent models (User, Property, Tenant, etc.)
├── database/
│   ├── factories/              # User factory for testing
│   ├── migrations/             # 17 migration files
│   └── seeders/                # Rich demo data seeder (3 properties)
├── resources/js/
│   ├── components/             # Reusable Vue components (forms, modals, UI primitives)
│   ├── layouts/                # App & Auth layouts with sidebar navigation
│   └── pages/                  # 10 page directories (dashboard, reports, CRUD pages)
├── tests/
│   ├── Feature/                # 17 feature test files (auth, CRUD, reports)
│   └── Unit/                   # Unit tests
├── Dockerfile                  # Production container (PHP 8.4-FPM + Nginx)
├── nginx.conf                  # Cloud Run Nginx configuration
└── start.sh                    # Container entrypoint script
```

---

## 🚀 Getting Started

### Prerequisites

- **PHP** ≥ 8.2
- **Composer** ≥ 2.0
- **Node.js** ≥ 18
- **npm** ≥ 9

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/eCarlsson-r/PropertyManagement.git && cd PropertyManagement

# 2. Install PHP and Node dependencies
composer install && npm install

# 3. Environment setup
cp .env.example .env && php artisan key:generate

# 4. Run database migrations with demo data
php artisan migrate --seed

# 5. Launch the development server
npm run dev
```

The application will be available at **http://localhost:8000** (or your configured Herd domain).

### Demo Data

The seeder provisions **2 fully-furnished properties**. Each property includes tenants, receipts, categorized expenses, and property rules.

---

## 🧪 Testing

The test suite covers **93 tests** with **494 assertions** across authentication, CRUD operations, reports, and settings.

```bash
# Run full test suite
php artisan test

# Run specific test file
php artisan test --filter=PropertyControllerTest

# Run with coverage (requires Xdebug/PCOV)
php artisan test --coverage
```

### Test Coverage

| Suite | Tests | What's Covered |
|-------|-------|----------------|
| **Auth** | 25 | Login, register, 2FA, password reset, email verification |
| **Property** | 9 | CRUD, search filtering, auth guards, validation |
| **Tenant** | 9 | CRUD, relationship search (unit/property/room), FK integrity |
| **Room** | 10 | CRUD, search, validation of pricing fields |
| **Receipt** | 8 | CRUD, FK chain (tenant → unit → property), validation |
| **Expense** | 10 | CRUD, category enum validation, date filtering |
| **Report** | 7 | Dashboard, schedule, occupancy, financial pages & JSON APIs |
| **Settings** | 11 | Profile update, password change, account deletion |
| **Other** | 4 | Dashboard access, homepage, general assertions |

---

## 🌐 Deployment

UrusProperti is containerized and ready for **Google Cloud Run** with **AlloyDB** (PostgreSQL).

### Quick Deploy

```bash
# Build & deploy from source
gcloud run deploy property-manager \
    --region=us-central1 \
    --source=. \
    --allow-unauthenticated \
    --vpc-connector=run-vpc \
    --set-secrets=/var/www/.env=LARAVEL_ENV_VARS:latest \
    --execution-environment=gen2
```

### Infrastructure

- **Container**: PHP 8.4-FPM + Nginx on Cloud Run
- **Database**: AlloyDB (PostgreSQL-compatible) with VPC peering
- **Secrets**: Google Secret Manager for environment variables
- **Auto-Migration**: Container startup runs `php artisan migrate --force`

See the full deployment guide in the project documentation.

---

## 🎨 Design Philosophy

UrusProperti follows a **Neo-Professional Dark Mode** aesthetic inspired by high-end financial terminals:

- **Glassmorphism** — High-blur backgrounds with subtle `border-white/5` borders
- **Neon Cyan Accents** — `#00E5FF` highlights for critical data points and CTAs
- **Micro-Animations** — Smooth Vue transitions and CSS hardware-accelerated effects
- **Shadcn-Vue Primitives** — Accessible, composable Card, Select, Dialog, and Button components

---

<div align="center">
  <p><b>Designed by</b> <a href="https://github.com/eCarlsson-r">Carlsson Studio</a> | <b>Built for</b> Success</p>
  <p>© 2026 Carlsson Studio. All rights reserved.</p>
</div>
