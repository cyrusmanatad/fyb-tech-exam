<!-- resources/views/reports/orders.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders Report</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 12px;
      color: #1f2937;
      padding: 30px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid #14b8a6;
    }

    .header h1 {
      font-size: 22px;
      font-weight: 900;
      color: #0f172a;
    }

    .header p {
      font-size: 10px;
      color: #94a3b8;
      margin-top: 2px;
    }

    .badge {
      background: #f0fdfa;
      color: #0d9488;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 10px;
      font-weight: 700;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
    }

    thead tr {
      background-color: #f8fafc;
      border-bottom: 2px solid #e2e8f0;
    }

    thead th {
      padding: 10px 12px;
      text-align: left;
      font-size: 9px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #94a3b8;
    }

    tbody tr {
      border-bottom: 1px solid #f1f5f9;
    }

    tbody tr:nth-child(even) {
      background-color: #f8fafc;
    }

    tbody td {
      padding: 10px 12px;
      font-size: 11px;
      color: #374151;
    }

    .status {
      padding: 3px 8px;
      border-radius: 20px;
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .status-pending    { background: #fef9c3; color: #854d0e; }
    .status-confirmed  { background: #dbeafe; color: #1e40af; }
    .status-delivered  { background: #dcfce7; color: #166534; }
    .status-cancelled  { background: #fee2e2; color: #991b1b; }

    .payment-paid     { background: #dcfce7; color: #166534; }
    .payment-unpaid   { background: #fee2e2; color: #991b1b; }
    .payment-partial  { background: #fef9c3; color: #854d0e; }

    .amount {
      font-weight: 700;
      color: #0f172a;
    }

    .footer {
      margin-top: 24px;
      padding-top: 12px;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      font-size: 9px;
      color: #94a3b8;
    }

    .summary {
      margin-top: 16px;
      background: #f0fdfa;
      border: 1px solid #99f6e4;
      border-radius: 8px;
      padding: 12px 16px;
      display: flex;
      gap: 32px;
    }

    .summary-item label {
      font-size: 9px;
      font-weight: 900;
      text-transform: uppercase;
      color: #94a3b8;
      display: block;
    }

    .summary-item span {
      font-size: 14px;
      font-weight: 900;
      color: #0f172a;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <div>
      <h1>Orders Report</h1>
      <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    <span class="badge">Total: {{ $orders->count() }} orders</span>
  </div>

  <!-- Summary -->
  <div class="summary">
    <div class="summary-item">
      <label>Total Orders</label>
      <span>{{ $orders->count() }}</span>
    </div>
    <div class="summary-item">
      <label>Total Revenue</label>
      <span>{{ number_format($orders->sum('total'), 2) }}</span>
    </div>
    <div class="summary-item">
      <label>Paid Orders</label>
      <span>{{ $orders->where('payment_status', 'paid')->count() }}</span>
    </div>
    <div class="summary-item">
      <label>Pending Orders</label>
      <span>{{ $orders->where('status', 'pending')->count() }}</span>
    </div>
  </div>

  <!-- Table -->
  <table>
    <thead>
      <tr>
        <th>Order #</th>
        <th>Customer</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Method</th>
        <th>Total</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        <tr>
          <td><strong>{{ $order->order_number }}</strong></td>
          <td>{{ $order->user->name ?? 'N/A' }}</td>
          <td>
            <span class="status status-{{ $order->status }}">
              {{ ucfirst($order->status) }}
            </span>
          </td>
          <td>
            <span class="status payment-{{ $order->payment_status }}">
              {{ ucfirst($order->payment_status) }}
            </span>
          </td>
          <td>{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
          <td class="amount">{{ number_format($order->total, 2) }}</td>
          <td>{{ $order->created_at->format('M d, Y') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" style="text-align:center; padding: 24px; color: #94a3b8;">
            No orders found.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Footer -->
  <div class="footer">
    <span>Confidential — Internal Use Only</span>
    <span> &copy; {{ now()->year }}</span>
  </div>

</body>
</html>