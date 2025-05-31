<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: sss.php');
    exit();
}

// Fetch user's active pawned items
$stmt = $conn->prepare("
    SELECT pi.*, sr.item_type, sr.brand, sr.model
    FROM pawned_items pi
    JOIN sangla_requests sr ON pi.sangla_request_id = sr.id
    WHERE pi.user_id = ? AND pi.status = 'active'
    ORDER BY pi.due_date ASC
");
$stmt->execute([$_SESSION['user_id']]);
$pawned_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle interest payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_interest'])) {
    try {
        $pawned_item_id = $_POST['pawned_item_id'];
        $payment_amount = $_POST['payment_amount'];

        // Start transaction
        $conn->beginTransaction();

        // Insert payment record
        $stmt = $conn->prepare("
            INSERT INTO interest_payments (pawned_item_id, user_id, payment_amount, payment_type)
            VALUES (?, ?, ?, 'interest')
        ");
        $stmt->execute([$pawned_item_id, $_SESSION['user_id'], $payment_amount]);

        // Create notification
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, 'Interest Payment', 'Your interest payment has been recorded.', 'payment')
        ");
        $stmt->execute([$_SESSION['user_id']]);

        $conn->commit();
        header('Location: tubo.php?success=1');
        exit();
    } catch(PDOException $e) {
        $conn->rollBack();
        $error = "Payment failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tubo - Pawnshop</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #fef8d9;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 960px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px #ccc;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 12px;
      text-align: center;
    }
    button {
      padding: 6px 12px;
      background-color: #3d7c2b;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #2f5f20;
    }
    .modal, .receipt {
      display: none;
      background: rgba(0,0,0,0.5);
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      justify-content: center;
      align-items: center;
      z-index: 10;
    }
    .modal-content, .receipt-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      width: 400px;
      max-width: 90%;
      position: relative;
    }
    .receipt-content p {
      margin: 8px 0;
    }
    .close {
      background-color: #aaa;
      color: white;
      position: absolute;
      top: 10px; right: 15px;
      padding: 5px 10px;
      border-radius: 50%;
      cursor: pointer;
    }
    .payment-methods input {
      margin-right: 8px;
    }
    .actions {
      text-align: center;
      margin-top: 20px;
    }
    .receipt-title {
      text-align: center;
      font-weight: bold;
      margin-bottom: 10px;
      font-size: 18px;
      text-transform: uppercase;
    }
    .divider {
      border-top: 1px dashed #ccc;
      margin: 10px 0;
    }
    .print-email-buttons {
      text-align: center;
      margin-top: 15px;
    }
    .print-email-buttons button {
      margin: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Pay Monthly TUBO</h1>
    <table>
      <thead>
        <tr>
          <th>Item Name</th>
          <th>Pawn Ticket No.</th>
          <th>Due Date</th>
          <th>Interest Amount Due</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pawned_items as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['item_type'] . ' - ' . $item['brand'] . ' ' . $item['model']); ?></td>
            <td>#<?php echo htmlspecialchars($item['pawn_ticket_no']); ?></td>
            <td><?php echo date('M d, Y', strtotime($item['due_date'])); ?></td>
            <td>₱<?php echo number_format($item['loan_amount'] * ($item['interest_rate'] / 100), 2); ?></td>
            <td>
              <button onclick="showPaymentModal(<?php echo $item['id']; ?>, <?php echo $item['loan_amount'] * ($item['interest_rate'] / 100); ?>)">
                Pay Interest
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Payment Modal -->
  <div id="paymentModal" class="modal">
    <div class="modal-content">
      <h3>Make Interest Payment</h3>
      <form method="POST" action="">
        <input type="hidden" name="pay_interest" value="1">
        <input type="hidden" name="pawned_item_id" id="pawned_item_id">
        <input type="hidden" name="payment_amount" id="payment_amount">
        
        <div class="form-group">
          <label>Amount to Pay:</label>
          <span id="displayAmount"></span>
        </div>
        
        <div class="form-group">
          <label>Payment Method:</label>
          <select name="payment_method" required>
            <option value="cash">Cash</option>
            <option value="gcash">GCash</option>
            <option value="bank_transfer">Bank Transfer</option>
          </select>
        </div>
        
        <button type="submit">Confirm Payment</button>
        <button type="button" onclick="closePaymentModal()">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Receipt Modal -->
  <div class="receipt" id="receiptScreen">
    <div class="receipt-content" id="receiptContent">
      <span class="close" onclick="closeModal('receiptScreen')">&times;</span>
      <div class="receipt-title">TUBO RECEIPT</div>
      <p><strong>Reference No:</strong> <span id="refNo"></span></p>
      <p><strong>Date & Time:</strong> <span id="dateTime"></span></p>
      <div class="divider"></div>
      <p><strong>Item Name:</strong> <span id="rItemName"></span></p>
      <p><strong>Pawn Ticket:</strong> <span id="rTicketNo"></span></p>
      <p><strong>Amount Paid:</strong> <span id="rAmountPaid"></span></p>
      <p><strong>Payment Method:</strong> <span id="rPaymentMethod"></span></p>
      <div class="divider"></div>
      <p><strong>Redemption Reminder:</strong></p>
      <p>Please make the next TUBO payment <strong>before or on</strong> the next due date to avoid penalty or item forfeiture.</p>
      <p>Always present your valid ID and this receipt during any transaction.</p>

      <div class="print-email-buttons">
        <button onclick="printReceipt()">🖨 Print Receipt</button>
        <button onclick="emailReceipt()">📧 Email Receipt</button>
      </div>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="success-message">Payment recorded successfully!</div>
  <?php endif; ?>

  <?php if (isset($error)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <script>
    let currentItem = {};

    function showPaymentModal(itemId, amount) {
      document.getElementById('pawned_item_id').value = itemId;
      document.getElementById('payment_amount').value = amount;
      document.getElementById('displayAmount').textContent = '₱' + amount.toFixed(2);
      document.getElementById('paymentModal').style.display = 'block';
    }

    function closePaymentModal() {
      document.getElementById('paymentModal').style.display = 'none';
    }

    function proceedToPayment() {
      const method = document.querySelector('input[name="paymentMethod"]:checked');
      if (!method) {
        alert('Please select a payment method.');
        return;
      }

      const refNo = 'TXN-' + new Date().getTime();
      const now = new Date().toLocaleString();

      document.getElementById('refNo').textContent = refNo;
      document.getElementById('dateTime').textContent = now;
      document.getElementById('rItemName').textContent = currentItem.name;
      document.getElementById('rTicketNo').textContent = currentItem.ticket;
      document.getElementById('rAmountPaid').textContent = currentItem.amount;
      document.getElementById('rPaymentMethod').textContent = method.value;

      closeModal('paymentModal');
      document.getElementById('receiptScreen').style.display = 'flex';
    }

    function printReceipt() {
      const content = document.getElementById('receiptContent').innerHTML;
      const printWindow = window.open('', '', 'width=800,height=600');
      printWindow.document.write('<html><head><title>TUBO Receipt</title>');
      printWindow.document.write('<style>body{font-family:sans-serif;padding:20px;} .divider{border-top:1px dashed #ccc;margin:10px 0;}</style>');
      printWindow.document.write('</head><body>');
      printWindow.document.write(content);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.print();
    }

    function emailReceipt() {
      const subject = encodeURIComponent("Your TUBO Payment Receipt");
      const body = encodeURIComponent(
        `TUBO Payment Successful!\n\n` +
        `Reference No: ${document.getElementById('refNo').textContent}\n` +
        `Date & Time: ${document.getElementById('dateTime').textContent}\n` +
        `Item Name: ${document.getElementById('rItemName').textContent}\n` +
        `Pawn Ticket: ${document.getElementById('rTicketNo').textContent}\n` +
        `Amount Paid: ${document.getElementById('rAmountPaid').textContent}\n` +
        `Payment Method: ${document.getElementById('rPaymentMethod').textContent}\n\n` +
        `Reminder: Please redeem before the next due date.`
      );
      window.location.href = `mailto:?subject=${subject}&body=${body}`;
    }
  </script>
</body>
</html>
