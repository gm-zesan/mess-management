<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-box {
            display: inline-block;
            width: 22%;
            margin: 1%;
            padding: 15px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
        }
        .summary-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th {
            background: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .status-credit {
            color: green;
            font-weight: bold;
        }
        .status-due {
            color: red;
            font-weight: bold;
        }
        .status-settled {
            color: gray;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Report</h1>
        <p><strong>{{ $month->name }}</strong> ({{ $month->start_date->format('M d') }} - {{ $month->end_date->format('M d, Y') }})</p>
    </div>

    <div>
        <div class="summary-box">
            <h3>Total Meals</h3>
            <div class="value">{{ $summary['total_meals'] }}</div>
        </div>
        <div class="summary-box">
            <h3>Total Expenses</h3>
            <div class="value">{{ number_format($summary['total_expenses'], 2) }}</div>
        </div>
        <div class="summary-box">
            <h3>Total Deposits</h3>
            <div class="value">{{ number_format($summary['total_deposits'], 2) }}</div>
        </div>
        <div class="summary-box">
            <h3>Meal Rate</h3>
            <div class="value">{{ number_format($summary['meal_rate'], 2) }}</div>
        </div>
    </div>

    <h2 style="margin-top: 40px;">Member Balances</h2>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Meals</th>
                <th>Meal Cost</th>
                <th>Deposit</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['member_balances'] as $balance)
                <tr>
                    <td>{{ $balance['member_name'] }}</td>
                    <td>{{ $balance['meals'] }}</td>
                    <td>{{ number_format($balance['meal_cost'], 2) }}</td>
                    <td>{{ number_format($balance['deposited'], 2) }}</td>
                    <td>{{ number_format($balance['balance'], 2) }}</td>
                    <td>
                        <span class="status-{{ $balance['status'] }}">
                            {{ ucfirst($balance['status']) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>
</body>
</html>
