# خطة تحسين قاعدة بيانات عيادة الأسنان

## الملخص
تحسين تصميم قاعدة البيانات بناءً على المراجعة. تشمل الخطة 7 تحسينات مرتبة حسب الأولوية.

---

## 1. إضافة `doctor_id` في `ORTHODONTIC_CASES`

> [!CAUTION]
> حالياً لا يوجد ربط بين حالة التقويم والطبيب المعالج

#### [MODIFY] [create_orthodontic_cases_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_23_185128_create_orthodontic_cases_table.php)

```diff
 $table->uuid('patient_id');
 $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');
+
+$table->uuid('doctor_id');
+$table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('restrict');
```

---

## 2. إضافة `invoice_number` في `INVOICES`

#### [MODIFY] [create_invoices_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_27_002334_create_invoices_table.php)

```diff
 $table->uuid('id')->primary();
+$table->string('invoice_number')->unique();
 $table->decimal('total_amount', 12, 2)->default(0);
```

---

## 3. إضافة `notes` في `PAYMENTS`

#### [MODIFY] [create_payments_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_27_002443_create_payments_table.php)

```diff
 $table->decimal('exchange_rate',12,6);
+$table->text('notes')->nullable();
 $table->timestamps();
```

---

## 4. إضافة `full_name` في `DOCTORS`

#### [MODIFY] [create_doctors_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_20_032830_create_doctors_table.php)

```diff
 $table->uuid('id')->primary();
+$table->string('full_name');
 $table->string('phone')->nullable();
```

---

## 5. إضافة `category` في `SERVICES`

#### [MODIFY] [create_services_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_19_224324_create_services_table.php)

```diff
 $table->string('name');
 $table->text('description')->nullable();
+$table->enum('category', [
+    'radiology',
+    'orthodontics',
+    'filling',
+    'cleaning',
+    'extraction',
+    'cosmetic',
+    'consultation',
+    'other'
+])->default('other');
 $table->decimal('price',12,2);
```

---

## 6. إضافة جدول `TREATMENTS` (السجلات الطبية العامة)

> [!IMPORTANT]
> هذا الجدول يغطي العلاجات التي لا تندرج تحت التقويم أو الأشعة

#### [NEW] [create_treatments_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_27_103000_create_treatments_table.php)

```php
Schema::create('treatments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->text('diagnosis')->nullable();
    $table->text('treatment_plan')->nullable();
    $table->text('notes')->nullable();
    $table->string('status')->default('in_progress');
    $table->timestamps();
    $table->softDeletes();

    $table->uuid('patient_id');
    $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');

    $table->uuid('doctor_id');
    $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('restrict');

    $table->uuid('service_id');
    $table->foreign('service_id')->references('id')->on('services')->onDelete('restrict');

    $table->uuid('appointment_id')->nullable();
    $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
});
```

---

## 7. تحسين إدارة المخزون بجدول حركات `STOCK_MOVEMENTS`

> [!NOTE]
> بدلاً من تعديل الكمية مباشرة، نتتبع كل حركة دخول/خروج

#### [NEW] [create_stock_movements_table.php](file:///root/webApps/dental-clinic/admin-panel/database/migrations/2026_02_27_104000_create_stock_movements_table.php)

```php
Schema::create('stock_movements', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->enum('type', ['in', 'out', 'adjustment']);
    $table->integer('quantity');
    $table->text('reason')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // يمكن ربطه بصنف صيدلية أو مستودع
    $table->uuid('pharmacy_batch_id')->nullable();
    $table->foreign('pharmacy_batch_id')->references('id')->on('pharmacy_batches')->onDelete('restrict');

    $table->uuid('warehouse_item_id')->nullable();
    $table->foreign('warehouse_item_id')->references('id')->on('warehouse_items')->onDelete('restrict');
});
```

---

## ترتيب الأولوية

| # | التحسين | الأهمية | الجهد |
|---|---------|---------|-------|
| 1 | `doctor_id` → orthodontic_cases | 🔴 عالية | منخفض |
| 2 | `invoice_number` → invoices | 🔴 عالية | منخفض |
| 3 | `notes` → payments | 🟡 متوسطة | منخفض |
| 4 | `full_name` → doctors | 🟡 متوسطة | منخفض |
| 5 | `category` → services | 🟡 متوسطة | منخفض |
| 6 | جدول treatments | 🟠 عالية | متوسط |
| 7 | جدول stock_movements | 🟡 متوسطة | متوسط |
