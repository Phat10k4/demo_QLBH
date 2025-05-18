<?php
defined('_JEXEC') or die;

// Đường dẫn file JSON lưu đơn hàng
$jsonFile = JPATH_ROOT . '/modules/mod_QLBH/orders.json'; // Thay 'mod_yourmodulename' bằng tên module của bạn

// Đọc dữ liệu đơn hàng hiện tại
$orders = [];
if (file_exists($jsonFile)) {
    $orders = json_decode(file_get_contents($jsonFile), true);
}

// Xử lý thêm, sửa, xóa qua POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $order_code = $_POST['order_code'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $quantity = $_POST['quantity'] ?? '';

    if ($action === 'add') {
        // Kiểm tra order_code chưa tồn tại mới thêm
        $exists = false;
        foreach ($orders as $order) {
            if ($order['order_code'] === $order_code) {
                $exists = true;
                break;
            }
        }
        if (!$exists && $order_code !== '') {
             $orders[] = [
                'order_code' => $order_code,
                'product_name' => $product_name,
                'price' => $price,
                'quantity' => $quantity,
            ];
        }
    } elseif ($action === 'update') {
        // Sửa đơn hàng theo order_code
        foreach ($orders as &$order) {
            if ($order['order_code'] === $order_code) {
                $order['product_name'] = $product_name;
                $order['price'] = $price;
                $order['quantity'] = $quantity;
                break;
            }
        }
        unset($order);
    } elseif ($action === 'delete') {
        // Xóa đơn hàng theo order_code
        foreach ($orders as $key => $order) {
            if ($order['order_code'] === $order_code) {
                unset($orders[$key]);
                break;
            }
        }
        // Reset lại mảng
        $orders = array_values($orders);
    }

    // Ghi lại file JSON
    $result = file_put_contents($jsonFile, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    if ($result === false) {
        JFactory::getApplication()->enqueueMessage('Lỗi khi ghi file orders.json: ' . error_get_last()['message'], 'error');
    } else {
        JFactory::getApplication()->enqueueMessage('Đã ghi dữ liệu thành công vào orders.json', 'message');
    }

    // Chuyển hướng để tránh submit lại form khi reload
    $app = JFactory::getApplication();
    $url = JRoute::_('index.php', false); // Chuyển hướng về trang chủ hoặc trang bạn muốn.
    $app->redirect($url);
    exit;
}
?>

<div class="order-form">
    <h2>Quản lý Đơn hàng</h2>
    <form id="orderForm" method="post" onsubmit="return validateForm()">
        <input type="hidden" name="action" id="formAction" value="add">
        <label for="orderId">Mã đơn hàng:</label>
        <input type="text" id="orderId" name="order_code" placeholder="VD: DH001" required>

        <label for="productName">Sản phẩm:</label>
        <input type="text" id="productName" name="product_name" placeholder="Tên sản phẩm" required>

        <label for="price">Giá:</label>
        <input type="text" id="price" name="price" placeholder="Giá sản phẩm" required>

        <label for="quantity">Số lượng:</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1" required>

        <div class="button-group" style="margin-top: 10px;">
            <button type="submit" onclick="setAction('add')">Thêm</button>
            <button type="submit" onclick="setAction('update')">Sửa</button>
            <button type="submit" onclick="setAction('delete')">Xóa</button>
        </div>
    </form>
</div>

<div class="order-list" style="margin-top: 30px;">
    <h2>Danh sách đơn hàng</h2>

    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." onkeyup="searchProduct()">

    <table id="ordersTable" border="1" cellpadding="5" cellspacing="0" style="margin-top: 10px; width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Chọn</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_code']); ?></td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['price']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><button type="button" onclick="fillForm('<?php echo addslashes($order['order_code']); ?>', '<?php echo addslashes($order['product_name']); ?>', '<?php echo addslashes($order['price']); ?>', '<?php echo addslashes($order['quantity']); ?>')">Chọn</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                 <tr><td colspan="5" style="text-align: center;">Không có đơn hàng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Set action ẩn trong form trước khi submit
function setAction(action) {
    document.getElementById('formAction').value = action;
}

// Fill form khi click chọn trong bảng
function fillForm(code, product, price, quantity) {
    document.getElementById('orderId').value = code;
    document.getElementById('productName').value = product;
    document.getElementById('price').value = price;
    document.getElementById('quantity').value = quantity;
}

// Tìm kiếm theo sản phẩm
function searchProduct() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#ordersTable tbody tr");
    rows.forEach(row => {
        const productCell = row.cells[1].textContent.toLowerCase();
        row.style.display = productCell.includes(input) ? "" : "none";
    });
}

// Kiểm tra form trước submit
function validateForm() {
    const orderId = document.getElementById('orderId').value.trim();
    if (!orderId) {
        alert('Mã đơn hàng không được để trống!');
        return false;
    }
    return true;
}
</script>
