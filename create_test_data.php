#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Month;
use App\Models\Meal;
use App\Models\Deposit;
use App\Models\Expense;
use Carbon\Carbon;
use Database\Factories\MonthFactory;

try {
    // Create a test month for April 2026
    $month = Month::create([
        'name' => 'April 2026',
        'start_date' => Carbon::parse('2026-04-01'),
        'end_date' => Carbon::parse('2026-04-30'),
        'status' => 'open',
    ]);
    
    echo "Created month: {$month->name} ({$month->start_date->format('Y-m-d')} to {$month->end_date->format('Y-m-d')})\n";
    
    // Create test expenses for this month
    Expense::create([
        'month_id' => $month->id,
        'category' => 'groceries',
        'amount' => 2000,
        'date' => Carbon::parse('2026-04-01'),
        'note' => 'Rice and dal',
    ]);
    
    Expense::create([
        'month_id' => $month->id,
        'category' => 'groceries',
        'amount' => 1500,
        'date' => Carbon::parse('2026-04-02'),
        'note' => 'Vegetables',
    ]);
    
    Expense::create([
        'month_id' => $month->id,
        'category' => 'groceries',
        'amount' => 1800,
        'date' => Carbon::parse('2026-04-03'),
        'note' => 'Meat',
    ]);
    
    echo "Created test expenses\n";
    
    // Create test meals for members
    $members = \App\Models\Member::all();
    
    foreach ($members as $member) {
        // Create 10 meals for each member
        for ($i = 1; $i <= 10; $i++) {
            Meal::create([
                'member_id' => $member->id,
                'month_id' => $month->id,
                'date' => Carbon::parse('2026-04-' . str_pad($i, 2, '0', STR_PAD_LEFT)),
                'meal_count' => 1,
            ]);
        }
        
        // Create deposit for each member
        Deposit::create([
            'member_id' => $member->id,
            'month_id' => $month->id,
            'date' => Carbon::parse('2026-04-05'),
            'amount' => 5000,
            'description' => 'Initial deposit',
        ]);
        
        echo "Created meals and deposit for {$member->name}\n";
    }
    
    echo "\nTest data created successfully!\n";
    echo "Members can now login with:\n";
    foreach ($members as $member) {
        echo "  Email: {$member->email}, Password: password\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
