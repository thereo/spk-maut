# 🧠 MAUT Decision Support System – Laravel + Filament

This is a Laravel-based decision support system using the **Multi-Attribute Utility Theory (MAUT)** method for evaluating and ranking employees based on multiple weighted criteria.

Built with **FilamentPHP**, this system supports:

- 🔢 Multi-criteria employee evaluation
- 🗂 Batch-based scoring
- 🧮 Step-by-step MAUT calculation
- 📊 Normalization, weighted scoring & ranking
- 📄 PDF export of final results
- 💡 Clean, responsive UI for admins

## ⚙️ Features

| Feature                    | Description                                                               |
|----------------------------|---------------------------------------------------------------------------|
| 🧑‍💼 Employees             | Manage evaluated employees                                                |
| 🧮 Criteria                 | Define weighted evaluation criteria (e.g. productivity, leadership)       |
| 📦 Batches                 | Group evaluations into logical batches (e.g. Q1 2025, Marketing Team)     |
| ✍️ Input Scores            | Input raw scores for each employee per criterion per batch                |
| 📈 MAUT Scoring            | Normalize scores, apply weights, rank employees                           |
| 📄 Export to PDF           | Generate professional PDF reports with full scoring breakdown             |
| 🧮 Supports Cost/Benefit   | (Optional) Handle benefit vs cost-type criteria via `is_benefit` flag     |

## 🏗️ Tech Stack

- Laravel 10+
- FilamentPHP 3.x
- DomPDF (for PDF export)
- TailwindCSS (via Filament)
- PHP 8.1+

## 🚀 Getting Started

### 1. Clone the Repo

```bash
git clone https://github.com/your-username/maut-filament-app.git
cd maut-filament-app
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env`:

```env
DB_DATABASE=maut_db
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate
```

(Optional) Seed with dummy data:

```bash
php artisan db:seed
```

### 4. Create an Admin User

```bash
php artisan tinker

\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
])
```

### 5. Start the App

```bash
php artisan serve
```

Visit: [http://localhost:8000/admin](http://localhost:8000/admin)

## 📁 File Structure Highlights

```bash
app/
├── Filament/
│   ├── Pages/
│   │   ├── MautScoring.php      # Core MAUT calculation & PDF export
│   │   ├── InputCriterionValues.php
│   │   └── EvaluationList.php
resources/
├── views/
│   └── maut/
│       ├── export.blade.php     # Full printable PDF report
│       └── partial-scoring-table.blade.php
```

## 📝 Customization Tips

- To **add new criteria types**, edit the `criteria` table and weights.
- To enable **cost vs benefit** support, add an `is_benefit` boolean column.
- You can fully customize the PDF layout via `resources/views/maut/export.blade.php`.

## 🛠 TODO (Future Enhancements)

- ✅ Export to Excel
- ✅ Chart visualizations (bar/pie/radar)
- ⏳ Multi-user roles (reviewer vs admin)
- ⏳ Criteria categories/subgroups
- ⏳ Audit log or score history

## 📄 License

MIT – free to use and modify.
