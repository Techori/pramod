<?php
// Backend logic abhi empty rakhenge, sirf frontend setup
include('../../_conn.php'); 
include('_retail_nav.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Purchase | Techori</title>
    <style>
        /* Yahan billing(1).html ka CSS paste karein */
        .container { max-width: 1100px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; }
        /* ... baki styles ... */
    </style>
</head>
<body>

<div class="container">
    <h2>Inventory Purchase Entry</h2>
    <form id="purchaseForm" method="POST">
        
        <div class="section">
            <h3>Supplier Details</h3>
            <div class="grid">
                <input type="text" name="supplier_name" placeholder="Supplier Name" required>
                <input type="text" name="supplier_gst" placeholder="Supplier GST No">
                <input type="text" name="mobile" placeholder="Mobile">
                <input type="text" name="city" placeholder="City">
                <input type="text" name="state" placeholder="State">
                <input type="text" name="bill_no" placeholder="Bill No">
                <input type="date" name="bill_date">
                <select id="gst_type" name="gst_type" onchange="toggleGST()">
                    <option value="with">With GST</option>
                    <option value="without">Without GST</option>
                </select>
            </div>
        </div>

        <div class="section">
            <h3>Items</h3>
            <div class="table-wrapper">
                <table id="itemTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th class="gst-col">GST %</th>
                            <th>Amount</th>
                            <th class="gst-col">GST Amt</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
            <br>
            <button type="button" class="btn add-btn" onclick="addRow()">+ Add Item</button>
        </div>

        <div class="summary">
            <p>Subtotal: ₹ <span id="subtotal">0.00</span></p>
            <p>Total GST: ₹ <span id="gstTotal">0.00</span></p>
            <hr>
            <h3>Grand Total: ₹ <span id="grand">0.00</span></h3>
            <input type="hidden" name="grand_total_hidden" id="grand_total_hidden">
        </div>

        <br>
        <button type="submit" name="save_inventory" class="btn print-btn">Save Purchase Entry</button>
    </form>
</div>

<script>
    // Yahan billing(1).html ka JavaScript (addRow, calculateTotal, toggleGST) paste karein
    // Bas calculateTotal() ke end mein ye line zaroor add karein:
    // document.getElementById('grand_total_hidden').value = finalTotal.toFixed(2);
</script>

</body>
</html>
