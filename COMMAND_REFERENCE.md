# IMPLEMENTATION COMMAND REFERENCE

Quick reference for all commands needed to validate, deploy, and monitor the audit fixes.

## Prerequisites

```bash
# Verify environment
php --version  # Should be 8.2+
composer --version
node --version  # For asset building

# Install dependencies
composer install
npm install
```

## Development & Testing

### Run Tests

```bash
# All tests
./vendor/bin/pest

# Specific test files
./vendor/bin/pest tests/Feature/Auth/LoginRateLimitingTest.php
./vendor/bin/pest tests/Feature/Tenancy/TenantIsolationTest.php
./vendor/bin/pest tests/Feature/Audit/AuditLoggingTest.php

# With coverage report
./vendor/bin/pest --coverage

# Specific test method
./vendor/bin/pest --filter="test_user_can_login"

# Watch mode (re-run on file change)
./vendor/bin/pest --watch
```

### Database Migrations

```bash
# Create tables for Issues 1-4
php artisan migrate

# In testing environment
php artisan migrate --env=testing

# Dry-run (preview without executing)
php artisan migrate --dry-run

# Rollback migrations
php artisan migrate:rollback
php artisan migrate:rollback --step=2

# Reset entire database
php artisan migrate:fresh
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Code Quality

```bash
# Static analysis
./vendor/bin/phpstan analyse app/

# Code formatting (PSR-12)
./vendor/bin/pint app/

# Check code without fixing
./vendor/bin/pint --test

# Laravel best practices
php artisan tinker
# Then review code in editor

# Check config
php artisan config:cache --dry-run
```

### Manual Testing

```bash
# Start Laravel development server
php artisan serve

# Interactive shell
php artisan tinker

# Check scheduled commands
php artisan schedule:list

# Run specific command
php artisan auth:cleanup-login-attempts

# Check current configuration
php artisan config:show auth.login_attempts
```

## Configuration Management

### Environment Setup

```bash
# Copy example env file
cp .env.example .env

# Generate application key
php artisan key:generate

# Add these to .env
echo "LOGIN_ATTEMPTS_ENABLED=true" >> .env
echo "SESSION_ENCRYPT=true" >> .env
echo "SESSION_SECURE_COOKIE=true" >> .env

# Verify configuration
php artisan config:cache

# Clear configuration cache
php artisan config:clear
```

### Database Configuration

```bash
# Show current DB driver
php artisan config:show database.default

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo()
>>> exit

# Monitor database size
# For MySQL:
mysql -u root -p -e "SELECT table_name, 
  ROUND(((data_length + index_length) / 1024 / 1024), 2) 'Size in MB' 
  FROM information_schema.TABLES 
  WHERE table_schema = 'mess_management' 
  ORDER BY (data_length + index_length) DESC;"
```

## Audit Log Queries

### Monitor Login Attempts

```bash
# Via Laravel Tinker
php artisan tinker

# View recent failed logins
>>> App\Models\LoginAttempt::where('successful', false)->latest(5)->get()

# Check if email is locked out
>>> App\Models\LoginAttempt::isLockedOut('user@example.com')

# Check if IP is locked out
>>> App\Models\LoginAttempt::isIpLockedOut('192.168.1.1')

# Get lockout time remaining
>>> App\Models\LoginAttempt::getLockoutTimeRemaining('user@example.com')

# Clear lockout manually
>>> App\Models\LoginAttempt::where('email', 'user@example.com')->delete()

# Exit tinker
>>> exit
```

### Monitor Audit Logs

```bash
# Via MySQL directly
mysql -u root -p mess_management -e "
  SELECT action, COUNT(*) as count 
  FROM audit_logs 
  GROUP BY action 
  ORDER BY count DESC;"

# View failed login attempts
mysql -u root -p mess_management -e "
  SELECT created_at, description 
  FROM audit_logs 
  WHERE action = 'login_attempt' AND status = 'failed' 
  ORDER BY created_at DESC LIMIT 10;"

# View audit trail for specific user
mysql -u root -p mess_management -e "
  SELECT action, description, created_at 
  FROM audit_logs 
  WHERE user_id = 1 
  ORDER BY created_at DESC LIMIT 20;"

