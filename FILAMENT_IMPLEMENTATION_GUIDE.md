# Reflect Constructions - Filament Implementation Guide

## Project Overview

Construction/Contracting project management system. Originally built with Laravel 10 + AdminLTE + Yajra DataTables + Livewire. This guide covers the full reimplementation using **Filament v3**.

---

## Tech Stack (Target)

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11 |
| Admin Panel | Filament v3 |
| PHP | ^8.2 |
| Translations | Spatie Laravel Translatable + Filament Spatie Translatable Plugin |
| Auth | Filament built-in (Shield plugin for roles/permissions) |
| File Uploads | Filament built-in (SpatieMediaLibrary optional) |

---

## Installation Steps

```bash
# 1. Create new Laravel project
composer create-project laravel/laravel reflect-constructions

# 2. Install Filament
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels

# 3. Install Plugins
composer require filament/spatie-laravel-translatable-plugin:"^3.2" -W
composer require bezhansalleh/filament-shield:"^3.2" -W
composer require spatie/laravel-translatable

# 4. Install Shield (Roles & Permissions)
php artisan shield:install

# 5. Create admin user
php artisan make:filament-user
```

---

## Database Schema & Migrations

### Order of Migration Creation

Create migrations in this order to respect foreign key dependencies:

```
1.  users (exists by default)
2.  languages
3.  settings (depends on languages)
4.  categories (self-referencing parent_id)
5.  clients
6.  projects (depends on clients, categories)
7.  tasks (depends on projects, categories)
8.  workers
9.  task_workers (pivot: tasks, workers)
10. products (depends on categories)
11. suppliers
12. purchase_tasks (depends on suppliers, products, tasks)
13. payment_methods
14. payments (polymorphic, depends on payment_methods)
15. expense_categories
16. expenses (depends on expense_categories, projects, payment_methods, users)
17. images (polymorphic)
18. branches
19. roles (handled by Shield)
20. permissions (handled by Shield)
21. role_permissions (handled by Shield)
22. contact_us
23. static_pages
24. sliders
```

### Migration Definitions

#### `languages`
```php
Schema::create('languages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('symbol');       // e.g., 'ar', 'en'
    $table->string('direction');    // 'rtl' or 'ltr'
    $table->boolean('active')->default(true);
    $table->string('image')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### `settings`
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->json('app_name');           // translatable
    $table->string('contact_email');
    $table->string('contact_phone');
    $table->string('whatsapp')->nullable();
    $table->string('facebook')->nullable();
    $table->string('instagram')->nullable();
    $table->string('image');            // logo
    $table->string('fav_ico');
    $table->foreignId('default_lang')->constrained('languages');
    $table->timestamps();
    $table->softDeletes();
});
```

#### `categories`
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->json('name');               // translatable
    $table->json('description')->nullable(); // translatable
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->string('image');
    $table->boolean('active')->default(true);
    $table->boolean('home_page')->default(false);
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
});
```

#### `clients`
```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->nullable();
    $table->string('phone');
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### `projects`
```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('client_id')->constrained();
    $table->foreignId('category_id')->constrained();
    $table->string('start_date');
    $table->string('end_date')->nullable();
    $table->integer('status');           // 0=pending, 1=active, 2=completed, etc.
    $table->double('observation')->nullable();
    $table->double('final_total')->nullable();
    $table->double('paid_total')->nullable();
    $table->double('rest_total')->nullable();
    $table->string('image')->nullable();
    $table->text('note')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### `tasks`
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained();
    $table->foreignId('category_id')->constrained();
    $table->string('name');
    $table->text('description')->nullable();
    $table->double('final_total')->nullable();
    $table->string('start_date');
    $table->string('end_date')->nullable();
    $table->double('rest_total')->nullable();
    $table->double('paid_total')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### `workers`
```php
Schema::create('workers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone_number');
    $table->string('image')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `task_workers` (pivot)
```php
Schema::create('task_workers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('task_id')->constrained();
    $table->foreignId('worker_id')->constrained();
    $table->double('paid')->default(0);
    $table->timestamps();
});
```

#### `products`
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->double('price');
    $table->foreignId('category_id')->constrained();
    $table->string('image');
    $table->boolean('active')->default(true);
    $table->boolean('featured')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `suppliers`
