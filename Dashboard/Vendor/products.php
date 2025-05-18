<?php
require_once 'database.php';
$all_products = get_products();
$categories = ["All Categories", "Wires", "Switches", "Lights", "Fans", "Appliances", "Accessories", "Conduits"];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'All Categories';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'asc';

// Filter products
$filtered_products = array_filter($all_products, function($product) use ($search, $category) {
    $search_match = stripos($product['name'], $search) !== false || stripos($product['sku'], $search) !== false;
    $category_match = $category === 'All Categories' || $product['category'] === $category;
    return $search_match && $category_match;
});

// Sort products
usort($filtered_products, function($a, $b) use ($sort, $dir) {
    $a_val = $a[$sort];
    $b_val = $b[$sort];
    if ($a_val < $b_val) return $dir === 'asc' ? -1 : 1;
    if ($a_val > $b_val) return $dir === 'asc' ? 1 : -1;
    return 0;
});


function get_status_class($status) {
    switch ($status) {
        case 'In Stock': return 'bg-success';
        case 'Low Stock': return 'bg-warning';
        case 'Out of Stock': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function build_sort_url($field) {
    global $search, $category, $sort, $dir;
    $new_dir = ($sort === $field && $dir === 'asc') ? 'desc' : 'asc';
    $params = [
        'page' => 'products',
        'search' => $search,
        'category' => $category,
        'sort' => $field,
        'dir' => $new_dir
    ];
    return '?' . http_build_query($params);
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-box text-primary"></i> Product Management</h1>
            <p>Manage your product catalog, update inventory and pricing.</p>
        </div>
        <button class="btn btn-primary" onclick="alert('Adding new product')"><i class="fas fa-plus"></i> Add Product</button>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="page" value="products">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Products</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="productsSearch" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or SKU...">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Filter by Category</label>
                        <select class="form-select" id="category" name="category">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c; ?>" <?php echo $category === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="alert('Importing products')"><i class="fas fa-file-import"></i> Import</button>
                        <button type="button" class="btn btn-outline-secondary" onclick="alert('Exporting products')"><i class="fas fa-file-export"></i> Export</button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
                <div class="text-end">
                    <a href="?page=products" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Product Inventory (<?php echo count($filtered_products); ?>)</h5>
            <?php if (empty($filtered_products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box fa-3x text-muted"></i>
                    <p class="mt-2 text-muted">No products found matching your criteria.</p>
                    <a href="?page=products" class="btn btn-primary">Clear Filters</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="productsTable">
                        <thead>
                            <tr>
                                <th><a href="<?php echo build_sort_url('name'); ?>">Product Name <?php if ($sort === 'name') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('sku'); ?>">SKU <?php if ($sort === 'sku') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('category'); ?>">Category <?php if ($sort === 'category') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('price'); ?>">Price <?php if ($sort === 'price') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('stock'); ?>">Stock <?php if ($sort === 'stock') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered_products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                                    <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <span class="badge <?php echo get_status_class($product['status']); ?>">
                                            <?php echo $product['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="alert('Editing product <?php echo htmlspecialchars($product['name']); ?>')"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="alert('Deleting product <?php echo htmlspecialchars($product['name']); ?>')"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                         <script>
                // Search Functionality
                    document.getElementById('productsSearch').addEventListener('input', function () {
                        const searchText = this.value.toLowerCase();
                        const rows = document.querySelectorAll('#productsTable tbody tr');

                        rows.forEach(row => {
                            const cells = row.getElementsByTagName('td');
                            let match = false;
                            for (let i = 0; i < cells.length; i++) {
                                if (cells[i].textContent.toLowerCase().includes(searchText)) {
                                    match = true;
                                    break;
                                }
                            }
                            row.style.display = match ? '' : 'none';
                        });
                    });
                  </script>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>