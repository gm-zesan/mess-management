# 🛡️ Critical Edge Cases - Validation Reference

## Overview
This document maps all 4 critical edge cases to their implementation locations for quick reference and testing.

---

## ✅ CASE 1: Meal = 0 → Divide by Zero Prevention

### What it does
Prevents `division by zero` error when calculating meal rate with 0 meals recorded.

### Implementation Location
📍 **File:** `app/Services/CalculationService.php`  
📍 **Method:** `getMealRate($month): float`  
📍 **Lines:** 37-47

```php
public function getMealRate($month): float
{
    $totalMeals = $this->getTotalMeals($month);
    
    if ($totalMeals == 0) {
        return 0;  // ✅ Returns 0 instead of dividing by zero
    }

    $totalExpenses = $this->getTotalExpenses($month);
    return round($totalExpenses / $totalMeals, 2);
}
```

### How it works
1. Gets total meals count
2. **IF meals = 0**, returns 0 (safe value)
3. **IF meals > 0**, divides expenses by meals normally

### Test Coverage
- **File:** `tests/Feature/EdgeCasesTest.php`
- **Test:** `test_zero_meals_does_not_cause_divide_by_zero()`

### Result
✅ **Pass:** Meal rate = 0, no errors  
❌ **Fail:** Division by zero error, crash

---

## ✅ CASE 2: No Deposit → Negative Balance

### What it does
Correctly calculates and displays negative balances when member has no deposits but has meal costs.

### Implementation Location
📍 **File:** `app/Services/CalculationService.php`  
📍 **Method:** `getPerMemberBalance($month): array`  
📍 **Lines:** 101-107

```php
// Calculate balance (deposit - cost)
$balance = $memberDeposit - $mealCost;

$balances[$member->id] = [
    'member_id' => $member->id,
    'member_name' => $member->name,
    'total_meals' => $memberMeals,
    'meal_rate' => $mealRate,
    'meal_cost' => round($mealCost, 2),
    'total_deposit' => round($memberDeposit, 2),
    'balance' => round($balance, 2),
    'status' => $balance > 0 ? 'credit' : ($balance < 0 ? 'due' : 'settled'),
];
```

### Status Logic
- `$balance > 0` → **'credit'** (member has overpaid)
- `$balance < 0` → **'due'** (member owes money)
- `$balance == 0` → **'settled'** (balanced)

### Test Coverage
- **File:** `tests/Feature/EdgeCasesTest.php`
- **Test:** `test_negative_balance_without_deposit()`

### Result
✅ **Pass:** Negative balance calculated, status = 'due'  
❌ **Fail:** Incorrect balance or status

---

## ❌ CASE 3: Closed Month Edit Prevention

### What it does
Prevents ANY modifications (create, update, delete) to data in closed months.

### Implementation - 3 Layers

#### Layer 1: Database Model Method
📍 **File:** `app/Models/Month.php`  
📍 **Method:** `isClosed(): bool`

```php
public function isClosed(): bool
{
    return $this->closed_at !== null;
}
```

#### Layer 2: Validation Rule
📍 **File:** `app/Rules/MonthNotClosed.php`  
📍 **Method:** `validate()`

```php
public function validate(string $attribute, mixed $value, Closure $fail): void
{
    $monthId = $this->monthId ?? $value;
    
    if (!$monthId) {
        return;
    }

    $month = Month::find($monthId);

    if ($month && $month->isClosed()) {
        $fail("The {$attribute} is closed. No further modifications are allowed.");
    }
}
```

#### Layer 3: Form Requests
📍 **File:** `app/Http/Requests/StoreMealRequest.php`  
📍 **Line:** 28

```php
'month_id' => ['required', 'exists:months,id', new MonthNotClosed()],
```

#### Layer 4: Controller Checks
📍 **File:** `app/Http/Controllers/MealController.php`

**store() method (line 39):**
```php
if ($activeMonth->isClosed()) {
    return redirect()->back()
        ->with('error', 'This month is closed. No further modifications are allowed.');
}
```

**update() method (line 84):**
```php
if ($meal->month->isClosed()) {
    return redirect()->back()
        ->with('error', 'This month is closed. No further modifications are allowed.');
}
```

**destroy() method (line 109):**
```php
if (isMonthClosed($meal->month_id)) {
    return redirect()->back()
        ->with('error', 'This month is closed. No further deletions are allowed.');
}
```

