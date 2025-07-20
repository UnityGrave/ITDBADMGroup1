<?php

namespace App\Policies;

use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     * Admins and Employees can view all orders, Customers can view their own.
     */
    public function viewAny(User $user): bool
    {
        // Admins and Employees can view all orders
        // Customers will be filtered to see only their own orders in the controller
        return $user->hasRole('Admin') || $user->hasRole('Employee') || $user->hasRole('Customer');
    }

    /**
     * Determine whether the user can view the order.
     * Admins and Employees can view any order, Customers can view only their own.
     */
    public function view(User $user, $order = null): bool
    {
        // Admins and Employees can view any order
        if ($user->hasRole('Admin') || $user->hasRole('Employee')) {
            return true;
        }

        // Customers can only view their own orders
        // Note: When the Order model is created, this would be:
        // return $user->hasRole('Customer') && $order->user_id === $user->id;
        
        // For now, allow customers to view orders (will be refined when Order model exists)
        return $user->hasRole('Customer');
    }

    /**
     * Determine whether the user can create orders.
     * All authenticated users can create orders.
     */
    public function create(User $user): bool
    {
        // All authenticated users can place orders
        return true;
    }

    /**
     * Determine whether the user can update the order.
     * Only Admins and Employees can update orders (status changes, etc.).
     * Customers cannot update orders once placed.
     */
    public function update(User $user, $order = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can cancel the order.
     * Admins can cancel any order, Employees can cancel orders,
     * Customers can cancel their own orders if still pending.
     */
    public function cancel(User $user, $order = null): bool
    {
        // Admins and Employees can cancel any order
        if ($user->hasRole('Admin') || $user->hasRole('Employee')) {
            return true;
        }

        // Customers can cancel their own orders
        // Note: When Order model exists, add status check:
        // return $user->hasRole('Customer') && $order->user_id === $user->id && $order->status === 'pending';
        
        return $user->hasRole('Customer');
    }

    /**
     * Determine whether the user can delete the order.
     * Only Admins can delete orders (for cleanup/admin purposes).
     */
    public function delete(User $user, $order = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the order.
     * Only Admins can restore deleted orders.
     */
    public function restore(User $user, $order = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the order.
     * Only Admins can permanently delete orders.
     */
    public function forceDelete(User $user, $order = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update order status.
     * Only Admins and Employees can change order status.
     */
    public function updateStatus(User $user, $order = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can process the order.
     * Only Admins and Employees can process orders.
     */
    public function process(User $user, $order = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can ship the order.
     * Only Admins and Employees can ship orders.
     */
    public function ship(User $user, $order = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can refund the order.
     * Only Admins can process refunds.
     */
    public function refund(User $user, $order = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can view order analytics.
     * Only Admins and Employees can view order analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can export orders.
     * Only Admins can export order data.
     */
    public function export(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