# Check table size
mysql -u root -p mess_management -e "
  SELECT 
    ROUND(((data_length + index_length) / 1024 / 1024), 2) 'Size in MB',
    TABLE_ROWS 'Rows'
  FROM information_schema.TABLES 
  WHERE table_name = 'audit_logs';"
```

## Deployment Commands

### Pre-Deployment Checklist

```bash
# Verify all tests pass
./vendor/bin/pest

# Check for modified files
git status

# Review changes
git diff

# Verify database migrations
php artisan migrate:status

# Build assets
npm run build

# Check for errors
php artisan config:cache

# Verify scheduled commands
php artisan schedule:list
```

### Deployment Steps

```bash
# 1. Backup database (your method here)
# mysqldump -u root -p mess_management > backup-2026-06-18.sql

# 2. Pull latest code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Clear caches (important!)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 5. Run migrations
php artisan migrate --force

# 6. Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Restart queue workers (if applicable)
php artisan queue:restart

# 8. Verify deployment
php artisan tinker
>>> DB::connection()->getPdo()
>>> exit
```

### Post-Deployment Validation

```bash
# Check audit logs exist
mysql -u root -p mess_management -e "SELECT COUNT(*) FROM audit_logs;"

# Check login attempts tracking works
mysql -u root -p mess_management -e "SELECT COUNT(*) FROM login_attempts;"

# Verify schedule is running
ps aux | grep schedule:work

# Check recent errors
tail -f storage/logs/laravel.log | grep -i error

# Test login with wrong password
# Visit app and try login with wrong password
# Should see "Too many" error after 5 attempts

# Verify HTTPS redirect
curl -I http://your-domain.com
# Should show 301/302 redirect to https
```

## Maintenance Commands

### Regular Cleanup

```bash
# Cleanup old login attempts (run daily)
php artisan auth:cleanup-login-attempts

# Cleanup old audit logs
php artisan auth:cleanup-audit-logs

# Clear expired sessions
php artisan session:clear-expired

# Clear cache
php artisan cache:clear

# Clear old logs
php artisan logs:clear
```

### Monitoring Commands

```bash
# View application logs
tail -f storage/logs/laravel.log

# Filter for errors only
tail -f storage/logs/laravel.log | grep -i error

# Filter for specific action
tail -f storage/logs/laravel.log | grep -i "login_attempt"

# Count error frequency
grep -c "error" storage/logs/laravel.log

# Show recent logins
mysql -u root -p mess_management -e "
  SELECT created_at, description 
  FROM audit_logs 
  WHERE action = 'login_attempt' 
  ORDER BY created_at DESC LIMIT 20;"

# Show security-related logs
mysql -u root -p mess_management -e "
  SELECT action, status, COUNT(*) 
  FROM audit_logs 
  WHERE action IN ('login_attempt', 'logout', 'permission_change') 
  GROUP BY action, status;"
```

## Troubleshooting Commands

### Debug Issues

```bash
# Enter interactive shell
php artisan tinker

# Check configuration
>>> config('auth.login_attempts')
>>> config('session')

# Test database connection
>>> DB::connection()->select('SELECT 1')

# Check if user is locked out
>>> App\Models\LoginAttempt::isLockedOut('user@example.com')

# List recent failed attempts
>>> App\Models\LoginAttempt::where('successful', false)->latest()->first()

# View recent audit logs
>>> App\Models\AuditLog::latest()->first()

# Clear user lockout
>>> App\Models\LoginAttempt::where('email', 'user@example.com')->delete()

# Check session encryption
>>> config('session.encrypt')

# Verify middleware is registered
>>> app('router')->getMiddlewareGroups()

# Exit tinker
>>> exit
```

### Performance Diagnostics

```bash
# Check query log
php artisan tinker
>>> DB::enableQueryLog()
>>> App\Models\Meal::all() // Execute query
>>> DB::getQueryLog()
>>> exit

# Monitor slow queries (MySQL)
mysql -u root -p mess_management -e "
  SELECT * FROM mysql.slow_log ORDER BY event_time DESC LIMIT 10;"

# Check table indexes
mysql -u root -p mess_management -e "
  SHOW INDEXES FROM login_attempts;"

# Check query plan
mysql -u root -p mess_management -e "
  EXPLAIN SELECT * FROM audit_logs 
  WHERE user_id = 1 AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);"
```

### Reset/Recovery Commands

```bash
# Clear all sessions
redis-cli FLUSHALL  # If using Redis
# Or via Laravel
php artisan session:clear-expired