### Test Coverage
- **Test 1:** `test_closed_month_prevents_meal_creation()` - ❌ Blocks meal creation
- **Test 2:** `test_closed_month_prevents_edit_and_delete()` - ❌ Blocks meal editing & deletion
- **Test 3:** `test_closed_month_prevents_expense_creation()` - ❌ Blocks expense creation
- **Test 4:** `test_closed_month_prevents_deposit_creation()` - ❌ Blocks deposit creation

### How Closing Works
📍 **File:** `app/Http/Controllers/MonthController.php`  
📍 **Method:** `close(Month $month, MonthService $monthService)`

```php
public function close(Month $month, MonthService $monthService)
{
    $monthService->closeMonth($month);
    return redirect()->route('months.show', $month)
        ->with('success', 'Month closed successfully.');
}
```

### Result
✅ **Pass:** All operations blocked with error messages  
❌ **Fail:** Data modified in closed month, corruption

---

## ❌ CASE 4: Duplicate Meal Prevention

### What it does
Prevents duplicate meal entries (same member, same date, same month).

### Implementation - 2 Layers

#### Layer 1: Database Unique Constraint
📍 **File:** `database/migrations/2026_03_31_195451_create_meals_table.php`

```php
$table->unique(['member_id', 'date', 'month_id']);
```

#### Layer 2: Validation Rule
📍 **File:** `app/Http/Requests/StoreMealRequest.php`  
📍 **Lines:** 23-31

```php
'date' => [
    'required',
    'date',
    Rule::unique('meals')
        ->where('member_id', $this->member_id)
        ->where('month_id', $this->month_id)
        ->ignore($this->meal ?? null),  // For updates
],
```

#### Error Message
📍 **File:** `app/Http/Requests/StoreMealRequest.php`  
📍 **Line:** 44

```php
'date.unique' => 'This member already has a meal entry for this date.',
```

### Test Coverage
- **File:** `tests/Feature/EdgeCasesTest.php`
- **Test:** `test_duplicate_meal_entry_prevented()`

### How it works
1. User attempts to create meal for John Doe on 2026-04-01
2. System checks if meal already exists with:
   - `member_id` = John (123)
   - `date` = 2026-04-01
   - `month_id` = April (1)
3. **IF exists:** Shows error "This member already has a meal entry for this date."
4. **IF NOT exists:** Creates meal record

### Result
✅ **Pass:** Duplicate prevented, validation error shown  
❌ **Fail:** Duplicates created, data inconsistency

---

## 🧪 Running All Tests

### Run Full Test Suite
```bash
php artisan test tests/Feature/EdgeCasesTest.php
```

### Run Specific Edge Case
```bash
# Test 1: Divide by zero
php artisan test --filter=test_zero_meals_does_not_cause_divide_by_zero

# Test 2: Negative balance
php artisan test --filter=test_negative_balance_without_deposit

# Test 3: Closed month
php artisan test --filter=test_closed_month_prevents_meal_creation

# Test 4: Duplicate meal
php artisan test --filter=test_duplicate_meal_entry_prevented
```

### Run with Coverage Report
```bash
php artisan test tests/Feature/EdgeCasesTest.php --coverage
```

---

## 📊 Quick Status Check

| Edge Case | Status | Location | Test |
|-----------|--------|----------|------|
| Meal = 0 | ✅ Implemented | `CalculationService.getMealRate()` | `test_zero_meals_does_not_cause_divide_by_zero` |
| No Deposit | ✅ Implemented | `CalculationService.getPerMemberBalance()` | `test_negative_balance_without_deposit` |
| Closed Month | ❌ Blocked | `MonthNotClosed`, Controllers | `test_closed_month_prevents_*` |
| Duplicate Meal | ❌ Blocked | `StoreMealRequest`, DB Constraint | `test_duplicate_meal_entry_prevented` |

---

## 🚀 Production Checklist

Before deploying to production, verify:

- [ ] All edge case tests pass: `php artisan test`
- [ ] Zero meals monthly report displays correctly
- [ ] Negative balances show 'due' status correctly
- [ ] Closed month prevents all operations
- [ ] Duplicate meals cannot be created
- [ ] Error messages are user-friendly
- [ ] Database constraint and validation rule both active
- [ ] Helper functions imported and working (`activeMonth()`, `isMonthClosed()`)
