# REMAINING AUDIT FIXES - ISSUES 5-14

## Overview
This document outlines the 10 remaining issues from the production-ready audit, expected completion by Issue 14 will address all 14 critical gaps identified in the comprehensive security and architecture review.

## Issue 5: N+1 Query Prevention & Optimization

### Current Status: Not Started
### Priority: HIGH (Performance)
### Estimated Time: 8-10 hours

### Problem
Multiple queries running in loops instead of eager loading - common in dashboard calculations and list views.

### Key Areas to Fix
1. **MealController@index** - List all meals with user and month data
2. **CalculationService** - getPerMemberBalance() loops through meals
3. **ReportController** - Summary calculations with multiple queries
4. **Dashboard** - Statistics requiring multiple database hits

### Implementation Strategy
```php
// ❌ Before (N+1)
$meals = Meal::all(); // 1 query
foreach ($meals as $meal) {
    $user = $meal->user->name; // N queries
    $month = $meal->month->name; // N queries
}

// ✅ After (Eager Loading)
$meals = Meal::with('user', 'month')->get(); // 1 query
foreach ($meals as $meal) {
    $user = $meal->user->name; // No additional queries
}
```

### Required Changes
- Add `:with()` clauses to all index/show queries
- Refactor calculations to use lazy collection methods
- Create database indexes on foreign keys
- Monitor with Debugbar in development

## Issue 6: Caching Strategy

### Current Status: Not Started
### Priority: HIGH (Performance)
### Estimated Time: 6-8 hours

### Problem
Dashboard and reports recalculate summaries on every request despite stable monthly data.

### Caching Targets
1. **Monthly Summaries** - Cache total expenses, balance by member
2. **Dashboard Stats** - Total meals, expenses, pending items
3. **Member Balances** - Per-member calculations (invalidate on transaction)
4. **Report Data** - Historic month summaries (invalidate on month close)

### Implementation Approach
```php
// Cache monthly totals
Cache::remember('month:' . $monthId . ':total_meals', 24*60, function() {
    return Meal::whereMess_id($messId)
        ->whereMonth_id($monthId)
        ->count();
});

// Invalidate on new meal
public function storeMeal(Request $request) {
    $meal = Meal::create($request->validated());
    Cache::tags(['month:' . $meal->month_id])->flush();
}
```

### Configuration
- Default driver: Redis (recommended) or file-based for small deployments
- TTL: 24 hours for stable data, 1 hour for dashboard stats
- Invalidation: Automatic on CRUD operations via observer

## Issue 7: Health Checks & Monitoring Endpoints

### Current Status: Not Started
### Priority: HIGH (Operations)
### Estimated Time: 4-6 hours

### Problem
No way to verify application health during deployments or troubleshoot issues.

### Required Endpoints
```
GET /health - Overall health status (200/503)
GET /health/database - Database connectivity
GET /health/redis - Cache/queue connectivity
GET /health/migrations - Database schema status
GET /health/disk - Disk space availability
```

### Response Format
```json
{
  "status": "healthy",
  "timestamp": "2026-06-18T12:00:00Z",
  "services": {
    "database": {"status": "up", "response_time_ms": 5},
    "redis": {"status": "up", "response_time_ms": 2},
    "disk": {"status": "ok", "free_gb": 50}
  }
}
```

### Implementation
- Create `HealthController` with status checks
- Add to public routes (unauthenticated)
- Integrate with monitoring systems (DataDog, New Relic, etc.)

## Issue 8: Backup Strategy & Disaster Recovery

### Current Status: Not Started
### Priority: HIGH (Operations)
### Estimated Time: 4-6 hours

### Problem
No automated backup procedures or disaster recovery plan documented.

### Required Components
1. **Database Backups**
   - Daily incremental + weekly full
   - 30-day retention policy
   - Encrypted storage (S3, GCS, etc.)

2. **File Backups**
   - User uploads directory
   - Configuration files
   - Same retention as database

3. **Backup Verification**
   - Test restores monthly
   - Document procedures
   - Track backup size/duration

### Implementation
```bash
# Daily backup schedule
php artisan backup:run

# Restore from backup
php artisan backup:restore --date=2026-06-17

# Verify backup integrity
php artisan backup:monitor
```

### Storage Options
- Local filesystem (for testing only)
- Amazon S3 (recommended)
- Google Cloud Storage
- Azure Blob Storage

## Issue 9: Invitation Expiration

### Current Status: Not Started
### Priority: MEDIUM (Features)
### Estimated Time: 3-4 hours

### Problem
Invitations have no expiration - users can accept invites months later.

### Implementation
1. Add `expires_at` column to `mess_user` migration
2. Add validation to prevent accepting expired invites
3. Add scheduled command to clean up old invitations
4. Email users before expiration (optional)

### Code Changes
```php
// Migration
Schema::table('mess_user', function (Blueprint $table) {
    $table->timestamp('expires_at')->after('status');
});

// Model validation
public function isExpired(): bool {
    return $this->expires_at < now();
}

// Acceptance validation
if ($messUser->isExpired()) {
    throw new ExpiredInvitationException();
}
```

## Issue 11: Month Archiving with Soft Deletes

### Current Status: Not Started
### Priority: MEDIUM (Features)
### Estimated Time: 6-8 hours

### Problem
Closed months remain visible in lists, historical data not clearly separated.

