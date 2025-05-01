<style>
    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .reportTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .reportTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .report-tab-content {
        display: none;
        padding: 20px 0;
    }

    .report-tab-content.active {
        display: block;
    }
</style>

<h2>Factory Reports</h2>
<p>Analyze production performance and factory operations</p>

<!-- Buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">

    <div class="d-flex w-75 gap-2">
        <button class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i> Filter</button>
        <button class="btn btn-outline-primary"><i class="fa-solid fa-download"></i> Export</button>
        <button class="btn btn-outline-primary"><i class="fa-regular fa-share-from-square"></i> Share</button>
    </div>

    <div>
        <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Create Report</button>
    </div>
</div>

<!-- Report table -->
<div class="col-md-12  card p-3 shadow-sm my-4 table-responsive">

    <div class="tabs">
        <button class="reportTab active" onclick="showReportTab('production')">Production</button>
        <button class="reportTab" onclick="showReportTab('raw_materials')">Raw Materials</button>
        <button class="reportTab" onclick="showReportTab('workers')">Workers</button>
    </div>

    <!-- Production table -->
    <div id="production" class="report-tab-content active">
        <div class="row mb-4">
            <!-- Factory Performance -->
            <div class="card shadow-sm col-md-6 col-sm-6 mx-3 mb-2">
                <h5 class="mt-3">Factory Performance</h5>
                <small>Last 30 days</small>
                <div class="mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="justify-content-start">
                            <strong><small>Production Output</small></strong>
                        </div>
                        <div class="justify-content-end">
                            <small class="text-success">+7.8%</small> <!-- Dynamic value from database -->
                        </div>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-primary" style="width: 95%"></div> <!-- Dynamic value -->
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="justify-content-start">
                            <small>Target: 15,000 units</small> <!-- Dynamic value from database -->
                        </div>
                        <div class="justify-content-end">
                            <strong><small>13,800 units</small></strong> <!-- Dynamic value from database -->
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="justify-content-start">
                            <strong><small>Production Efficiency</small></strong>
                        </div>
                        <div class="justify-content-end">
                            <small class="text-success">86%</small> <!-- Dynamic value from database -->
                        </div>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-primary" style="width: 86%"></div> <!-- Dynamic value -->
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="justify-content-start">
                            <small>Target: 90%</small> <!-- Dynamic value from database -->
                        </div>
                        <div class="justify-content-end">
                            <strong><small>86%</small></strong> <!-- Dynamic value from database -->
                        </div>
                    </div>
                </div>

                <div class="my-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="justify-content-start">
                            <strong><small>Downtime</small></strong>
                        </div>
                        <div class="justify-content-end">
                            <small class="text-success">4.2%</small> <!-- Dynamic value from database -->
                        </div>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-primary" style="width: 42%"></div> <!-- Dynamic value -->
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="justify-content-start">
                            <small>Target: 3%</small> <!-- Dynamic value from database -->
                        </div>
                        <div class="justify-content-end">
                            <strong><small>4.2%</small></strong> <!-- Dynamic value from database -->
                        </div>
                    </div>
                </div>
                <hr />

                <strong>Production by Product</strong>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="justify-content-start">
                        <small>Product X</small> <!-- Dynamic value from database -->
                    </div>
                    <div class="justify-content-end">
                        <strong><small>5,200 units</small></strong> <!-- Dynamic value from database -->
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="justify-content-start">
                        <small>Product Y</small> <!-- Dynamic value from database -->
                    </div>
                    <div class="justify-content-end">
                        <strong><small>4,100 units</small></strong> <!-- Dynamic value from database -->
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="justify-content-start">
                        <small>Product Z</small> <!-- Dynamic value from database -->
                    </div>
                    <div class="justify-content-end">
                        <strong><small>3,800 units</small></strong> <!-- Dynamic value from database -->
                    </div>
                </div>
            </div>

            <!-- Production Reports -->
            <div class="card shadow-sm col-md-5 col-sm-6 ms-2 mb-2">
                <h5 class="mt-3">Production Reports</h5>
                <div class="mt-2">
                    <div class="col-md-12 col-sm-6 mb-3 card shadow-sm p-2 cards">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-regular fa-file-lines text-primary"></i> Daily Production Log</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-6 mb-3 card shadow-sm p-2 cards">
                        <div class="d-flex justify-content-between align-items-center ">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-solid fa-chart-column text-success"></i> Machine Utilization</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-6 mb-3 card shadow-sm p-2 cards">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-solid fa-arrow-up-right-dots text-primary"></i> Production Efficiency
                                </h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-6 mb-3 card shadow-sm p-2 cards">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-regular fa-file-word text-success"></i> Production Cost Analysis</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Report table -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

            <div id="recent_reports">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="justify-content-start">
                        <h5 class="mb-0">Recent Reports</h5>
                    </div>
                    <div class="justify-content-end">
                        <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-arrows-rotate"></i>
                            Refresh</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fa-regular fa-file-lines"></i> Monthly Sales Summary – March 2023<br>
                                <small class="text-muted">Sales Summary • REP-001</small>
                            </td>
                            <td>Rajesh Kumar</td>
                            <td>2023-04-01 14:30</td> <!-- Dynamic data -->
                            <td>Ready</td> <!-- Dynamic data -->
                            <td>2.4 MB</td> <!-- Dynamic data -->
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i
                                            class="fa-regular fa-eye"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i
                                            class="fa-solid fa-download"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i
                                            class="fa-solid fa-print"></i></button>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Raw Materials table -->
    <div id="raw_materials" class="report-tab-content">
        <h5 class="mt-3">Raw Materials Reports</h5>
        <div class="row">
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-regular fa-file-word text-success"></i> Inventory Status</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-solid fa-chart-column text-success"></i> Material Usage</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-regular fa-file-word text-success"></i> Stock Valuation</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-solid fa-chart-column text-danger"></i> Wastage Report</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workers table -->
    <div id="workers" class="report-tab-content">
        <h5 class="mt-3">Worker Reports</h5>
        <div class="row">
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-regular fa-file-word text-success"></i> Attendance Report</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-solid fa-chart-column text-success"></i> Performance Analysis</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-regular fa-file-word text-danger"></i> Overtime Report</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="justify-content-start ps-2">
                                <h4><i class="fa-solid fa-chart-column text-success"></i> Skills Matrix</h4>
                            </div>
                            <div class="justify-content-end pe-2">
                                <h4><i class="fa-solid fa-download text-muted"></i></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>



<script>
    // For retail store section
    function showReportTab(id) {
        const tabs = document.querySelectorAll('.reportTab');
        const contents = document.querySelectorAll('.report-tab-content');

        tabs.forEach(tab => tab.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));

        document.querySelector(`#${id}`).classList.add('active');
        document.querySelector(`[onclick="showReportTab('${id}')"]`).classList.add('active');
    }
</script>