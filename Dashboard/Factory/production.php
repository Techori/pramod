

    <h1>Production Management</h1>
    <p>Monitor and manage factory production lines</p>
    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-start border-3 border-primary">
                <small>Today's Output


                </small>
                <h3>1,450 units</h3>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-start border-3 border-success">
                <small>Efficiency
                </small>
                <h3>78.5%</h3>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-start border-3 border-warning">
                <small>Active Lines</small>
                <h3>2/4</h3>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 border-start border-3 border-purple"
                style="--bs-border-opacity: 1; border-color: #6f42c1;">
                <small>Workers Present</small>
                <h3>32</h3>
            </div>
        </div>
    </div>

        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold">Production Lines Status</h4>
                    <p class="text-muted mb-0">Real-time status of production line operations</p>
                </div>
                <button class="btn btn-light border" onclick="location.reload()">
                    🔄 Refresh
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Line</th>
                            <th>Status</th>
                            <th>Efficiency</th>
                            <th>Progress</th>
                            <th>Operator</th>
                            <th>Started At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Wire Production Line</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>85%</td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-fill" style="width: 81.25%;"></div>
                                </div>
                                <small>650 / 800</small>
                            </td>
                            <td>Raj Kumar</td>
                            <td>7:00 AM</td>
                            <td><span class="text-warning action-btn" onclick="toggleIcon(this)">⏸️</span>
                            </td>
                        </tr>

                        <tr>
                            <td>Cable Assembly</td>
                            <td><span class="status-badge maintenance">Maintenance</span></td>
                            <td>45%</td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-fill" style="width: 45%;"></div>
                                </div>
                                <small>270 / 600</small>
                            </td>
                            <td>Amit Sharma</td>
                            <td>8:00 AM</td>
                            <td><span class="text-success action-btn" onclick="toggleIcon(this)">▶️</span>
                            </td>
                        </tr>

                        <tr>
                            <td>Quality Control</td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>92%</td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-fill" style="width: 92%;"></div>
                                </div>
                                <small>690 / 750</small>
                            </td>
                            <td>Priya Patel</td>
                            <td>8:00 AM</td>
                            <td><span class="text-warning action-btn" onclick="toggleIcon(this)">⏸️</span>
                            </td>
                        </tr>

                        <tr>
                            <td>Packaging Line</td>
                            <td><span class="status-badge idle">Idle</span></td>
                            <td>0%</td>
                            <td>
                                <div class="progress-bar-bg">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <small>0 / 500</small>
                            </td>
                            <td>Vijay Singh</td>
                            <td>-</td>
                            <td><span class="text-success action-btn" onclick="toggleIcon(this)">▶️</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="card shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fw-bold">Production Schedule</h2>
                    <p class="text-muted">Upcoming and in-progress production runs</p>
                </div>
                <div>
                    <!-- Schedule Button -->
                    <button class="btn btn-light-custom me-2" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                        <i class="bi bi-calendar-event"></i> Schedule
                    </button>

                    <!-- Schedule Modal -->
                    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="scheduleModalLabel">Schedule Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="scheduleForm">
                                        <div class="mb-3">
                                            <label for="idInput" class="form-label">ID</label>
                                            <input type="text" class="form-control" id="idInput" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="productInput" class="form-label">Product</label>
                                            <input type="text" class="form-control" id="productInput" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="quantityInput" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="quantityInput" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="startDateInput" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" id="startDateInput" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="endDateInput" class="form-label">End Date</label>
                                            <input type="date" class="form-control" id="endDateInput" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="statusInput" class="form-label">Status</label>
                                            <select class="form-select" id="statusInput" required>
                                                <option selected disabled value="">Choose...</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Scheduled">Scheduled</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary"
                                        onclick="submitSchedule()">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-light-custom" onclick="showAllRows(this)">
                        View All
                    </button>

                </div>
            </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-muted">
                        <th>ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">PRD-001</td>
                        <td>1.5mm House Wire</td>
                        <td>2,500 m</td>
                        <td>12 Apr 2025</td>
                        <td>14 Apr 2025</td>
                        <td><span class="badge badge-in-progress">In Progress</span></td>
                        <td>
                            <span class="text-warning action-btn" onclick="toggleIcon(this)">⏸️</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">PRD-002</td>
                        <td>2.5mm Industrial Cable</td>
                        <td>1,800 m</td>
                        <td>14 Apr 2025</td>
                        <td>16 Apr 2025</td>
                        <td><span class="badge badge-scheduled">Scheduled</span></td>
                        <td>
                            <span class="text-success action-btn" onclick="toggleIcon(this)">▶️</span>
                        </td>
                    </tr>

                    <!-- Hidden rows initially -->
                    <tr class="extra-row d-none">
                        <td class="fw-bold">PRD-003</td>
                        <td>4mm Armored Cable</td>
                        <td>950 m</td>
                        <td>15 Apr 2025</td>
                        <td>17 Apr 2025</td>
                        <td><span class="badge badge-scheduled">Scheduled</span></td>
                        <td>
                            <span class="text-success action-btn" onclick="toggleIcon(this)">▶️</span>
                        </td>
                    </tr>
                    <tr class="extra-row d-none">
                        <td class="fw-bold">PRD-004</td>
                        <td>6mm Power Cable</td>
                        <td>750 m</td>
                        <td>18 Apr 2025</td>
                        <td>20 Apr 2025</td>
                        <td><span class="badge badge-scheduled">Scheduled</span></td>
                        <td>
                            <span class="text-success action-btn" onclick="toggleIcon(this)">▶️</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sidebar toggle on mobile
    document.getElementById('toggleSidebar').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });
    function refreshPage() {
        location.reload();
    }

    function toggleIcon(element) {
        if (element.innerText.trim() === '▶️') {
            element.innerText = '⏸️';
            element.classList.remove('text-success');
            element.classList.add('text-warning');
        } else {
            element.innerText = '▶️';
            element.classList.remove('text-warning');
            element.classList.add('text-success');
        }
    }

    <!-- Optional: schedule production ke liye -->
    function submitSchedule() {
        const form = document.getElementById('scheduleForm');
        if (form.checkValidity()) {
            // Get the form values
            const data = {
                id: document.getElementById('idInput').value,
                product: document.getElementById('productInput').value,
                quantity: document.getElementById('quantityInput').value,
                startDate: document.getElementById('startDateInput').value,
                endDate: document.getElementById('endDateInput').value,
                status: document.getElementById('statusInput').value,
            };

            console.log('Form Submitted:', data);

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleModal'));
            modal.hide();

            // Reset form
            form.reset();

            // TODO: Save the data to server or update UI
        } else {
            form.reportValidity(); // Show validation messages
        }
    }
    // table kka pura data dikhane ke liye
    function showAllRows() {
    const extraRows = document.querySelectorAll('.extra-row');
    extraRows.forEach(row => row.classList.remove('d-none'));
}