```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->json('name');              // translatable
    $table->string('phone');
    $table->string('address')->nullable();
    $table->string('image')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `purchase_tasks`
```php
Schema::create('purchase_tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained();
    $table->foreignId('task_id')->constrained();
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity');
    $table->double('unit_price');
    $table->double('total');
    $table->double('discount')->nullable();
    $table->double('final_total');
    $table->timestamps();
});
```

#### `payment_methods`
```php
Schema::create('payment_methods', function (Blueprint $table) {
    $table->id();
    $table->json('name');              // translatable
    $table->string('image')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `payments`
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('payment_method_id')->constrained();
    $table->double('paid');
    $table->morphs('paymentable');     // paymentable_type + paymentable_id
    $table->string('payment_code')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### `expense_categories`
```php
Schema::create('expense_categories', function (Blueprint $table) {
    $table->id();
    $table->json('name');              // translatable
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `expenses`
```php
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->double('value');
    $table->string('date');
    $table->foreignId('added_by')->constrained('users');
    $table->foreignId('last_edit_by')->constrained('users');
    $table->foreignId('expense_category_id')->constrained();
    $table->foreignId('project_id')->nullable()->constrained();
    $table->foreignId('payment_method_id')->constrained();
    $table->timestamps();
    $table->softDeletes();
});
```

#### `images` (polymorphic)
```php
Schema::create('images', function (Blueprint $table) {
    $table->id();
    $table->string('image');
    $table->morphs('imageable');       // imageable_type + imageable_id
    $table->timestamps();
});
```

#### `branches`
```php
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->json('name');              // translatable
    $table->json('address');           // translatable
    $table->string('lng')->nullable();
    $table->string('lat')->nullable();
    $table->boolean('active')->default(true);
    $table->boolean('base_branch')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `contact_us`
```php
Schema::create('contact_us', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('phone');
    $table->text('message');
    $table->timestamps();
    $table->softDeletes();
});
```