# Reset rate limiting
php artisan tinker
>>> DB::table('login_attempts')->truncate()
>>> exit

# Disable rate limiting temporarily
php artisan tinker
>>> config(['auth.login_attempts.enabled' => false])
>>> exit

# Clear application cache
php artisan cache:clear

# Completely reset development database
php artisan migrate:fresh --seed

# Rebuild composer autoloader
composer dumpautoload -o

# Rebuild NPM assets
npm run dev
npm run build  # For production
```

## Git Commands for Management

### Version Control

```bash
# View changes
git status
git diff

# Stage changes
git add .
git add app/Models/LoginAttempt.php

# Commit changes
git commit -m "Implement login rate limiting (Issue #1)"

# Push to repository
git push origin main

# Create feature branch
git checkout -b feature/issue-1-rate-limiting

# Merge feature branch
git checkout main
git merge feature/issue-1-rate-limiting

# View commit history
git log --oneline -10

# Revert last commit
git revert HEAD

# Tag release
git tag v1.0-security-fixes
git push origin v1.0-security-fixes
```

## Docker Commands (If Using Containers)

```bash
# Build container
docker-compose build

# Start services
docker-compose up -d

# Run migrations in container
docker-compose exec app php artisan migrate

# Run tests in container
docker-compose exec app ./vendor/bin/pest

# Access Laravel tinker in container
docker-compose exec app php artisan tinker

# View logs
docker-compose logs -f app

# Stop services
docker-compose down

# Clean up volumes
docker-compose down -v
```

## Scheduled Task Management

### Check Scheduling

```bash
# List all scheduled commands
php artisan schedule:list

# Run scheduler in foreground (watch mode)
php artisan schedule:work

# Manually run specific command
php artisan auth:cleanup-login-attempts

# Test cron expression
php artisan schedule:test

# Check cron job on server
crontab -l

# Verify Laravel cron is running
ps aux | grep schedule:work
```

## Emergency Procedures

### If Site is Down

```bash
# 1. Check error logs
tail -f storage/logs/laravel.log

# 2. Verify database connection
php artisan tinker
>>> DB::connection()->getPdo()
>>> exit

# 3. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Check disk space
df -h

# 5. Check memory usage
free -h

# 6. If rate limiting is stuck, disable temporarily
php artisan tinker
>>> config(['auth.login_attempts.enabled' => false])
>>> exit

# 7. If migrations failed, check status
php artisan migrate:status

# 8. Restart services
systemctl restart php-fpm  # or nginx, apache, etc.
systemctl restart mysql    # or postgresql

# 9. Restore from backup if needed
mysql -u root -p mess_management < backup-2026-06-18.sql
```

### Complete Rollback

```bash
# 1. Stop the application (if using systemd)
systemctl stop laravel-app

# 2. Revert code changes
git revert HEAD~1
# Or restore from backup
git checkout previous-tag

# 3. Rollback database
php artisan migrate:rollback --step=2

# 4. Rebuild caches
php artisan config:cache

# 5. Restart application
systemctl start laravel-app

# 6. Verify rollback
php artisan migrate:status
php artisan tinker
>>> DB::connection()->getPdo()
>>> exit
```

## Checklists

### Daily Maintenance

```
□ Check application logs for errors
□ Verify scheduled tasks ran: php artisan schedule:list
□ Monitor login attempts: mysql -e "SELECT * FROM login_attempts"
□ Verify audit logs are being created: mysql -e "SELECT * FROM audit_logs LIMIT 1"
□ Check disk space: df -h
□ Verify backups completed
```

### Weekly Tasks

```
□ Review security audit logs for suspicious patterns
□ Test user password reset flow
□ Test login with rate limiting disabled, then re-enable
□ Verify migration status: php artisan migrate:status
□ Check application performance metrics
□ Review and archive old logs
```

### Monthly Tasks

```
□ Analyze audit logs for patterns
□ Test complete disaster recovery procedure
□ Review and update rate limiting thresholds if needed
□ Performance optimization review
□ Security patch updates
□ Database optimization: ANALYZE and OPTIMIZE tables
```

---

**Last Updated**: June 18, 2026
**For Issues**: 1-4 (See IMPLEMENTATION_ROADMAP_ISSUES_5-14.md for Issues 5-14)