// action field 
function toggleIcon(element) {
        // If the icon is "▶️", change to "⏸️" and change the status to "In Progress"
        if (element.innerText.trim() === '▶️') {
            element.innerText = '⏸️';
            element.classList.remove('text-success');
            element.classList.add('text-warning');
            // Update the status badge to "In Progress"
            const row = element.closest('tr');
            row.querySelector('td:nth-child(6) .badge').innerText = 'In Progress';
            row.querySelector('td:nth-child(6) .badge').classList.remove('badge-scheduled');
            row.querySelector('td:nth-child(6) .badge').classList.add('badge-in-progress');
        } 
        // If the icon is "⏸️", change to "▶️" and change the status to "Scheduled"
        else {
            element.innerText = '▶️';
            element.classList.remove('text-warning');
            element.classList.add('text-success');
            // Update the status badge to "Scheduled"
            const row = element.closest('tr');
            row.querySelector('td:nth-child(6) .badge').innerText = 'Scheduled';
            row.querySelector('td:nth-child(6) .badge').classList.remove('badge-in-progress');
            row.querySelector('td:nth-child(6) .badge').classList.add('badge-scheduled');
        }
    }

    // Show more rows when the "View All" button is clicked
    document.getElementById('viewAllBtn').addEventListener('click', function() {
        const extraRows = document.querySelectorAll('.extra-row');
        extraRows.forEach(row => row.classList.remove('d-none'));
        this.style.display = 'none';  // Hide the "View All" button
    });
</script>