#### `static_pages`
```php
Schema::create('static_pages', function (Blueprint $table) {
    $table->id();
    $table->json('page_title');        // translatable
    $table->json('page_content');      // translatable
    $table->boolean('active')->default(true);
    $table->boolean('show_menu')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

#### `sliders`
```php
Schema::create('sliders', function (Blueprint $table) {
    $table->id();
    $table->json('title')->nullable(); // translatable
    $table->json('image');             // translatable (per-locale images)
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

---

## Models

### Model Creation Commands

```bash
php artisan make:model Language -m
php artisan make:model Setting -m
php artisan make:model Category -m
php artisan make:model Client -m
php artisan make:model Project -m
php artisan make:model Task -m
php artisan make:model Worker -m
php artisan make:model Product -m
php artisan make:model Supplier -m
php artisan make:model PurchaseTask -m
php artisan make:model PaymentMethod -m
php artisan make:model Payment -m
php artisan make:model ExpenseCategory -m
php artisan make:model Expense -m
php artisan make:model Image -m
php artisan make:model Branch -m
php artisan make:model ContactUs -m
php artisan make:model StaticPage -m
php artisan make:model Slider -m
```

### Model Definitions

#### `Project`
```php
use SoftDeletes;

protected $fillable = [
    'name', 'description', 'client_id', 'category_id',
    'start_date', 'end_date', 'status', 'observation',
    'final_total', 'paid_total', 'rest_total', 'image', 'note'
];

public function client(): BelongsTo { return $this->belongsTo(Client::class); }
public function category(): BelongsTo { return $this->belongsTo(Category::class); }
public function tasks(): HasMany { return $this->hasMany(Task::class)->orderBy('category_id'); }
public function payments(): MorphMany { return $this->morphMany(Payment::class, 'paymentable'); }
public function images(): MorphMany { return $this->morphMany(Image::class, 'imageable'); }
public function expenses(): HasMany { return $this->hasMany(Expense::class); }
```

#### `Task`
```php
use SoftDeletes;

protected $fillable = [
    'project_id', 'category_id', 'name', 'description',
    'final_total', 'start_date', 'end_date', 'rest_total', 'paid_total'
];

public function project(): BelongsTo { return $this->belongsTo(Project::class); }
public function category(): BelongsTo { return $this->belongsTo(Category::class); }
public function images(): MorphMany { return $this->morphMany(Image::class, 'imageable'); }
public function workers(): BelongsToMany {
    return $this->belongsToMany(Worker::class, 'task_workers')->withPivot('paid');
}
public function purchases(): HasMany {
    return $this->hasMany(PurchaseTask::class)->with(['product', 'supplier']);
}
```

#### `Client`
```php
use SoftDeletes;

protected $fillable = ['name', 'email', 'phone', 'notes'];

public function projects(): HasMany { return $this->hasMany(Project::class); }
```

#### `Worker`
```php
use SoftDeletes;

protected $fillable = ['name', 'phone_number', 'image', 'active'];

public function payments(): MorphMany { return $this->morphMany(Payment::class, 'paymentable'); }
public function tasks(): BelongsToMany {
    return $this->belongsToMany(Task::class, 'task_workers')->withPivot('paid');
}
```

#### `Category` (Translatable)
```php
use SoftDeletes, HasTranslations;

public $translatable = ['name', 'description'];
protected $fillable = ['name', 'description', 'parent_id', 'image', 'active', 'home_page'];

public function parent(): BelongsTo { return $this->belongsTo(Category::class, 'parent_id'); }
public function children(): HasMany { return $this->hasMany(Category::class, 'parent_id'); }
```

#### `Product`
```php
use SoftDeletes;

protected $fillable = ['name', 'description', 'price', 'category_id', 'image', 'active', 'featured'];

public function category(): BelongsTo { return $this->belongsTo(Category::class); }
```

#### `Supplier` (Translatable)
```php
use SoftDeletes, HasTranslations;

public $translatable = ['name'];
protected $fillable = ['name', 'phone', 'address', 'image', 'active'];

public function payments(): MorphMany { return $this->morphMany(Payment::class, 'paymentable'); }
public function purchases(): HasMany { return $this->hasMany(PurchaseTask::class); }
```

#### `Payment`
```php
use SoftDeletes;

protected $fillable = ['payment_method_id', 'paid', 'paymentable_type', 'paymentable_id', 'payment_code'];

public function paymentable(): MorphTo { return $this->morphTo(); }
public function paymentMethod(): BelongsTo { return $this->belongsTo(PaymentMethod::class); }
```

#### `Expense`
```php
use SoftDeletes;

protected $fillable = [
    'name', 'description', 'value', 'date',
    'expense_category_id', 'project_id', 'payment_method_id',
    'added_by', 'last_edit_by'
];

public function addedBy(): BelongsTo { return $this->belongsTo(User::class, 'added_by'); }
public function lastEditBy(): BelongsTo { return $this->belongsTo(User::class, 'last_edit_by'); }
public function expenseCategory(): BelongsTo { return $this->belongsTo(ExpenseCategory::class); }
public function project(): BelongsTo { return $this->belongsTo(Project::class); }
public function paymentMethod(): BelongsTo { return $this->belongsTo(PaymentMethod::class); }
```

#### `PurchaseTask`
```php
protected $fillable = [
    'supplier_id', 'product_id', 'task_id',
    'quantity', 'unit_price', 'total', 'discount', 'final_total'
];

public function product(): BelongsTo { return $this->belongsTo(Product::class); }
public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
public function task(): BelongsTo { return $this->belongsTo(Task::class); }
```

#### `Image`
```php
protected $fillable = ['image', 'imageable_type', 'imageable_id'];

public function imageable(): MorphTo { return $this->morphTo(); }
```

#### Other Translatable Models

```php
// PaymentMethod
use SoftDeletes, HasTranslations;
public $translatable = ['name'];
protected $fillable = ['name', 'image', 'active'];

// ExpenseCategory
use SoftDeletes, HasTranslations;
public $translatable = ['name'];
protected $fillable = ['name', 'active'];

// Branch
use SoftDeletes, HasTranslations;
public $translatable = ['name', 'address'];
protected $fillable = ['name', 'address', 'lng', 'lat', 'active', 'base_branch'];

// Setting
use SoftDeletes, HasTranslations;
public $translatable = ['app_name'];
protected $fillable = ['app_name', 'contact_email', 'contact_phone', 'whatsapp', 'facebook', 'instagram', 'image', 'fav_ico', 'default_lang'];
public function language(): BelongsTo { return $this->belongsTo(Language::class, 'default_lang'); }

// StaticPage
use SoftDeletes, HasTranslations;
public $translatable = ['page_title', 'page_content'];
protected $fillable = ['page_title', 'page_content', 'active', 'show_menu'];

// Slider
use SoftDeletes, HasTranslations;
public $translatable = ['title'];
protected $fillable = ['title', 'image', 'active'];

// ContactUs
use SoftDeletes;
protected $fillable = ['name', 'email', 'phone', 'message'];

// Language
use SoftDeletes;
protected $fillable = ['name', 'symbol', 'direction', 'active', 'image'];
```

---

## Filament Resources (Full Implementation)

### Generate All Resources

```bash
php artisan make:filament-resource Project --generate --soft-deletes
php artisan make:filament-resource Task --generate --soft-deletes
php artisan make:filament-resource Client --generate --soft-deletes
php artisan make:filament-resource Worker --generate --soft-deletes
php artisan make:filament-resource Category --generate --soft-deletes
php artisan make:filament-resource Product --generate --soft-deletes
php artisan make:filament-resource Supplier --generate --soft-deletes
php artisan make:filament-resource Payment --generate --soft-deletes
php artisan make:filament-resource PaymentMethod --generate --soft-deletes
php artisan make:filament-resource Expense --generate --soft-deletes
php artisan make:filament-resource ExpenseCategory --generate --soft-deletes
php artisan make:filament-resource Branch --generate --soft-deletes
php artisan make:filament-resource Language --generate --soft-deletes
php artisan make:filament-resource Setting --generate --soft-deletes
php artisan make:filament-resource ContactUs --generate --soft-deletes
php artisan make:filament-resource StaticPage --generate --soft-deletes
php artisan make:filament-resource Slider --generate --soft-deletes
```

---

### Resource #1: ProjectResource

**Navigation Group:** `Project Management`

#### Form Schema
```php
use Filament\Forms\Components\*;

Forms\Components\Section::make('Basic Info')->schema([
    TextInput::make('name')->required()->maxLength(255),
    RichEditor::make('description')->columnSpanFull(),
    Select::make('client_id')
        ->relationship('client', 'name')
        ->searchable()->preload()->required()
        ->createOptionForm([...]),  // inline client creation
    Select::make('category_id')
        ->relationship('category', 'name')
        ->searchable()->preload()->required(),
    Select::make('status')
        ->options([
            0 => 'Pending',
            1 => 'In Progress',
            2 => 'Completed',
            3 => 'Cancelled',
        ])->required(),
])->columns(2),

Section::make('Dates')->schema([
    DatePicker::make('start_date')->required(),
    DatePicker::make('end_date'),
])->columns(2),

Section::make('Financials')->schema([
    TextInput::make('final_total')->numeric()->prefix('EGP'),
    TextInput::make('paid_total')->numeric()->prefix('EGP'),
    TextInput::make('rest_total')->numeric()->prefix('EGP'),
    TextInput::make('observation')->numeric(),
])->columns(2),

Section::make('Media')->schema([
    FileUpload::make('image')
        ->image()->directory('projects')
        ->imageResizeMode('cover'),
]),

Section::make('Notes')->schema([
    Textarea::make('note')->rows(3)->columnSpanFull(),
]),

// Payments Repeater
Section::make('Payments')->schema([
    Repeater::make('payments')
        ->relationship()
        ->schema([
            Select::make('payment_method_id')
                ->relationship('paymentMethod', 'name')
                ->required(),
            TextInput::make('paid')->numeric()->required(),
            TextInput::make('payment_code'),
        ])->columns(3),
]),
```

#### Table Columns
```php
Tables\Columns\ImageColumn::make('image')->circular(),
Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
Tables\Columns\TextColumn::make('client.name')->searchable()->sortable(),
Tables\Columns\TextColumn::make('category.name')->sortable(),
Tables\Columns\TextColumn::make('status')
    ->badge()
    ->color(fn (int $state) => match ($state) {
        0 => 'warning',
        1 => 'info',
        2 => 'success',
        3 => 'danger',
    })
    ->formatStateUsing(fn (int $state) => match ($state) {
        0 => 'Pending',
        1 => 'In Progress',
        2 => 'Completed',
        3 => 'Cancelled',
    }),
Tables\Columns\TextColumn::make('final_total')->money('EGP')->sortable(),
Tables\Columns\TextColumn::make('paid_total')->money('EGP')->sortable(),
Tables\Columns\TextColumn::make('rest_total')->money('EGP')->sortable(),
Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
Tables\Columns\TextColumn::make('tasks_count')->counts('tasks')->label('Tasks'),
```

#### Relation Managers
```php
// TasksRelationManager - manage tasks within project
// PaymentsRelationManager - manage payments within project
// ImagesRelationManager - manage gallery images
// ExpensesRelationManager - manage project expenses
```

#### Custom Page: Project Show (View)
- Project details card
- Financial summary widgets
- Tasks grouped by category (like original taskCategories())
- Image gallery with date filter
- Payment history
- Chart: tasks breakdown by category

---

### Resource #2: TaskResource

**Navigation Group:** `Project Management`

#### Form Schema
```php
Select::make('project_id')
    ->relationship('project', 'name')
    ->searchable()->preload()->required(),
Select::make('category_id')
    ->relationship('category', 'name')
    ->searchable()->preload()->required(),
TextInput::make('name')->required(),
Textarea::make('description'),
DatePicker::make('start_date')->required(),
DatePicker::make('end_date'),
TextInput::make('final_total')->numeric()->prefix('EGP'),
TextInput::make('paid_total')->numeric()->prefix('EGP'),
TextInput::make('rest_total')->numeric()->prefix('EGP'),
```

#### Relation Managers
```php
// WorkersRelationManager - pivot table with 'paid' field
// PurchaseTasksRelationManager - manage purchases
// ImagesRelationManager - manage task images
```

#### Workers Relation Manager (Pivot)
```php
Tables\Columns\TextColumn::make('name'),
Tables\Columns\TextColumn::make('phone_number'),
Tables\Columns\TextColumn::make('pivot.paid')->money('EGP')->label('Paid'),

// Attach action with custom form
AttachAction::make()
    ->form(fn (AttachAction $action) => [
        $action->getRecordSelect(),
        TextInput::make('paid')->numeric()->required()->default(0),
    ]),
```

#### Purchase Tasks Relation Manager
```php
// Form
Select::make('supplier_id')->relationship('supplier', 'name')->required(),
Select::make('product_id')->relationship('product', 'name')->required(),
TextInput::make('quantity')->numeric()->required()->reactive()
    ->afterStateUpdated(fn ($state, Set $set, Get $get) =>
        $set('total', $state * $get('unit_price'))),
TextInput::make('unit_price')->numeric()->required()->reactive()
    ->afterStateUpdated(fn ($state, Set $set, Get $get) =>
        $set('total', $state * $get('quantity'))),
TextInput::make('total')->numeric()->disabled()->dehydrated(),
TextInput::make('discount')->numeric(),
TextInput::make('final_total')->numeric(),
```

---

### Resource #3: ClientResource

**Navigation Group:** `Client Management`

#### Form Schema
```php
TextInput::make('name')->required(),
TextInput::make('email')->email(),
TextInput::make('phone')->required()->tel(),
Textarea::make('notes'),
```

#### Table Columns
```php
TextColumn::make('name')->searchable()->sortable(),
TextColumn::make('phone')->searchable(),
TextColumn::make('email'),
TextColumn::make('projects_count')->counts('projects')->label('Projects'),
```

#### Custom View Page
- Client info
- Projects list with totals
- Total paid across all projects
- Payment history (aggregated from project payments)

---

### Resource #4: WorkerResource

**Navigation Group:** `HR Management`

#### Form Schema
```php
TextInput::make('name')->required(),
TextInput::make('phone_number')->required()->tel(),
FileUpload::make('image')->image()->directory('workers'),
Toggle::make('active')->default(true),
```

#### Relation Managers
```php
// PaymentsRelationManager (morphMany)
// TasksRelationManager (belongsToMany with pivot 'paid')
```

---

### Resource #5: CategoryResource (Translatable)

**Navigation Group:** `Catalog`

> Use `HasTranslatableFormAction` from Filament Spatie Translatable Plugin

#### Form Schema
```php
TextInput::make('name')->required(),   // auto-translatable
Textarea::make('description'),          // auto-translatable
Select::make('parent_id')
    ->relationship('parent', 'name')
    ->searchable()->preload()->nullable(),
FileUpload::make('image')->image()->directory('categories')->required(),
Toggle::make('active')->default(true),
Toggle::make('home_page')->default(false),
```

#### Table
```php
ImageColumn::make('image')->circular(),
TextColumn::make('name')->searchable()->sortable(),
TextColumn::make('parent.name')->label('Parent'),
IconColumn::make('active')->boolean(),
IconColumn::make('home_page')->boolean(),
```

---

### Resource #6: ProductResource

**Navigation Group:** `Catalog`

#### Form Schema
```php
TextInput::make('name')->required(),
Textarea::make('description'),
TextInput::make('price')->numeric()->required()->prefix('EGP'),
Select::make('category_id')
    ->relationship('category', 'name')
    ->searchable()->preload()->required(),
FileUpload::make('image')->image()->directory('products')->required(),
Toggle::make('active')->default(true),
Toggle::make('featured')->default(false),
```

---

### Resource #7: SupplierResource (Translatable)

**Navigation Group:** `Procurement`

#### Form Schema
```php
TextInput::make('name')->required(),   // translatable
TextInput::make('phone')->required()->tel(),
TextInput::make('address'),
FileUpload::make('image')->image()->directory('suppliers'),
Toggle::make('active')->default(true),
```

#### Relation Managers
```php
// PaymentsRelationManager (morphMany)
// PurchaseTasksRelationManager
```

---

### Resource #8: ExpenseResource

**Navigation Group:** `Finance`

#### Form Schema
```php
TextInput::make('name')->required(),
Textarea::make('description'),
TextInput::make('value')->numeric()->required()->prefix('EGP'),
DatePicker::make('date')->required(),
Select::make('expense_category_id')
    ->relationship('expenseCategory', 'name')
    ->searchable()->preload()->required(),
Select::make('project_id')
    ->relationship('project', 'name')
    ->searchable()->preload()->nullable(),
Select::make('payment_method_id')
    ->relationship('paymentMethod', 'name')
    ->searchable()->preload()->required(),
// added_by and last_edit_by are set automatically:
Hidden::make('added_by')->default(auth()->id()),
Hidden::make('last_edit_by')->default(auth()->id()),
```

#### Auto-set `last_edit_by` on update:
```php
protected function mutateFormDataBeforeSave(array $data): array
{
    $data['last_edit_by'] = auth()->id();
    return $data;
}
```

---

### Resource #9: PaymentResource

**Navigation Group:** `Finance`

#### Form Schema
```php
Select::make('payment_method_id')
    ->relationship('paymentMethod', 'name')
    ->required(),
TextInput::make('paid')->numeric()->required()->prefix('EGP'),
MorphToSelect::make('paymentable')
    ->types([
        MorphToSelect\Type::make(Project::class)->titleAttribute('name'),
        MorphToSelect\Type::make(Worker::class)->titleAttribute('name'),
        MorphToSelect\Type::make(Supplier::class)->titleAttribute('name'),
    ])->required(),
TextInput::make('payment_code'),
```

---

### Resource #10: PaymentMethodResource (Translatable)

**Navigation Group:** `Finance`

```php
TextInput::make('name')->required(),    // translatable
FileUpload::make('image')->image()->directory('payment_methods'),
Toggle::make('active')->default(true),
```

---

### Resource #11: ExpenseCategoryResource (Translatable)

**Navigation Group:** `Finance`

```php
TextInput::make('name')->required(),    // translatable
Toggle::make('active')->default(true),
```

---

### Resource #12: BranchResource (Translatable)

**Navigation Group:** `Settings`

```php
TextInput::make('name')->required(),    // translatable
TextInput::make('address'),             // translatable
TextInput::make('lng'),
TextInput::make('lat'),
Toggle::make('active')->default(true),
Toggle::make('base_branch')->default(false),
```

---

### Resource #13: LanguageResource

**Navigation Group:** `Settings`

```php
TextInput::make('name')->required(),
TextInput::make('symbol')->required(),  // 'ar', 'en'
Select::make('direction')->options(['rtl' => 'RTL', 'ltr' => 'LTR'])->required(),
Toggle::make('active')->default(true),
FileUpload::make('image')->image()->directory('languages'),
```

---

### Resource #14: SettingResource (Translatable)

**Navigation Group:** `Settings`

> Implement as a **custom page** instead of CRUD (single row):

```php
// app/Filament/Pages/ManageSettings.php
class ManageSettings extends Page
{
    protected static string $view = 'filament.pages.manage-settings';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('app_name')->required(),    // translatable
            TextInput::make('contact_email')->email()->required(),
            TextInput::make('contact_phone')->required(),
            TextInput::make('whatsapp'),
            TextInput::make('facebook')->url(),
            TextInput::make('instagram')->url(),
            FileUpload::make('image')->image()->directory('settings'),
            FileUpload::make('fav_ico')->image()->directory('settings'),
            Select::make('default_lang')
                ->relationship('language', 'name')
                ->required(),
        ]);
    }
}
```

---

### Resource #15: ContactUsResource

**Navigation Group:** `CMS`

```php
// Read-only resource (no create/edit in panel)
TextColumn::make('name')->searchable(),
TextColumn::make('email'),
TextColumn::make('phone'),
TextColumn::make('message')->limit(50),
TextColumn::make('created_at')->dateTime(),
```

---

### Resource #16: StaticPageResource (Translatable)

**Navigation Group:** `CMS`

```php
TextInput::make('page_title')->required(),     // translatable
RichEditor::make('page_content')->required(),  // translatable
Toggle::make('active')->default(true),
Toggle::make('show_menu')->default(false),
```

---

### Resource #17: SliderResource (Translatable)

**Navigation Group:** `CMS`

```php
TextInput::make('title'),                      // translatable
FileUpload::make('image')->image()->directory('sliders')->required(),
Toggle::make('active')->default(true),
```

---

## Dashboard Widgets

### Widget #1: StatsOverview
```php
use Filament\Widgets\StatsOverviewWidget;

protected function getStats(): array
{
    return [
        Stat::make('Total Projects', Project::count()),
        Stat::make('Active Projects', Project::where('status', 1)->count()),
        Stat::make('Total Clients', Client::count()),
        Stat::make('Total Workers', Worker::where('active', true)->count()),
        Stat::make('Total Income', number_format(Payment::whereHasMorph('paymentable', Project::class)->sum('paid'), 2) . ' EGP'),
        Stat::make('Total Expenses', number_format(Expense::sum('value') + Payment::whereHasMorph('paymentable', [Worker::class, Supplier::class])->sum('paid'), 2) . ' EGP'),
    ];
}
```

### Widget #2: IncomeExpenseChart
```php
use Filament\Widgets\ChartWidget;

// Line/Bar chart showing income vs expenses by month
// Replicate the IncomeStats Livewire component logic
// Filter: All / Today / Yesterday / Custom date range
```

### Widget #3: ProjectsByCategoryChart
```php
// Pie/Doughnut chart showing projects grouped by category
// Sum of final_total per category
```

### Widget #4: LatestProjects
```php
use Filament\Widgets\TableWidget;

// Table showing 5 most recent projects with status badges
```

---

## Reports (Custom Filament Pages)

### Income Report Page
```php
// app/Filament/Pages/IncomeReport.php
// - Date range filter (from/to)
// - Table: Payment methods with total paid per method
// - Source: Project payments grouped by payment_method_id
// - Summary row with grand total
```

### Suppliers Report Page
```php
// app/Filament/Pages/SuppliersReport.php
// - Date range filter
// - Table: Supplier name, total purchases, total payments
// - Source: Supplier payments + PurchaseTasks
```

### Workers Report Page
```php
// app/Filament/Pages/WorkersReport.php
// - Date range filter
// - Table: Worker name, tasks count, total paid (from pivot + payments)
```

### Clients Report Page
```php
// app/Filament/Pages/ClientsReport.php
// - Date range filter
// - Table: Client name, projects count, total paid, total rest
// - Source: Clients with projects and payments aggregated
```

---

## Navigation Structure

```
Dashboard
├── StatsOverview Widget
├── IncomeExpenseChart Widget
├── ProjectsByCategoryChart Widget
└── LatestProjects Widget

Project Management
├── Projects (CRUD + View with Relations)
├── Tasks (CRUD + Workers + Purchases + Images)
└── Clients (CRUD + Projects view)

HR Management
├── Workers (CRUD + Payments + Tasks)

Procurement
├── Suppliers (CRUD + Payments + Purchases)
├── Products (CRUD)

Finance
├── Payments (CRUD with MorphTo)
├── Payment Methods (CRUD)
├── Expenses (CRUD)
├── Expense Categories (CRUD)

Catalog
├── Categories (CRUD, tree structure)
├── Products (CRUD)

CMS
├── Static Pages (CRUD)
├── Sliders (CRUD)
├── Contact Us (Read-only list)

Settings
├── App Settings (Single-row form)
├── Languages (CRUD)
├── Branches (CRUD)

Reports
├── Income Report
├── Suppliers Report
├── Workers Report
└── Clients Report

Access Control (via Shield)
├── Roles
└── Permissions
```

---

## Translatable Resources Setup

For every translatable resource, add the `HasTranslatableForm` concern:

```php
use Filament\Resources\Concerns\Translatable;

class CategoryResource extends Resource
{
    use Translatable;

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }
}
```

In the `ListRecords` and `EditRecord` pages:
```php
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListCategories extends ListRecords
{
    use Translatable;
}
```

This adds a locale switcher to forms and tables.

---

## Polymorphic Image Gallery

### Reusable ImagesRelationManager
```php
// app/Filament/RelationManagers/ImagesRelationManager.php

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('image')
                ->image()
                ->directory('gallery')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->height(100)->width(100),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                DeleteAction::make(),
            ]);
    }
}
```

Use in both `ProjectResource` and `TaskResource`:
```php
public static function getRelations(): array
{
    return [
        ImagesRelationManager::class,
        // ... other relation managers
    ];
}
```

---

## Polymorphic Payments RelationManager

### Reusable PaymentsRelationManager
```php
// app/Filament/RelationManagers/PaymentsRelationManager.php

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('payment_method_id')
                ->relationship('paymentMethod', 'name')
                ->required(),
            TextInput::make('paid')->numeric()->required()->prefix('EGP'),
            TextInput::make('payment_code'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paymentMethod.name'),
                TextColumn::make('paid')->money('EGP'),
                TextColumn::make('payment_code'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
```

Use in `ProjectResource`, `WorkerResource`, `SupplierResource`.

---

## Middleware: Localization

```php
// app/Http/Middleware/SetLocale.php
class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = session('locale', Setting::first()?->default_lang?->symbol ?? 'ar');
        app()->setLocale($locale);
        return $next($request);
    }
}

// Register in Filament Panel Provider:
->middleware([SetLocale::class])
```

---

## API (Optional - Keep Existing)

If the API is still needed (for mobile app, etc.), keep the existing API routes and controllers. Filament handles the admin panel only.

```php
// routes/api.php - Keep existing API resource routes
// Controllers in app/Http/Controllers/API/ - Keep as-is
```

---

## Implementation Order (Step by Step)

### Phase 1: Foundation
1. Create fresh Laravel 11 project
2. Install Filament v3 and plugins
3. Run all migrations
4. Create admin user
5. Configure Filament panel (logo, colors, navigation groups)

### Phase 2: Core Settings
6. LanguageResource
7. SettingResource (as custom page)
8. Setup localization middleware

### Phase 3: Catalog & Base Entities
9. CategoryResource (with translatable)
10. ProductResource
11. ClientResource
12. WorkerResource
13. SupplierResource (with translatable)

### Phase 4: Project Management (Core)
14. PaymentMethodResource (with translatable)
15. ProjectResource with PaymentsRelationManager and ImagesRelationManager
16. TaskResource with WorkersRelationManager, PurchaseTasksRelationManager, ImagesRelationManager
17. ExpenseCategoryResource
18. ExpenseResource

### Phase 5: CMS
19. StaticPageResource
20. SliderResource
21. ContactUsResource

### Phase 6: Dashboard & Reports
22. Dashboard widgets (Stats, Charts)
23. Income Report page
24. Suppliers Report page
25. Workers Report page
26. Clients Report page

### Phase 7: Access Control
27. Install & configure Filament Shield
28. Define permissions per resource
29. Assign roles

### Phase 8: Branch Management
30. BranchResource (with translatable)

### Phase 9: Polish
31. Test all CRUD operations
32. Test all relations
33. Test translatable fields
34. Test file uploads
35. Test reports with real data
36. Migrate existing data from old database

---

## Data Migration Strategy

```php
// Create a command to migrate data from old DB to new:
// php artisan app:migrate-old-data

// 1. Export old DB
// 2. Map old table structures to new
// 3. Handle JSON translatable fields conversion
// 4. Handle file paths (copy uploaded files)
// 5. Re-create polymorphic relationships
// 6. Verify data integrity
```

---

## Key Differences from Original

| Feature | Original | Filament |
|---------|----------|----------|
| UI Framework | AdminLTE + Bootstrap | Filament (Tailwind) |
| DataTables | Yajra DataTables | Filament Tables (built-in) |
| Forms | Blade + jQuery | Filament Forms (Livewire) |
| CRUD Generator | InfyOm Generator | `make:filament-resource` |
| Roles/Permissions | Custom Role/Permission models | Filament Shield (Spatie Permission) |
| Livewire Components | Custom (IncomeStats, etc.) | Filament Widgets + Custom Pages |
| File Uploads | Custom FileUpload helper | Filament FileUpload component |
| Translations | Spatie Translatable + manual | Filament Translatable Plugin |
| Charts | Custom JS charts | Filament Chart Widgets |
