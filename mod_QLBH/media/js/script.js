let orders = [];

function addOrder() {
  const id = document.getElementById("orderId").value;
  const name = document.getElementById("customerName").value;
  const price = document.getElementById("price").value;
  const quantity = document.getElementById("quantity").value;

  if (!id || !name || !price || !quantity) {
    alert("Vui lòng nhập đầy đủ thông tin.");
    return;
  }

  orders.push({ id, name, price, quantity });
  renderTable();
  clearForm();
}

function updateOrder() {
  const id = document.getElementById("orderId").value;
  const index = orders.findIndex(order => order.id === id);
  if (index === -1) {
    alert("Không tìm thấy đơn hàng.");
    return;
  }
  orders[index] = {
    id,
    name: document.getElementById("customerName").value,
    price: document.getElementById("price").value,  // ✅ Sửa ở đây
    quantity: document.getElementById("quantity").value,
  };
  renderTable();
  clearForm();
}

function deleteOrder() {
  const id = document.getElementById("orderId").value;
  orders = orders.filter(order => order.id !== id);
  renderTable();
  clearForm();
}

function renderTable() {
  const tbody = document.querySelector("#ordersTable tbody");
  tbody.innerHTML = "";
  orders.forEach(order => {
    const row = `<tr>
      <td>${order.id}</td>
      <td>${order.name}</td>
      <td>${order.price}</td>
      <td>${order.quantity}</td>
    </tr>`;
    tbody.innerHTML += row;
  });
}

function clearForm() {
  document.getElementById("orderForm").reset();
}

function searchProduct() {
  const keyword = document.getElementById("searchInput").value.toLowerCase();
  const rows = document.querySelectorAll("#ordersTable tbody tr");
  rows.forEach(row => {
    const product = row.children[2].textContent.toLowerCase();
    row.style.display = product.includes(keyword) ? "" : "none";
  });
}
