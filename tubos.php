<?php
// --- Sample Data for Redeemable Items ---
$redeemableItems = [
    [
        'item_name' => '18K Gold Ring',
        'pawn_ticket' => 'PT-12345',
        'date_pawned' => '2025-01-10',
        'principal' => 10000,
        'interest' => 1200,
    ],
    [
        'item_name' => '22K Necklace',
        'pawn_ticket' => 'PT-12346',
        'date_pawned' => '2025-02-05',
        'principal' => 15000,
        'interest' => 1800,
    ],
    [
        'item_name' => 'Wristwatch',
        'pawn_ticket' => 'PT-12347',
        'date_pawned' => '2025-03-01',
        'principal' => 7000,
        'interest' => 900,
    ],
];

// --- Helper: Format Date ---
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// --- Step Management ---
$step = isset($_GET['step']) ? $_GET['step'] : 'list';
$selected = null;
if ($step === 'redeem' && isset($_GET['id']) && isset($redeemableItems[$_GET['id']])) {
    $selected = $redeemableItems[$_GET['id']];
}
$receipt = null;

// --- Handle Payment Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proceed_payment'])) {
    // Simulate payment gateway redirect and success
    $txnId = 'TXN-' . date('Ymd') . '-' . rand(100,999);
    $now = date("Y-m-d H:i:s");
    $receipt = [
        'reference' => $txnId,
        'datetime' => $now,
        'item_name' => $_POST['item_name'],
        'pawn_ticket' => $_POST['pawn_ticket'],
        'principal' => $_POST['principal'],
        'interest' => $_POST['interest'],
        'total' => $_POST['total'],
        'payment_method' => $_POST['payment_method'],
        'pickup_location' => 'Main Branch, XYZ St.',
    ];
    $step = 'receipt';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redeem Your Jewelry (Tubos)</title>
    <!-- Add Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            padding: 20px;
            background: rgb(238, 233, 160);
        }
        h2, h3, table, th, td, button, select, input, label, .card, .success, .receipt, .notes, .pay-icons, .center, .hidden, .receipt * {
            font-family: 'Poppins', Arial, sans-serif !important;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        button, select {
            padding: 8px 12px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
        }
        button {
            background: #1d4b0b;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
        }
        button:hover, button:focus {
            background: #267a3e;
            color: #fff;
            box-shadow: 0 2px 12px rgba(29,75,11,0.18);
        }
        .card { 
            background: #fffde4; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .success { color: #267a3e; font-weight: bold; }
        .receipt { background: #fff3e0; padding: 18px 22px; border-radius: 8px; }
        .notes { background: #e7fbe7; color: #1d4b0b; border-left: 4px solid #1d4b0b; padding: 12px 18px; margin-top: 18px; border-radius: 6px; font-size: 1rem;}
        .pay-icons { display: flex; gap: 16px; margin: 12px 0 18px 0; }
        .pay-icons label { cursor: pointer; display: flex; align-items: center; gap: 6px; }
        .pay-icons input[type="radio"] { accent-color: #1d4b0b; }
        .pay-icons span { font-weight: bold; }
        .pay-icons .icon { font-size: 1.4em; }
        .center { text-align: center; }
        .hidden { display: none; }
    </style>
</head>
<body>

<?php if ($step === 'receipt' && $receipt): ?>
    <div class="receipt" style="max-width:400px;margin:32px auto 0 auto;font-family:'Courier New',monospace;background:#fff;border:1px dashed #aaa;box-shadow:0 2px 16px rgba(0,0,0,0.08);padding:32px 28px 24px 28px;position:relative;">
        <div style="text-align:center;">
            <div style="font-size:2.2rem; color:#267a3e; margin-bottom:8px;">✅</div>
            <div style="font-size:1.25rem; font-weight:bold; letter-spacing:2px; margin-bottom:2px;">TUBOS RECEIPT</div>
            <div style="font-size:0.95rem; color:#888; margin-bottom:18px;">Main Branch, XYZ St.</div>
        </div>
        <hr style="border:none;border-top:1px dashed #bbb;margin:12px 0 18px 0;">
        <table style="width:100%;font-size:1rem;">
            <tr>
                <td style="color:#888;">Reference No.</td>
                <td style="text-align:right;"><b><?= htmlspecialchars($receipt['reference']) ?></b></td>
            </tr>
            <tr>
                <td style="color:#888;">Date & Time</td>
                <td style="text-align:right;"><?= htmlspecialchars($receipt['datetime']) ?></td>
            </tr>
            <tr>
                <td style="color:#888;">Pawn Ticket</td>
                <td style="text-align:right;"><?= htmlspecialchars($receipt['pawn_ticket']) ?></td>
            </tr>
            <tr>
                <td style="color:#888;">Item</td>
                <td style="text-align:right;"><?= htmlspecialchars($receipt['item_name']) ?></td>
            </tr>
        </table>
        <hr style="border:none;border-top:1px dashed #bbb;margin:18px 0 12px 0;">
        <table style="width:100%;font-size:1rem;">
            <tr>
                <td>Principal Paid</td>
                <td style="text-align:right;">₱<?= number_format($receipt['principal'], 2) ?></td>
            </tr>
            <tr>
                <td>Interest Paid</td>
                <td style="text-align:right;">₱<?= number_format($receipt['interest'], 2) ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;">TOTAL PAID</td>
                <td style="text-align:right;font-weight:bold;">₱<?= number_format($receipt['total'], 2) ?></td>
            </tr>
        </table>
        <hr style="border:none;border-top:1px dashed #bbb;margin:18px 0 12px 0;">
        <table style="width:100%;font-size:1rem;">
            <tr>
                <td style="color:#888;">Payment Method</td>
                <td style="text-align:right;"><?= htmlspecialchars($receipt['payment_method']) ?></td>
            </tr>
            <tr>
                <td style="color:#888;">Pick-Up Location</td>
                <td style="text-align:right;"><?= htmlspecialchars($receipt['pickup_location']) ?></td>
            </tr>
        </table>
        <hr style="border:none;border-top:1px dashed #bbb;margin:18px 0 18px 0;">
        <div style="font-size:0.98rem; color:#1d4b0b; text-align:center; margin-bottom:10px;">
            🛡️ <b>Important:</b> Bring 1 valid ID and this receipt when claiming your item.<br>
            Pick-up available within 7 business days after payment.
        </div>
        <div style="display:flex;justify-content:center;gap:12px;margin-top:10px;">
            <button onclick="window.print()" style="background:#1d4b0b;color:#fff;border:none;border-radius:6px;padding:8px 18px;font-weight:bold;cursor:pointer;">🖨️ Download PDF</button>
            <button onclick="alert('Receipt emailed!')" style="background:#f5b60f;color:#1d4b0b;border:none;border-radius:6px;padding:8px 18px;font-weight:bold;cursor:pointer;">✉️ Email</button>
        </div>
        <div style="position:absolute;bottom:10px;left:0;width:100%;text-align:center;font-size:0.85rem;color:#bbb;letter-spacing:2px;">
            --- END OF RECEIPT ---
        </div>
    </div>

<?php elseif ($step === 'redeem' && $selected): ?>
    <!-- Redemption Modal/Page -->
    <h2 style="text-align:center;"> Confirm Tubos</h2>
    <div class="card" style="max-width:600px; margin: 0 auto;">
        <form method="POST">
            <input type="hidden" name="item_name" value="<?= htmlspecialchars($selected['item_name']) ?>">
            <input type="hidden" name="pawn_ticket" value="<?= htmlspecialchars($selected['pawn_ticket']) ?>">
            <input type="hidden" name="principal" value="<?= $selected['principal'] ?>">
            <input type="hidden" name="interest" value="<?= $selected['interest'] ?>">
            <input type="hidden" name="total" value="<?= $selected['principal'] + $selected['interest'] ?>">

            <p><strong>Item Name:</strong> <?= htmlspecialchars($selected['item_name']) ?></p>
            <p><strong>Date Pawned:</strong> <?= formatDate($selected['date_pawned']) ?></p>
            <p><strong>Pawn Ticket Number:</strong> <?= htmlspecialchars($selected['pawn_ticket']) ?></p>
            <p><strong>Principal:</strong> ₱<?= number_format($selected['principal'], 2) ?></p>
            <p><strong>Tubo (Interest):</strong> ₱<?= number_format($selected['interest'], 2) ?></p>
            <p><strong>Total to Pay:</strong> <b>₱<?= number_format($selected['principal'] + $selected['interest'], 2) ?></b></p>

            <hr>
            <label><b>Payment Method:</b></label>
            <div class="pay-icons">
                <label><input type="radio" name="payment_method" value="GCash" required> <span class="icon">📱</span> <span>GCash</span></label>
                <label><input type="radio" name="payment_method" value="Maya" required> <span class="icon">💳</span> <span>Maya</span></label>
                <label><input type="radio" name="payment_method" value="Debit/Credit Card" required> <span class="icon">🏦</span> <span>Debit/Credit Card</span></label>
                <label><input type="radio" name="payment_method" value="Bank Transfer" required> <span class="icon">🏛️</span> <span>Bank Transfer</span></label>
            </div>
            <div class="center">
                <a href="?step=list" style="text-decoration:none;">
                    <button type="button" style="background:#f5b60f; color:#1d4b0b; margin-right:10px;">Back</button>
                </a>
                <button type="submit" name="proceed_payment">Proceed to Payment</button>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- Main Tubos Screen -->
    <h2>Redeem Your Jewelry (Tubos)</h2>
    <div class="card">
        <h3>Tubos Items</h3>
        <table>
            <tr>
                <th>Item</th>
                <th>Pawn Ticket No.</th>
                <th>Date Pawned</th>
                <th>Amount Borrowed</th>
                <th>Total to Pay</th>
                <th>Action</th>
            </tr>
            <?php foreach ($redeemableItems as $i => $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td>#<?= htmlspecialchars($item['pawn_ticket']) ?></td>
                <td><?= formatDate($item['date_pawned']) ?></td>
                <td>₱<?= number_format($item['principal'], 2) ?></td>
                <td>₱<?= number_format($item['principal'] + $item['interest'], 2) ?></td>
                <td>
                    <a href="?step=redeem&id=<?= $i ?>"><button type="button">Redeem Item</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p style="color:#888; font-size:0.95em;">* “Total to Pay” = Principal + Accrued Tubo (Interest)</p>
    </div>
<?php endif; ?>

<!-- Make sure this is in your <head> or before </body> -->
<script>
function showStep2() {
    // Validate required fields in Step 1
    var itemType = document.getElementById('item_type').value.trim();
    var brand = document.getElementById('brand').value.trim();
    var model = document.getElementById('model').value.trim();
    var grams = document.getElementById('grams').value.trim();
    var remarks = document.getElementById('remarks').value.trim();
    var origPrice = document.getElementById('orig_price').value.trim();

    if (!itemType) {
        alert('Please select Item Type.');
        document.getElementById('item_type').focus();
        return;
    }
    if (!brand) {
        alert('Please enter Brand.');
        document.getElementById('brand').focus();
        return;
    }
    if (!model) {
        alert('Please select Model.');
        document.getElementById('model').focus();
        return;
    }
    if (!grams) {
        alert('Please enter Grams.');
        document.getElementById('grams').focus();
        return;
    }
    if (!remarks) {
        alert('Please enter Remarks/Attributes.');
        document.getElementById('remarks').focus();
        return;
    }
    if (!origPrice) {
        alert('Please enter Orig Price.');
        document.getElementById('orig_price').focus();
        return;
    }

    // Show Step 2, hide Step 1
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
}
</script>

</body>
</html>
