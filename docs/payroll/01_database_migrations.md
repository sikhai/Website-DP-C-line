# 01. Tạo bảng database migrations

## 1. Tạo migration

Chạy các lệnh sau:

```bash
php artisan make:migration create_payrolls_table
php artisan make:migration create_payroll_allowances_table
php artisan make:migration create_payroll_overtimes_table
php artisan make:migration create_payroll_bonuses_table
php artisan make:migration create_business_trips_table
php artisan make:migration create_business_trip_advances_table
php artisan make:migration create_payroll_payment_attachments_table
```

---

# 2. Bảng `payrolls`

Bảng này lưu dữ liệu tổng hợp lương của từng nhân viên theo tháng.

```php
Schema::create('payrolls', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->unsignedTinyInteger('month');
    $table->unsignedSmallInteger('year');

    $table->decimal('base_salary', 15, 2)->default(0);
    $table->decimal('total_working_days', 5, 2)->default(0);
    $table->decimal('actual_working_days', 5, 2)->default(0);

    $table->decimal('official_work_salary', 15, 2)->default(0);
    $table->decimal('hourly_salary', 15, 2)->default(0);

    $table->decimal('allowance_total', 15, 2)->default(0);
    $table->decimal('overtime_total', 15, 2)->default(0);
    $table->decimal('bonus_total', 15, 2)->default(0);

    // Công tác phí chỉ báo cáo, không cộng vào lương.
    // Nếu cần hiển thị báo cáo nhanh, có thể lưu tổng đã cấp hoặc tổng thực chi ở bảng riêng/report query.

    $table->decimal('gross_salary', 15, 2)->default(0);

    $table->text('note')->nullable();
    $table->string('status')->default('draft');

    $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('confirmed_at')->nullable();

    $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('paid_at')->nullable();

    $table->timestamps();

    $table->unique(['user_id', 'month', 'year']);
});
```

---

# 3. Bảng `payroll_allowances`

Lưu các khoản hỗ trợ như xăng xe, ăn uống, điện thoại.

```php
Schema::create('payroll_allowances', function (Blueprint $table) {
    $table->id();

    $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();

    $table->string('name');
    $table->decimal('amount', 15, 2)->default(0);
    $table->text('note')->nullable();

    $table->timestamps();
});
```

---

# 4. Bảng `payroll_overtimes`

Lưu từng dòng tăng ca để dễ kiểm tra và audit.

```php
Schema::create('payroll_overtimes', function (Blueprint $table) {
    $table->id();

    $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();

    $table->string('type'); // normal, sunday, holiday
    $table->decimal('hours', 8, 2)->default(0);
    $table->decimal('multiplier', 5, 2)->default(1);
    $table->decimal('hourly_salary', 15, 2)->default(0);
    $table->decimal('amount', 15, 2)->default(0);

    $table->text('note')->nullable();

    $table->timestamps();
});
```

---

# 5. Bảng `payroll_bonuses`

Lưu các khoản thưởng.

```php
Schema::create('payroll_bonuses', function (Blueprint $table) {
    $table->id();

    $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();

    $table->string('name');
    $table->decimal('amount', 15, 2)->default(0);
    $table->text('note')->nullable();

    $table->timestamps();
});
```

---

# 6. Bảng `business_trips`

Bảng này chỉ dùng để quản lý và báo cáo công tác phí. Không cộng vào lương.

```php
Schema::create('business_trips', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete();

    $table->string('title');
    $table->string('destination')->nullable();

    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();

    $table->decimal('estimated_amount', 15, 2)->default(0);
    $table->decimal('total_advanced_amount', 15, 2)->default(0);
    $table->decimal('actual_spent_amount', 15, 2)->default(0);
    $table->decimal('difference_amount', 15, 2)->default(0);

    $table->string('status')->default('pending');

    $table->timestamp('employee_confirmed_at')->nullable();
    $table->timestamp('accountant_confirmed_at')->nullable();

    $table->text('note')->nullable();

    $table->timestamps();
});
```

Giải thích `difference_amount`:

```txt
difference_amount = actual_spent_amount - total_advanced_amount
```

- Nếu `difference_amount > 0`: công ty cần trả thêm cho nhân viên.
- Nếu `difference_amount < 0`: nhân viên còn thừa tiền cần hoàn lại.
- Nhưng khoản này chỉ dùng để báo cáo, không cộng vào `gross_salary`.

---

# 7. Bảng `business_trip_advances`

Lưu từng lần kế toán cấp tiền công tác.

```php
Schema::create('business_trip_advances', function (Blueprint $table) {
    $table->id();

    $table->foreignId('business_trip_id')->constrained()->cascadeOnDelete();

    $table->decimal('amount', 15, 2)->default(0);
    $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('confirmed_at')->nullable();

    $table->text('note')->nullable();

    $table->timestamps();
});
```

---

# 8. Bảng `payroll_payment_attachments`

Lưu hình ảnh chứng từ khi kế toán xác nhận đã thanh toán lương.

```php
Schema::create('payroll_payment_attachments', function (Blueprint $table) {
    $table->id();

    $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();

    $table->string('disk')->default('public');
    $table->string('path');
    $table->string('original_name')->nullable();
    $table->string('mime_type')->nullable();
    $table->unsignedBigInteger('size')->nullable();

    $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
});
```

## Lưu ý validation

Khi chuyển `payrolls.status` sang `paid`, hệ thống bắt buộc phải có ít nhất 1 ảnh trong `payroll_payment_attachments`.

Không nên chỉ validate ở frontend. Phải validate ở backend trong service hoặc form request.
