# Database Security SQL Files

This directory contains SQL scripts for setting up the **Defense-in-Depth Security System** that integrates Laravel Application-Level RBAC with MySQL Database-Level RBAC.

## 🛡️ Security Architecture

Our defense-in-depth model uses two security layers:

1. **👮 GATEKEEPER (Laravel Application Layer)**
   - Authenticates users and validates roles
   - Controls UI access and business logic  
   - First line of defense

2. **🔒 VAULT (MySQL Database Layer)**
   - Separate database users per operation type
   - MySQL enforces GRANT/REVOKE privileges
   - Final security barrier

## 📁 File Organization

### ✅ **CURRENT IMPLEMENTATION** (Use This)

| File | Purpose | Status |
|------|---------|--------|
| `defense_in_depth_current.sql` | **Main setup script** for existing tables | ✅ **ACTIVE** - Use this file |

**Command to run:**
```bash
Get-Content database/sql/defense_in_depth_current.sql | docker-compose exec -T db mysql -uroot -proot_password
```

### 🔄 **FUTURE IMPLEMENTATION** (Reference Only)

| File | Purpose | Status |
|------|---------|--------|
| `defense_in_depth_future.sql` | Complete setup for ALL tables (including future e-commerce tables) | ⚠️ **FUTURE USE** - Don't use yet |

**Use when:** After implementing products, orders, shopping_cart tables

## 🔐 MySQL Users Created

The current implementation creates these action-based database users:

| MySQL User | Action Type | Use Cases | Privileges |
|------------|-------------|-----------|------------|
| `konibui_read_only` | **READ** | View data, browse, reports | `SELECT` only |
| `konibui_data_entry` | **DATA_ENTRY** | Place orders, update profile, cart | `SELECT`, `INSERT`, limited `UPDATE` |
| `konibui_admin_ops` | **ADMIN_OPS** | Manage users, business operations | `SELECT`, `INSERT`, `UPDATE` (no DELETE) |
| `konibui_system_admin` | **SYSTEM_ADMIN** | Migrations, emergency access | `ALL PRIVILEGES` |

## 🎯 Laravel Role → Operation Mapping

| Laravel Role | Allowed Operations |
|--------------|-------------------|
| **Customer** | `READ`, `DATA_ENTRY` |
| **Employee** | `READ`, `DATA_ENTRY`, `ADMIN_OPS` |
| **Admin** | `READ`, `DATA_ENTRY`, `ADMIN_OPS`, `SYSTEM_ADMIN` |

## 🧪 Testing

After running the SQL setup, test the system:

### Web Interface
```
http://127.0.0.1:8080/test/defense-in-depth
```

### Command Line
```bash
docker-compose exec app php database/test_defense_in_depth.php
```

## 💡 Key Benefits

✅ **Defense in Depth**: Two independent security layers  
✅ **Principle of Least Privilege**: Minimal required permissions  
✅ **Breach Containment**: Database protected even if app compromised  
✅ **Granular Control**: Different operations use different privileges  
✅ **Audit Trail**: Database logs show user actions  

## 🔄 Migration Notes

- **From synchronized approach**: We moved from same-password approach to action-based for better security
- **From role-based**: Action-based provides more granular control than simple role mapping
- **Cleaned up files**: Removed redundant/outdated SQL scripts for clarity

## ⚠️ Important Notes

1. **Always use `defense_in_depth_current.sql`** for current setup
2. **Don't use future implementation** until e-commerce tables are created
3. **Test thoroughly** after any changes to database users
4. **Monitor logs** for unauthorized access attempts
5. **Review privileges** regularly for security compliance 