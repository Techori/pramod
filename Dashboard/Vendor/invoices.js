        // Invoice Generation Script
        function addInvoiceItem() {
            const newRow = `
        <div class="item-row row mb-2">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Item Name" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control quantity" placeholder="Qty" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control price" placeholder="Price" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control gst-rate" placeholder="GST %" required>
            </div>
            <div class="col-md-2">
                <span class="item-total">₹0.00</span>
                <button type="button" class="btn btn-link text-danger btn-sm" onclick="removeItem(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
            document.getElementById('invoiceItems').insertAdjacentHTML('beforeend', newRow);
        }

        function removeItem(button) {
            button.closest('.item-row').remove();
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            let totalGst = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const gstRate = parseFloat(row.querySelector('.gst-rate').value) || 0;

                const itemTotal = quantity * price;
                const itemGst = itemTotal * (gstRate / 100);

                subtotal += itemTotal;
                totalGst += itemGst;

                row.querySelector('.item-total').textContent = `₹${itemTotal.toFixed(2)}`;
            });

            const grandTotal = subtotal + totalGst;

            document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
            document.getElementById('totalGst').textContent = `₹${totalGst.toFixed(2)}`;
            document.getElementById('grandTotal').textContent = `₹${grandTotal.toFixed(2)}`;
        }

        function generateInvoice() {
            const invoiceData = {
                type: document.querySelector('input[name="invoiceType"]:checked').value,
                date: document.getElementById('invoiceDate').value,
                customer: {
                    name: document.getElementById('customerName').value,
                    gstin: document.getElementById('customerGstin').value
                },
                items: [],
                totals: {
                    subtotal: document.getElementById('subtotal').textContent,
                    gst: document.getElementById('totalGst').textContent,
                    total: document.getElementById('grandTotal').textContent
                }
            };

            document.querySelectorAll('.item-row').forEach(row => {
                invoiceData.items.push({
                    name: row.querySelector('input[placeholder="Item Name"]').value,
                    quantity: row.querySelector('.quantity').value,
                    price: row.querySelector('.price').value,
                    gst: row.querySelector('.gst-rate').value,
                    total: row.querySelector('.item-total').textContent
                });
            });

            // Mock invoice generation
            const invoiceNumber = 'INV-' + Math.floor(Math.random() * 10000);

            // Show success message
            const modal = bootstrap.Modal.getInstance(document.getElementById('generateInvoiceModal'));
            modal.hide();

            // Show download options
            showDownloadOptions(invoiceNumber, invoiceData);
        }

        function showDownloadOptions(invoiceNumber, invoiceData) {
            const downloadModal = `
        <div class="modal fade" id="downloadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Invoice Generated Successfully</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Invoice ${invoiceNumber} has been generated successfully!</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="downloadInvoice('pdf', '${invoiceNumber}')">
                                <i class="fas fa-file-pdf"></i> Download as PDF
                            </button>
                            <button class="btn btn-secondary" onclick="downloadInvoice('excel', '${invoiceNumber}')">
                                <i class="fas fa-file-excel"></i> Download as Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', downloadModal);
            const modal = new bootstrap.Modal(document.getElementById('downloadModal'));
            modal.show();

            // Remove modal from DOM after it's hidden
            document.getElementById('downloadModal').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        }

        function downloadInvoice(format, invoiceNumber) {
            // Mock download process
            const message = `Downloading invoice ${invoiceNumber} in ${format.toUpperCase()} format...`;
            alert(message);
        }

        // Add event listeners for real-time calculation
        document.addEventListener('input', function (e) {
            if (e.target.matches('.quantity, .price, .gst-rate')) {
                calculateTotals();
            }
        }

document.querySelectorAll('input[name="invoiceType"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const gstInputs = document.querySelectorAll('.gst-rate');
                const gstinInput = document.getElementById('customerGstin');

                if (this.value === 'non-gst') {
                    gstInputs.forEach(input => {
                        input.value = '0';
                        input.disabled = true;
                    });
                    gstinInput.disabled = true;
                    gstinInput.value = '';
                } else {
                    gstInputs.forEach(input => {
                        input.disabled = false;
                    });
                    gstinInput.disabled = false;
                }
                calculateTotals();
            });
        });
);
