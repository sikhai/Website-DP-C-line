# 02. Tạo Models và Relationships

## 1. Tạo model

```bash
php artisan make:model Payroll
php artisan make:model PayrollAllowance
php artisan make:model PayrollOvertime
php artisan make:model PayrollBonus
php artisan make:model BusinessTrip
php artisan make:model BusinessTripAdvance
php artisan make:model PayrollPaymentAttachment
```

---

# 2. `User.php`

File: `app/Models/User.php`

```php
public function payrolls()
{
    return $this->hasMany(Payroll::class);
}

public function businessTrips()
{
    return $this->hasMany(BusinessTrip::class);
}
```

---

# 3. `Payroll.php`

File: `app/Models/Payroll.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'base_salary',
        'total_working_days',
        'actual_working_days',
        'official_work_salary',
        'hourly_salary',
        'allowance_total',
        'overtime_total',
        'bonus_total',
        'gross_salary',
        'note',
        'status',
        'confirmed_by',
        'confirmed_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_working_days' => 'decimal:2',
        'actual_working_days' => 'decimal:2',
        'official_work_salary' => 'decimal:2',
        'hourly_salary' => 'decimal:2',
        'allowance_total' => 'decimal:2',
        'overtime_total' => 'decimal:2',
        'bonus_total' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allowances()
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    public function overtimes()
    {
        return $this->hasMany(PayrollOvertime::class);
    }

    public function bonuses()
    {
        return $this->hasMany(PayrollBonus::class);
    }

    public function businessTrips()
    {
        return $this->hasMany(BusinessTrip::class);
    }

    public function paymentAttachments()
    {
        return $this->hasMany(PayrollPaymentAttachment::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['confirmed', 'paid'], true);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
```

---

# 4. `PayrollAllowance.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAllowance extends Model
{
    protected $fillable = [
        'payroll_id',
        'name',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
```

---

# 5. `PayrollOvertime.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollOvertime extends Model
{
    protected $fillable = [
        'payroll_id',
        'type',
        'hours',
        'multiplier',
        'hourly_salary',
        'amount',
        'note',
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'hourly_salary' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
```

---

# 6. `PayrollBonus.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBonus extends Model
{
    protected $fillable = [
        'payroll_id',
        'name',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
```

---

# 7. `BusinessTrip.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTrip extends Model
{
    protected $fillable = [
        'user_id',
        'payroll_id',
        'title',
        'destination',
        'start_date',
        'end_date',
        'estimated_amount',
        'total_advanced_amount',
        'actual_spent_amount',
        'difference_amount',
        'status',
        'employee_confirmed_at',
        'accountant_confirmed_at',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_amount' => 'decimal:2',
        'total_advanced_amount' => 'decimal:2',
        'actual_spent_amount' => 'decimal:2',
        'difference_amount' => 'decimal:2',
        'employee_confirmed_at' => 'datetime',
        'accountant_confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function advances()
    {
        return $this->hasMany(BusinessTripAdvance::class);
    }
}
```

---

# 8. `BusinessTripAdvance.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTripAdvance extends Model
{
    protected $fillable = [
        'business_trip_id',
        'amount',
        'confirmed_by',
        'confirmed_at',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
```

---

# 9. `PayrollPaymentAttachment.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPaymentAttachment extends Model
{
    protected $fillable = [
        'payroll_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
```
