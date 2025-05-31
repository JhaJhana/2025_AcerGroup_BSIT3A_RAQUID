<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sangla Estimator</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Container Styles */
        .estimator-container {
            width: 100%;
            max-width: 900px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Header Styles */
        .estimator-header {
            background: linear-gradient(135deg, #1d4b0b 0%, #2d6a1f 100%);
            color: white;
            padding: 1rem 2rem;
            text-align: center;
        }

        .estimator-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
        }

        .estimator-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        /* Form Styles */
        .estimator-form {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 0.75rem;
        }

        .form-row {
            display: contents;
        }

        .form-row label {
            align-self: center;
            font-weight: 600;
            color: #2c3e50;
            text-align: right;
            padding-right: 0.75rem;
            font-size: 0.9rem;
        }

        .form-row input[type="text"],
        .form-row input[type="number"],
        .form-row select,
        .form-row textarea {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-row input:focus,
        .form-row select:focus,
        .form-row textarea:focus {
            border-color: #d4a437;
            box-shadow: 0 0 0 3px rgba(212, 164, 55, 0.1);
            outline: none;
        }

        .form-row textarea {
            resize: vertical;
            min-height: 60px;
            max-height: 80px;
        }

        /* File Upload Styles */
        .file-upload-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-container input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-button {
            display: inline-block;
            padding: 8px 16px;
            background: #d4a437;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 0.9rem;
        }

        .file-upload-button:hover {
            background: #c3932f;
        }

        /* Result Display */
        .estimate-result {
            background: #f8f9fa;
            padding: 0.75rem;
            border-radius: 6px;
            margin: 0.5rem 0;
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            color: #1d4b0b;
            border: 2px solid #e9ecef;
        }

        /* Button Styles */
        .buttons-row {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .button {
            padding: 8px 24px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .button-primary {
            background: #1d4b0b;
            color: white;
        }

        .button-primary:hover {
            background: #2d6a1f;
            transform: translateY(-2px);
        }

        .button-secondary {
            background: #6c757d;
            color: white;
        }

        .button-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            padding: 0.5rem;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #1d4b0b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .estimator-form {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .form-row label {
                text-align: left;
                padding-right: 0;
                margin-bottom: 0.25rem;
            }

            .buttons-row {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="estimator-container">
        <div class="estimator-header">
            <h1>Sangla Estimator</h1>
            <p>Get an instant estimate for your valuable items</p>
        </div>

        <form id="sanglaForm" action="estimate_process.php" method="POST" enctype="multipart/form-data" class="estimator-form">
            <div class="form-row">
                <label for="item_type">Item Type</label>
                <select name="item_type" id="item_type" required>
                    <option value="">Select item type</option>
                    <option value="earrings">Earrings</option>
                    <option value="necklace">Necklace</option>
                    <option value="bracelet">Bracelet</option>
                    <option value="wristwatch">Wristwatch</option>
                    <option value="rings">Rings</option>
                </select>
            </div>

            <div class="form-row">
                <label for="branch">Branch</label>
                <select name="branch" id="branch" required>
                    <option value="">Select branch</option>
                    <option value="ligao">Ligao</option>
                    <option value="polangui">Polangui</option>
                    <option value="libon">Libon</option>
                </select>
            </div>

            <div class="form-row">
                <label for="brand">Brand</label>
                <input type="text" name="brand" id="brand" placeholder="e.g., Mitana, Tala" required>
            </div>

            <div class="form-row">
                <label for="model">Model</label>
                <select name="model" id="model" required>
                    <option value="">Select model</option>
                    <option value="18k">18K</option>
                    <option value="22k">22K</option>
                    <option value="24k">24K</option>
                </select>
            </div>

            <div class="form-row">
                <label for="grams">Grams</label>
                <input type="number" step="0.01" name="grams" id="grams" placeholder="Weight in grams" required>
            </div>

            <div class="form-row">
                <label for="remarks">Remarks / Description</label>
                <textarea name="remarks" id="remarks" placeholder="Provide a short description..." required></textarea>
            </div>

            <div class="form-row">
                <label for="photo">Photo</label>
                <div class="file-upload-container">
                    <div class="file-upload-button">Choose File</div>
                    <input type="file" name="photo" id="photo" accept="image/*">
                </div>
            </div>

            <div class="form-row">
                <div class="estimate-result" id="estimateResult">
                    Estimated Value: ₱0.00
                </div>
            </div>

            <div class="loading" id="loading"></div>

            <div class="buttons-row">
                <button type="button" class="button button-secondary" onclick="window.location.href='home.php'">Back to Home</button>
                <button type="submit" class="button button-primary">Get Estimate</button>
            </div>
        </form>
    </div>

    <script>
        // Constants
        const multipliers = {
            "18k": 1800,
            "22k": 2200,
            "24k": 2500
        };

        // DOM Elements
        const form = document.getElementById('sanglaForm');
        const gramsInput = document.getElementById('grams');
        const modelSelect = document.getElementById('model');
        const estimateResult = document.getElementById('estimateResult');
        const loading = document.getElementById('loading');
        const fileInput = document.getElementById('photo');
        const fileButton = document.querySelector('.file-upload-button');

        // Update file button text when file is selected
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileButton.textContent = this.files[0].name;
            } else {
                fileButton.textContent = 'Choose File';
            }
        });

        // Update estimate in real-time
        function updateEstimate() {
            const grams = parseFloat(gramsInput.value) || 0;
            const model = modelSelect.value;
            let estimate = 0;

            if (grams > 0 && multipliers[model]) {
                estimate = grams * multipliers[model];
                estimateResult.style.color = '#1d4b0b';
            } else {
                estimateResult.style.color = '#6c757d';
            }

            estimateResult.textContent = `Estimated Value: ₱${estimate.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        }

        // Form submission handling
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            loading.style.display = 'block';
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                loading.style.display = 'none';
                // Add your form submission logic here
                alert('Estimate submitted successfully!');
            }, 1500);
        });

        // Event listeners
        gramsInput.addEventListener('input', updateEstimate);
        modelSelect.addEventListener('change', updateEstimate);

        // Initialize estimate
        updateEstimate();
    </script>
</body>
</html> 