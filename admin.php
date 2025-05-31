<?php
// Simulated sample data with more functionality
$pendingAppraisals = 12;
$approvedLoans = 8;
$completedTransactions = 23;
$registeredUsers = 57;

// Sample appraisal requests with IDs for tracking
$requests = [
    [
        'id' => 1,
        'user' => 'Maria Santos',
        'item' => '18K Gold Necklace',
        'estimate' => '₱10,000',
        'timestamp' => '2025-05-20 14:22',
        'status' => 'pending'
    ],
    [
        'id' => 2,
        'user' => 'Juan Dela Cruz',
        'item' => 'Diamond Ring',
        'estimate' => '₱25,000',
        'timestamp' => '2025-05-21 09:15',
        'status' => 'pending'
    ]
];

// Sample pending responses
$pendingResponses = [
    [
        'id' => 3,
        'user' => 'Ana Rodriguez',
        'item' => '18K Gold Necklace',
        'offered_amount' => '₱8,500',
        'status' => 'waiting_response'
    ]
];

// Sample branch confirmations
$branchConfirmations = [
    [
        'id' => 4,
        'user' => 'Carlos Mendoza',
        'item' => 'Gold Bracelet',
        'loan_amount' => '₱12,000',
        'status' => 'verified'
    ]
];

// Sample transaction history
$transactions = [
    [
        'id' => 101,
        'user' => 'Sofia Garcia',
        'item' => 'Diamond Earrings',
        'amount' => '₱15,000',
        'date' => '2025-05-15',
        'status' => 'completed'
    ],
    [
        'id' => 102,
        'user' => 'Miguel Torres',
        'item' => 'Gold Watch',
        'amount' => '₱20,000',
        'date' => '2025-05-18',
        'status' => 'completed'
    ]
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'send_appraisal':
                if (isset($_POST['request_id'], $_POST['final_value'], $_POST['interest_rate'], $_POST['loan_amount'])) {
                    // In a real application, you would save this to database
                    $response = [
                        'success' => true,
                        'message' => 'Appraisal result sent successfully to user!'
                    ];
                }
                break;
                
            case 'confirm_loan_release':
                if (isset($_POST['confirmation_id'])) {
                    // In a real application, you would update the database
                    $response = [
                        'success' => true,
                        'message' => 'Loan has been released successfully!'
                    ];
                }
                break;
                
            case 'export_report':
                // In a real application, you would generate and download a report
                $response = [
                    'success' => true,
                    'message' => 'Report exported successfully!',
                    'redirect' => '#' // Would be actual download link
                ];
                break;
        }
    }
    
    // Return JSON response for AJAX requests
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>iPawnShop Admin</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f5b60f;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .sidebar {
      width: 250px;
      background: #1d4b0b ;  
      padding: 20px;
      border-right: 1px solid #ccc;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      min-height: 100vh; 
    }
    .sidebar h2 {
      margin-bottom: 20px;
    }
    .sidebar a {
      display: block;
      padding: 12px 0;
      margin-bottom: 10px;
      text-decoration: none;
      color: #fff;
      border-radius: 7px;
      background: #ffeaa7;
      text-align: center;
      font-weight: bold;
      letter-spacing: 1px;
      box-shadow: 0 2px 8px rgba(29,75,11,0.08);
      transition: background 0.3s;
      cursor: pointer;
    }
    .sidebar a:hover,
    .sidebar a:focus,
    .sidebar a.active {
      background: #1d4b0b;
      color: #fff;
      border: 2px solid #ffeaa7;
    }
    .main {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100vw;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    .main > div {
      display: grid !important;
      grid-template-columns: repeat(3, 1fr);
      gap: 60px;
      justify-content: center;
      align-items: stretch;
      width: 100%;
      max-width: 1700px;
      margin: 0 auto;
      box-sizing: border-box;
    }
    .card {
      background: linear-gradient(135deg, #fffbe7 60%,  #ffeaa7 100%);
      padding: 24px 18px;
      border-radius: 14px;
      box-shadow: 0 4px 18px rgba(245,188,89,0.13);
      border-left: 6px solid #f5b60f;
      width: 100%;
      min-width: 240px;
      max-width: 360px;
      min-height: 220px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      text-align: center;
      font-size: 1.08rem;
      position: relative;
      height: 100%;
      transition: box-shadow 0.2s, transform 0.2s, border-left-color 0.2s;
    }
    .card h3 {
      font-size: 1.5rem;
      margin-bottom: 18px;
    }
    .card:nth-child(2) { border-left-color: #f5b60f; background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);}
    .card:nth-child(3) { border-left-color: #f5b60f; background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);}
    .card:nth-child(4) { border-left-color: #f5b60f; background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);}
    .card:nth-child(5) { border-left-color: #f5b60f; background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);}
    .card:nth-child(6) { border-left-color: #f5b60f; background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);}
    .card:nth-child(7) { border-left-color: #f5b60f; background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);}
    .card:hover {
      box-shadow: 0 8px 32px rgba(40,167,69,0.18), 0 2px 12px rgba(40,167,69,0.10);
      transform: translateY(-4px) scale(1.02);
      border-left-color: #0e2f1c;
    }
    .widget {
      display: inline-block;
      width: 80%;
      min-width: 100px;
      max-width: 180px;
      margin: 10px auto;
      padding: 12px;
      background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);
      border-radius: 10px;
      box-shadow: 0 1px 4px rgba(245,188,89,0.06);
      margin-bottom: 6px;
      transition: box-shadow 0.2s, transform 0.2s;
      vertical-align: top;
      text-align: center;
      font-size: 1.08rem;
    }
    .widget h2 {
      font-size: 1.5rem;
      margin: 8px 0 0 0;
    }
    .widget:hover {
      box-shadow: 0 6px 18px rgba(40,167,69,0.15);
      transform: scale(1.03);
      background: linear-gradient(135deg, #ffeaa7 60%, #ffeaa7 100%);
    }
    .button {
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      color: #fff !important;
      background: #1d4b0b;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s;
      margin-top: auto;
      align-self: flex-end;
      margin-bottom: 0;
      font-size: 14px;
    }
    .button.green { background: #1d4b0b; }
    .button.purple { background: #1d4b0b; }
    .button:hover, .button:focus {
      background: #0e2f1c;
      box-shadow: 0 2px 12px rgba(245,188,89,0.18);
    }
    .button:disabled {
      background: #ccc;
      cursor: not-allowed;
      opacity: 0.6;
    }

    /* Form styles */
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: #ffeaa7;
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 80%;
      max-width: 500px;
      position: relative;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      line-height: 1;
    }
    .close:hover,
    .close:focus {
      color: #000;
    }

    /* Alert styles */
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border: 1px solid transparent;
      border-radius: 4px;
    }
    .alert-success {
      color: #155724;
      background-color: #d4edda;
      border-color: #c3e6cb;
    }
    .alert-error {
      color: #721c24;
      background-color: #f8d7da;
      border-color: #f5c6cb;
    }

    /* Single view styles */
    .single-view {
      display: none;
      justify-content: center;
      align-items: center;
      height: calc(100vh - 70px);
      min-height: 500px;
      flex-direction: column;
      padding: 0;
    }
    .single-view.active {
      display: flex;
    }
    .single-card {
      background: linear-gradient(135deg, #fffbe7 60%, #ffeaa7 100%);
      box-shadow: 0 2px 8px rgba(245,188,89,0.08);
      border-radius: 18px;
      padding: 24px 18px;
      max-width: 600px;
      width: 100%;
      min-width: 320px;
      min-height: 160px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    /* Hide default grid view when single view is active */
    .main.hidden {
      display: none;
    }

    ::-webkit-scrollbar {
      width: 12px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    @media (max-width: 1500px) {
      .main > div {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 1100px) {
      .main > div {
        grid-template-columns: 1fr;
      }
      .card {
        max-width: 98vw;
        min-width: 0;
      }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav style="width:100%; background:#1d4b0b; color:#fff; padding:18px 0; display:flex; align-items:center; box-shadow:0 2px 8px rgba(29,75,11,0.08); position:fixed; top:0; left:0; z-index:100;">
    <div style="font-size:1.5rem; font-weight:bold; margin-left:40px; letter-spacing:2px;">
      iPawnShop Admin
    </div>
    <div style="margin-left:auto; margin-right:40px;">
      <form action="logout.php" method="post" style="display:inline;">
        <button type="submit" 
          style="background:#f5b60f; color:#1d4b0b; font-weight:bold; border:none; border-radius:6px; padding:8px 22px; font-size:1rem; cursor:pointer; box-shadow:0 2px 8px rgba(245,188,89,0.13); transition:background 0.2s;">
          Logout
        </button>
      </form>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="sidebar"
       style="position:fixed; left:0; top:60px; height:calc(100vh - 70px); width:200px; background:#1d4b0b; padding:20px; border-right:1px solid #ccc; display:flex; flex-direction:column; justify-content:flex-start;">
    <a href="#" data-view="dashboard" class="nav-link active">Dashboard</a>
    <a href="#" data-view="appraisal" class="nav-link">Appraisal Requests</a>
    <a href="#" data-view="pending" class="nav-link">Pending Responses</a>
    <a href="#" data-view="branch" class="nav-link">Branch Confirmations</a>
    <a href="#" data-view="transactions" class="nav-link">Transactions</a>
    <a href="admin_marketplace.php" class="nav-link">Marketplace Items</a>
  </div>

  <!-- Alert Container -->
  <div id="alertContainer" style="position: fixed; top: 80px; right: 20px; z-index: 1001;"></div>

  <!-- Main Content Grid View -->
  <div class="main" id="gridView" style="margin-top:100px; margin-left:250px; min-height:calc(100vh - 110px);">
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 100px; justify-content: center; align-items: stretch; width: 100%; max-width: 1700px; margin: 0 auto; box-sizing: border-box;">
      <!-- Dashboard Overview -->
      <div class="card">
        <h3>Dashboard Overview</h3>
        <div class="widget">
          <strong>Pending Appraisals</strong><br>
          <h2><?= $pendingAppraisals ?></h2>
        </div>
        <div class="widget">
          <strong>Approved Loans</strong><br>
          <h2><?= $approvedLoans ?></h2>
        </div>
        <div class="widget">
          <strong>Completed Transactions</strong><br>
          <h2><?= $completedTransactions ?></h2>
        </div>
        <div class="widget">
          <strong>Registered Users</strong><br>
          <h2><?= $registeredUsers ?></h2>
        </div>
      </div>
      
      <!-- New Appraisal Requests -->
      <div class="card">
        <h3>New Appraisal Requests</h3>
        <?php if (!empty($requests)): ?>
          <?php foreach($requests as $req): ?>
            <div style="border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 10px;">
              <p><strong>User:</strong> <?= htmlspecialchars($req['user']) ?></p>
              <p><strong>Item:</strong> <?= htmlspecialchars($req['item']) ?></p>
              <p><strong>Estimate:</strong> <?= htmlspecialchars($req['estimate']) ?></p>
              <p><strong>Submitted:</strong> <?= htmlspecialchars($req['timestamp']) ?></p>
              <button class="button review-btn" data-request-id="<?= $req['id'] ?>">Review</button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No pending appraisal requests.</p>
        <?php endif; ?>
      </div>
      
      <!-- Review & Evaluate Submission -->
      <div class="card">
        <h3>Review & Evaluate Submission</h3>
        <form id="appraisalForm">
          <div class="form-group">
            <label>Select Request:</label>
            <select name="request_id" id="requestSelect" required>
              <option value="">Select a request to review</option>
              <?php foreach($requests as $req): ?>
                <option value="<?= $req['id'] ?>"><?= htmlspecialchars($req['user']) ?> - <?= htmlspecialchars($req['item']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Final Appraisal Value:</label>
            <input type="number" name="final_value" step="0.01" required />
          </div>
          <div class="form-group">
            <label>Interest Rate (%):</label>
            <input type="number" name="interest_rate" step="0.01" min="0" max="100" required />
          </div>
          <div class="form-group">
            <label>Loan Amount Offer:</label>
            <input type="number" name="loan_amount" step="0.01" required />
          </div>
          <button type="submit" class="button green">Send Appraisal Result</button>
        </form>
      </div>
      
      <!-- Pending User Responses -->
      <div class="card">
        <h3>Pending User Responses</h3>
        <?php if (!empty($pendingResponses)): ?>
          <?php foreach($pendingResponses as $response): ?>
            <div style="border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 10px;">
              <p><strong>User:</strong> <?= htmlspecialchars($response['user']) ?></p>
              <p><strong>Item:</strong> <?= htmlspecialchars($response['item']) ?></p>
              <p><strong>Offered Amount:</strong> <?= htmlspecialchars($response['offered_amount']) ?></p>
              <p><strong>Status:</strong> Waiting for user response</p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No pending user responses.</p>
        <?php endif; ?>
      </div>
      
      <!-- Branch Confirmation -->
      <div class="card">
        <h3>Branch Confirmation</h3>
        <?php if (!empty($branchConfirmations)): ?>
          <?php foreach($branchConfirmations as $confirmation): ?>
            <div style="border-bottom: 1px solid #ddd; padding: 10px 0; margin-bottom: 10px;">
              <p><strong>User:</strong> <?= htmlspecialchars($confirmation['user']) ?></p>
              <p><strong>Item:</strong> <?= htmlspecialchars($confirmation['item']) ?></p>
              <p><strong>Loan Amount:</strong> <?= htmlspecialchars($confirmation['loan_amount']) ?></p>
              <p>Item verified by branch. Ready to release loan.</p>
              <button class="button purple confirm-loan-btn" data-confirmation-id="<?= $confirmation['id'] ?>">
                Item Verified and Loan Released
              </button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No items awaiting branch confirmation.</p>
        <?php endif; ?>
      </div>
      
      <!-- Transaction History -->
      <div class="card">
        <h3>Transaction History</h3>
        <p>View and export completed sangla records.</p>
        <?php if (!empty($transactions)): ?>
          <div style="max-height: 150px; overflow-y: auto;">
            <?php foreach($transactions as $transaction): ?>
              <div style="border-bottom: 1px solid #ddd; padding: 5px 0;">
                <small>
                  <strong><?= htmlspecialchars($transaction['user']) ?></strong><br>
                  <?= htmlspecialchars($transaction['item']) ?> - <?= htmlspecialchars($transaction['amount']) ?><br>
                  <?= htmlspecialchars($transaction['date']) ?>
                </small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <button class="button" id="exportReportBtn">Export Report</button>
      </div>
    </div>
  </div>

  <!-- Single View Containers -->
  <div id="dashboardView" class="single-view">
    <div class="single-card" style="min-height:auto; padding:24px 18px;">
      <h3 style="text-align:center; font-size:2rem; margin-bottom:18px;">Dashboard Overview</h3>
      <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:16px; width:100%;">
        <div class="widget" style="flex:1 1 45%; min-width:140px; max-width:48%; text-align:center; margin:0;">
          <strong>Pending Appraisals</strong><br>
          <h2 style="font-size:1.5rem;"><?= $pendingAppraisals ?></h2>
        </div>
        <div class="widget" style="flex:1 1 45%; min-width:140px; max-width:48%; text-align:center; margin:0;">
          <strong>Approved Loans</strong><br>
          <h2 style="font-size:1.5rem;"><?= $approvedLoans ?></h2>
        </div>
        <div class="widget" style="flex:1 1 45%; min-width:140px; max-width:48%; text-align:center; margin:0;">
          <strong>Completed Transactions</strong><br>
          <h2 style="font-size:1.5rem;"><?= $completedTransactions ?></h2>
        </div>
        <div class="widget" style="flex:1 1 45%; min-width:140px; max-width:48%; text-align:center; margin:0;">
          <strong>Registered Users</strong><br>
          <h2 style="font-size:1.5rem;"><?= $registeredUsers ?></h2>
        </div>
      </div>
    </div>
  </div>

  <div id="appraisalView" class="single-view">
    <div class="single-card">
      <h3 style="text-align:center; font-size:2.2rem; margin-bottom:32px;">New Appraisal Requests</h3>
      <?php if (!empty($requests)): ?>
        <?php foreach($requests as $req): ?>
          <div style="text-align:center; margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
            <p style="font-size:1.2rem;"><strong>User:</strong> <?= htmlspecialchars($req['user']) ?></p>
            <p style="font-size:1.2rem;"><strong>Item:</strong> <?= htmlspecialchars($req['item']) ?></p>
            <p style="font-size:1.2rem;"><strong>Estimated Value:</strong> <?= htmlspecialchars($req['estimate']) ?></p>
            <p style="font-size:1.2rem;"><strong>Submitted:</strong> <?= htmlspecialchars($req['timestamp']) ?></p>
            <button class="button review-btn" data-request-id="<?= $req['id'] ?>" style="margin:10px auto;display:block;font-size:1.2rem;width:70%;">Review</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center;font-size:1.2rem;">No pending appraisal requests.</p>
      <?php endif; ?>
    </div>
  </div>

  <div id="pendingView" class="single-view">
    <div class="single-card">
      <h3 style="text-align:center; font-size:2.2rem; margin-bottom:32px;">Pending User Responses</h3>
      <?php if (!empty($pendingResponses)): ?>
        <?php foreach($pendingResponses as $response): ?>
          <div style="text-align:center; margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
            <p style="font-size:1.2rem;"><strong>User:</strong> <?= htmlspecialchars($response['user']) ?></p>
            <p style="font-size:1.2rem;"><strong>Item:</strong> <?= htmlspecialchars($response['item']) ?></p>
            <p style="font-size:1.2rem;"><strong>Offered Amount:</strong> <?= htmlspecialchars($response['offered_amount']) ?></p>
            <p style="font-size:1.2rem;"><strong>Status:</strong> Waiting for user response</p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center;font-size:1.2rem;">No pending user responses.</p>
      <?php endif; ?>
    </div>
  </div>

  <div id="branchView" class="single-view">
    <div class="single-card">
      <h3 style="text-align:center; font-size:2.2rem; margin-bottom:32px;">Branch Confirmation</h3>
      <?php if (!empty($branchConfirmations)): ?>
        <?php foreach($branchConfirmations as $confirmation): ?>
          <div style="text-align:center; margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
            <p style="font-size:1.2rem;"><strong>User:</strong> <?= htmlspecialchars($confirmation['user']) ?></p>
            <p style="font-size:1.2rem;"><strong>Item:</strong> <?= htmlspecialchars($confirmation['item']) ?></p>
            <p style="font-size:1.2rem;"><strong>Loan Amount:</strong> <?= htmlspecialchars($confirmation['loan_amount']) ?></p>
            <p style="font-size:1.2rem;">Item verified by branch. Ready to release loan.</p>
            <button class="button purple confirm-loan-btn" data-confirmation-id="<?= $confirmation['id'] ?>" style="margin:32px auto 0 auto;display:block;font-size:1.2rem;width:70%;">
              Item Verified and Loan Released
            </button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center;font-size:1.2rem;">No items awaiting branch confirmation.</p>
      <?php endif; ?>
    </div>
  </div>

  <div id="transactionsView" class="single-view">
    <div class="single-card">
      <h3 style="text-align:center; font-size:2.2rem; margin-bottom:32px;">Transaction History</h3>
      <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
        <?php if (!empty($transactions)): ?>
          <?php foreach($transactions as $transaction): ?>
            <div style="text-align:center; margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
              <p style="font-size:1.1rem;"><strong>ID:</strong> <?= htmlspecialchars($transaction['id']) ?></p>
              <p style="font-size:1.1rem;"><strong>User:</strong> <?= htmlspecialchars($transaction['user']) ?></p>
              <p style="font-size:1.1rem;"><strong>Item:</strong> <?= htmlspecialchars($transaction['item']) ?></p>
              <p style="font-size:1.1rem;"><strong>Amount:</strong> <?= htmlspecialchars($transaction['amount']) ?></p>
              <p style="font-size:1.1rem;"><strong>Date:</strong> <?= htmlspecialchars($transaction['date']) ?></p>
              <p style="font-size:1.1rem;"><strong>Status:</strong> <?= htmlspecialchars($transaction['status']) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align:center;font-size:1.2rem;">No completed transactions.</p>
        <?php endif; ?>
      </div>
      <button class="button" id="exportReportBtnSingle" style="margin:32px auto 0 auto;display:block;font-size:1.2rem;width:70%;">Export Report</button>
    </div>
  </div>

  <!-- Review Modal -->
  <div id="reviewModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3>Review Appraisal Request</h3>
      <div id="reviewModalContent">
        <!-- Content will be loaded here -->
      </div>
      <form id="modalAppraisalForm">
        <input type="hidden" name="request_id" id="modalRequestId">
        <div class="form-group">
          <label>Final Appraisal Value (₱):</label>
          <input type="number" name="final_value" step="0.01" required />
        </div>
        <div class="form-group">
          <label>Interest Rate (%):</label>
          <input type="number" name="interest_rate" step="0.01" min="0" max="100" required />
        </div>
        <div class="form-group">
          <label>Loan Amount Offer (₱):</label>
          <input type="number" name="loan_amount" step="0.01" required />
        </div>
        <button type="submit" class="button green" style="width: 100%;">Send Appraisal Result</button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Navigation functionality
      const navLinks = document.querySelectorAll('.nav-link');
      const gridView = document.getElementById('gridView');
      const singleViews = document.querySelectorAll('.single-view');

      // Show alerts
      function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        alertContainer.appendChild(alert);
        
        setTimeout(() => {
          alert.remove();
        }, 5000);
      }

      // Navigation event listeners
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove active class from all links
          navLinks.forEach(l => l.classList.remove('active'));
          // Add active class to clicked link
          this.classList.add('active');
          
          const viewType = this.getAttribute('data-view');
          
          if (viewType === 'dashboard') {
            // Show grid view for dashboard
            gridView.classList.remove('hidden');
            singleViews.forEach(view => view.classList.remove('active'));
          } else {
            // Show single view for other sections
            gridView.classList.add('hidden');
            singleViews.forEach(view => view.classList.remove('active'));
            document.getElementById(viewType + 'View').classList.add('active');
          }
        });
      });

      // Review button functionality
      const reviewButtons = document.querySelectorAll('.review-btn');
      const reviewModal = document.getElementById('reviewModal');
      const modalClose = document.querySelector('.modal .close');
      const modalContent = document.getElementById('reviewModalContent');
      const modalRequestId = document.getElementById('modalRequestId');

      reviewButtons.forEach(button => {
        button.addEventListener('click', function() {
          const requestId = this.getAttribute('data-request-id');
          
          // Find the request data (in a real app, this would come from server)
          const requests = <?= json_encode($requests) ?>;
          const request = requests.find(r => r.id == requestId);
          
          if (request) {
            modalContent.innerHTML = `
              <p><strong>User:</strong> ${request.user}</p>
              <p><strong>Item:</strong> ${request.item}</p>
              <p><strong>Estimated Value:</strong> ${request.estimate}</p>
              <p><strong>Submitted:</strong> ${request.timestamp}</p>
            `;
            modalRequestId.value = requestId;
            reviewModal.style.display = 'block';
          }
        });
      });

      // Close modal
      modalClose.addEventListener('click', function() {
        reviewModal.style.display = 'none';
      });

      window.addEventListener('click', function(event) {
        if (event.target === reviewModal) {
          reviewModal.style.display = 'none';
        }
      });

      // Appraisal form submission (both regular and modal forms)
      function handleAppraisalForm(form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          formData.append('action', 'send_appraisal');
          formData.append('ajax', '1');
          
          // Disable submit button to prevent double submission
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.textContent;
          submitBtn.disabled = true;
          submitBtn.textContent = 'Sending...';
          
          fetch(window.location.href, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showAlert(data.message, 'success');
              this.reset();
              if (reviewModal.style.display === 'block') {
                reviewModal.style.display = 'none';
              }
              // In a real app, you'd refresh the data or remove the processed request
            } else {
              showAlert(data.message || 'An error occurred', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showAlert('Network error occurred', 'error');
          })
          .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
          });
        });
      }

      // Handle both appraisal forms
      const appraisalForm = document.getElementById('appraisalForm');
      const modalAppraisalForm = document.getElementById('modalAppraisalForm');
      
      if (appraisalForm) {
        handleAppraisalForm(appraisalForm);
      }
      
      if (modalAppraisalForm) {
        handleAppraisalForm(modalAppraisalForm);
      }

      // Confirm loan release buttons
      const confirmLoanButtons = document.querySelectorAll('.confirm-loan-btn');
      confirmLoanButtons.forEach(button => {
        button.addEventListener('click', function() {
          const confirmationId = this.getAttribute('data-confirmation-id');
          
          if (confirm('Are you sure you want to confirm loan release?')) {
            const formData = new FormData();
            formData.append('action', 'confirm_loan_release');
            formData.append('confirmation_id', confirmationId);
            formData.append('ajax', '1');
            
            // Disable button
            const originalText = this.textContent;
            this.disabled = true;
            this.textContent = 'Processing...';
            
            fetch(window.location.href, {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                showAlert(data.message, 'success');
                // In a real app, you'd remove this item from the list
                this.textContent = 'Loan Released';
                this.style.background = '#28a745';
              } else {
                showAlert(data.message || 'An error occurred', 'error');
                this.disabled = false;
                this.textContent = originalText;
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showAlert('Network error occurred', 'error');
              this.disabled = false;
              this.textContent = originalText;
            });
          }
        });
      });

      // Export report buttons
      const exportButtons = document.querySelectorAll('#exportReportBtn, #exportReportBtnSingle');
      exportButtons.forEach(button => {
        button.addEventListener('click', function() {
          const formData = new FormData();
          formData.append('action', 'export_report');
          formData.append('ajax', '1');
          
          // Disable button
          const originalText = this.textContent;
          this.disabled = true;
          this.textContent = 'Exporting...';
          
          fetch(window.location.href, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showAlert(data.message, 'success');
              
              // Simulate file download (in real app, you'd generate actual file)
              const transactions = <?= json_encode($transactions) ?>;
              let csvContent = "data:text/csv;charset=utf-8,";
              csvContent += "ID,User,Item,Amount,Date,Status\n";
              
              transactions.forEach(transaction => {
                csvContent += `${transaction.id},"${transaction.user}","${transaction.item}","${transaction.amount}",${transaction.date},${transaction.status}\n`;
              });
              
              const encodedUri = encodeURI(csvContent);
              const link = document.createElement("a");
              link.setAttribute("href", encodedUri);
              link.setAttribute("download", "ipawnshop_transactions_" + new Date().toISOString().split('T')[0] + ".csv");
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);
            } else {
              showAlert(data.message || 'An error occurred', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showAlert('Network error occurred', 'error');
          })
          .finally(() => {
            // Re-enable button
            this.disabled = false;
            this.textContent = originalText;
          });
        });
      });

      // Auto-populate loan amount based on final value and interest rate
      function setupLoanCalculation(form) {
        const finalValueInput = form.querySelector('input[name="final_value"]');
        const interestRateInput = form.querySelector('input[name="interest_rate"]');
        const loanAmountInput = form.querySelector('input[name="loan_amount"]');
        
        if (finalValueInput && interestRateInput && loanAmountInput) {
          function calculateLoanAmount() {
            const finalValue = parseFloat(finalValueInput.value) || 0;
            const interestRate = parseFloat(interestRateInput.value) || 0;
            
            if (finalValue > 0) {
              // Typically loan amount is 70-80% of appraised value
              const loanAmount = finalValue * 0.75; // 75% of appraised value
              loanAmountInput.value = loanAmount.toFixed(2);
            }
          }
          
          finalValueInput.addEventListener('input', calculateLoanAmount);
          interestRateInput.addEventListener('input', calculateLoanAmount);
        }
      }
      
      // Setup loan calculation for both forms
      if (appraisalForm) {
        setupLoanCalculation(appraisalForm);
      }
      if (modalAppraisalForm) {
        setupLoanCalculation(modalAppraisalForm);
      }

      // Request select change handler
      const requestSelect = document.getElementById('requestSelect');
      if (requestSelect) {
        requestSelect.addEventListener('change', function() {
          const selectedRequestId = this.value;
          if (selectedRequestId) {
            const requests = <?= json_encode($requests) ?>;
            const selectedRequest = requests.find(r => r.id == selectedRequestId);
            
            if (selectedRequest) {
              // Pre-fill some suggested values
              const finalValueInput = appraisalForm.querySelector('input[name="final_value"]');
              const interestRateInput = appraisalForm.querySelector('input[name="interest_rate"]');
              
              // Extract numeric value from estimate (remove ₱ and commas)
              const estimatedValue = selectedRequest.estimate.replace(/[₱,]/g, '');
              finalValueInput.value = estimatedValue;
              interestRateInput.value = '3.5'; // Default interest rate
              
              // Trigger calculation
              finalValueInput.dispatchEvent(new Event('input'));
            }
          }
        });
      }
    });
  </script>
</body>
</html>