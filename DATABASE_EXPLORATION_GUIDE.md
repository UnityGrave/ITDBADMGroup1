# 🗄️ Database Exploration Guide for EPIC-6

## 📋 Connection Options

### Option 1: PHPMyAdmin (Easiest)
- **URL**: http://localhost:8081
- **Username**: `konibui_user`
- **Password**: `konibui_password`
- **Database**: `konibui`

### Option 2: MySQL Workbench
- **Host**: `127.0.0.1` or `localhost`
- **Port**: `3307`
- **Username**: `konibui_user`
- **Password**: `konibui_password`
- **Database**: `konibui`

---

## 🛒 EPIC-6 Database Tables

### 1. **`cart_items`** - Shopping Cart Storage
```sql
-- View all cart items
SELECT ci.*, p.sku, c.name as card_name, u.name as user_name
FROM cart_items ci
LEFT JOIN products p ON ci.product_id = p.id
LEFT JOIN cards c ON p.card_id = c.id
LEFT JOIN users u ON ci.user_id = u.id;

-- Check cart functionality
SELECT 
    u.name as customer,
    COUNT(ci.id) as items_in_cart,
    SUM(ci.quantity) as total_quantity,
    SUM(ci.quantity * p.price) as cart_total
FROM cart_items ci
JOIN users u ON ci.user_id = u.id
JOIN products p ON ci.product_id = p.id
GROUP BY u.id, u.name;
```

### 2. **`orders`** - Order Records
```sql
-- View all orders with customer info
SELECT 
    o.order_number,
    o.status,
    o.payment_method,
    o.payment_status,
    u.name as customer,
    u.email as customer_email,
    o.total_amount,
    o.created_at as order_date
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC;

-- Order summary statistics
SELECT 
    status,
    COUNT(*) as order_count,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_order_value
FROM orders 
GROUP BY status;
```

### 3. **`order_items`** - Order Line Items
```sql
-- View order items with product details
SELECT 
    o.order_number,
    oi.product_name,
    oi.product_sku,
    oi.quantity,
    oi.unit_price,
    oi.total_price,
    o.status as order_status
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
ORDER BY o.created_at DESC;

-- Popular products by order frequency
SELECT 
    oi.product_name,
    oi.product_sku,
    COUNT(*) as times_ordered,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.total_price) as total_revenue
FROM order_items oi
GROUP BY oi.product_name, oi.product_sku
ORDER BY times_ordered DESC;
```

---

## 🔍 Useful Exploration Queries

### Complete Order Flow Analysis
```sql
-- Complete order with all details
SELECT 
    o.order_number,
    o.status,
    u.name as customer,
    u.email,
    o.shipping_first_name,
    o.shipping_last_name,
    o.shipping_address_line_1,
    o.shipping_city,
    o.shipping_state,
    o.shipping_postal_code,
    o.payment_method,
    o.subtotal,
    o.tax_amount,
    o.shipping_cost,
    o.total_amount,
    o.created_at,
    -- Order items
    GROUP_CONCAT(
        CONCAT(oi.product_name, ' (', oi.quantity, 'x @ $', oi.unit_price, ')')
        SEPARATOR '; '
    ) as order_items
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id
ORDER BY o.created_at DESC;
```

### Cart Migration Verification
```sql
-- Check for any remaining session cart data (should be empty after migration)
SELECT 
    ci.*,
    u.name as user_name,
    p.sku as product_sku
FROM cart_items ci
JOIN users u ON ci.user_id = u.id
JOIN products p ON ci.product_id = p.id
WHERE ci.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

### Revenue and Sales Analysis
```sql
-- Daily sales summary
SELECT 
    DATE(created_at) as sale_date,
    COUNT(*) as orders_count,
    SUM(total_amount) as daily_revenue,
    AVG(total_amount) as avg_order_value
FROM orders
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY sale_date DESC;
```

### Product Performance
```sql
-- Best selling products
SELECT 
    p.sku,
    c.name as card_name,
    s.name as set_name,
    r.name as rarity,
    SUM(oi.quantity) as total_sold,
    SUM(oi.total_price) as total_revenue,
    COUNT(DISTINCT oi.order_id) as orders_count
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN cards c ON p.card_id = c.id
JOIN sets s ON c.set_id = s.id
JOIN rarities r ON c.rarity_id = r.id
GROUP BY p.id, p.sku, c.name, s.name, r.name
ORDER BY total_sold DESC;
```

### Customer Analysis
```sql
-- Customer order statistics
SELECT 
    u.name,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(o.total_amount) as total_spent,
    AVG(o.total_amount) as avg_order_value,
    MAX(o.created_at) as last_order_date
FROM users u
JOIN orders o ON u.id = o.user_id
GROUP BY u.id, u.name, u.email
ORDER BY total_spent DESC;
```

---

## 🧪 Test Data Verification

### Verify EPIC-6 Test Results
```sql
-- Check the test order created during comprehensive testing
SELECT 
    o.order_number,
    o.status,
    o.total_amount,
    o.created_at,
    oi.product_name,
    oi.quantity,
    oi.unit_price
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
WHERE o.order_number LIKE 'ORD-2025-%'
ORDER BY o.created_at DESC
LIMIT 5;
```

### Database Schema Verification
```sql
-- Check table structures
SHOW TABLES LIKE '%cart%';
SHOW TABLES LIKE '%order%';

-- Check table structures
DESCRIBE cart_items;
DESCRIBE orders;
DESCRIBE order_items;
```

---

## 🚀 Quick Start Commands

### Connect and Basic Exploration
1. **Connect** using one of the methods above
2. **Select Database**: `USE konibui;`
3. **Show Tables**: `SHOW TABLES;`
4. **Quick Overview**:
   ```sql
   SELECT 'cart_items' as table_name, COUNT(*) as record_count FROM cart_items
   UNION ALL
   SELECT 'orders', COUNT(*) FROM orders
   UNION ALL
   SELECT 'order_items', COUNT(*) FROM order_items;
   ```

### Most Recent Activity
```sql
-- See the latest activity across all EPIC-6 tables
SELECT 'cart_items' as table_name, MAX(created_at) as latest_activity FROM cart_items
UNION ALL
SELECT 'orders', MAX(created_at) FROM orders
UNION ALL
SELECT 'order_items', MAX(created_at) FROM order_items
ORDER BY latest_activity DESC;
```

---

## 📊 Expected Data

Based on your EPIC-6 implementation, you should see:

✅ **Cart Items**: Session and database cart storage  
✅ **Orders**: Complete order records with unique order numbers  
✅ **Order Items**: Detailed line items for each order  
✅ **Test Orders**: Orders created during testing (ORD-2025-* format)  
✅ **Customer Data**: User information linked to orders  
✅ **Product References**: SKUs and product details in order items  

---

## 🔧 Troubleshooting

- **Can't connect?** Make sure Docker containers are running: `docker-compose ps`
- **Empty tables?** Run some test orders through your application
- **Permission denied?** Double-check username/password: `konibui_user` / `konibui_password`
- **Port issues?** PHPMyAdmin is available at http://localhost:8081 as backup

---

Happy exploring! 🗄️✨ 