### Implementation
1. Add `soft_delete` to Month model
2. Migration to add `deleted_at` column
3. Update queries to include archived months in reports
4. Create UI toggle for viewing archived months

### Code Changes
```php
// Model
class Month extends Model {
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}

// Archive a month
$month->delete(); // Sets deleted_at, doesn't remove

// Query archived
Month::onlyTrashed()->get(); // Only archived
Month::withTrashed()->get(); // All months
```

## Issue 12: Month Closure Warnings & Validations

### Current Status: Not Started
### Priority: MEDIUM (Features)
### Estimated Time: 4-5 hours

### Problem
Users can close months with missing data or unpaid balances without warnings.

### Pre-Closure Checks
1. All members have meals recorded
2. All expenses are assigned
3. No pending deposits
4. All balances are settled or explicitly marked unpaid

### Implementation
```php
class MonthClosureValidator {
    public function validate(Month $month): array {
        $warnings = [];
        
        if ($month->meals()->count() === 0) {
            $warnings[] = "No meals recorded";
        }
        
        if ($month->hasMembersWithoutMeals()) {
            $warnings[] = "Members with no meals recorded";
        }
        
        if ($month->hasPendingDeposits()) {
            $warnings[] = "Pending deposits waiting approval";
        }
        
        if ($month->hasUnpaidBalances()) {
            $warnings[] = "Unpaid member balances";
        }
        
        return $warnings;
    }
}
```

## Issue 13: Report Export Enhancements

### Current Status: Not Started
### Priority: MEDIUM (Features)
### Estimated Time: 6-8 hours

### Problem
Reports only support PDF, users need Excel/CSV for analysis and sharing.

### Export Formats
1. **Excel (.xlsx)** - Formatted tables, charts, formulas
2. **CSV (.csv)** - Spreadsheet import, basic formatting
3. **JSON (.json)** - API integration, data export
4. **PDF** - Existing, keep as-is

### Implementation
```php
class ReportExporter {
    public function toExcel(Month $month): StreamedResponse
    // Uses Laravel Excel (Maatwebsite\Excel)
    
    public function toCsv(Month $month): StreamedResponse
    // Simple CSV stream
    
    public function toJson(Month $month): JsonResponse
    // API-compatible JSON
    
    public function toPdf(Month $month): StreamedResponse
    // Existing implementation
}
```

### Package Dependencies
- `maatwebsite/laravel-excel` for Excel export
- Built-in PHP for CSV generation
- Existing dompdf for PDF

## Issue 14: Cross-Tenant Leak Prevention Audit

### Current Status: Not Started
### Priority: HIGH (Security)
### Estimated Time: 4-6 hours

### Problem
Verify that all data access is properly scoped to active tenant.

### Audit Checklist
1. **Controller Actions**
   - [ ] All index() queries use with('user', 'mess')
   - [ ] All show() queries validate ownership
   - [ ] All store/update/delete validate tenant context
   - [ ] Forbidden actions return 403, not 404

2. **Database Queries**
   - [ ] All queries include mess_id WHERE clause (or use scope)
   - [ ] No raw SQL queries without tenant filtering
   - [ ] All relationships eager loaded

3. **API Endpoints**
   - [ ] All routes require authentication
   - [ ] All endpoints validated for tenant isolation
   - [ ] Rate limiting on sensitive endpoints

4. **Test Coverage**
   - [ ] Cross-tenant access attempts return 403
   - [ ] User A cannot see User B's data
   - [ ] Superadmin bypass works correctly

### Implementation
```php
// Test case pattern
public function test_user_cannot_view_other_mess_meals() {
    $user1Mess = Mess::factory()->create();
    $user2Mess = Mess::factory()->create();
    $meal = Meal::factory()->create(['mess_id' => $user1Mess->id]);
    
    $user2 = User::factory()->create();
    MessUser::create(['user_id' => $user2->id, 'mess_id' => $user2Mess->id]);
    
    $this->actingAs($user2)
        ->get("/meals/{$meal->id}")
        ->assertForbidden();
}
```

## Implementation Priority & Timeline

### Week 1 (Issues 5-7)
- [ ] Issue 5: N+1 Query Prevention (6 hours)
- [ ] Issue 6: Caching Strategy (6 hours)
- [ ] Issue 7: Health Checks (4 hours)
- **Total**: 16 hours

### Week 2 (Issues 8-14)
- [ ] Issue 8: Backup Strategy (4 hours)
- [ ] Issue 9: Invitation Expiration (3 hours)
- [ ] Issue 11: Month Archiving (6 hours)
- [ ] Issue 12: Closure Warnings (4 hours)
- [ ] Issue 13: Export Enhancements (6 hours)
- [ ] Issue 14: Leak Prevention Audit (4 hours)
- **Total**: 27 hours

### Estimated Total: ~43 hours for Issues 5-14

## Testing Strategy

Each issue should include:
1. Unit tests for business logic
2. Feature tests for endpoints
3. Integration tests for cross-system interactions
4. Performance tests for optimized queries

## Success Criteria

- [x] All 14 issues have implementation plans
- [ ] Issues 5-8 completed and tested (Week 1)
- [ ] Issues 9-14 completed and tested (Week 2)
- [ ] Full test coverage > 80%
- [ ] All security issues resolved
- [ ] Performance baselines established
- [ ] Deployment checklist completed

---

**Next Action**: Begin Issue 5 implementation after Issues 1-4 are fully validated with running test suite.
