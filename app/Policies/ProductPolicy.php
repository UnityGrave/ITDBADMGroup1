<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     * All authenticated users can view products.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can browse products
        return true;
    }

    /**
     * Determine whether the user can view the product.
     * All authenticated users can view individual products.
     */
    public function view(User $user, $product = null): bool
    {
        // All authenticated users can view individual products
        return true;
    }

    /**
     * Determine whether the user can create products.
     * Only Admins and Employees can create products.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can update the product.
     * Only Admins and Employees can update products.
     */
    public function update(User $user, $product = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can delete the product.
     * Only Admins can delete products.
     */
    public function delete(User $user, $product = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the product.
     * Only Admins can restore deleted products.
     */
    public function restore(User $user, $product = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the product.
     * Only Admins can permanently delete products.
     */
    public function forceDelete(User $user, $product = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can manage product inventory.
     * Only Admins and Employees can manage inventory.
     */
    public function manageInventory(User $user, $product = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can set product pricing.
     * Only Admins can set pricing.
     */
    public function setPricing(User $user, $product = null): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can publish/unpublish products.
     * Only Admins and Employees can control product visibility.
     */
    public function toggleVisibility(User $user, $product = null): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Employee');
    }

    /**
     * Determine whether the user can add the product to cart.
     * All authenticated users can add products to their cart.
     */
    public function addToCart(User $user, $product = null): bool
    {
        // All authenticated users can add products to cart
        return true;
    }

    /**
     * Determine whether the user can purchase the product.
     * All authenticated users can purchase products.
     */
    public function purchase(User $user, $product = null): bool
    {
        // All authenticated users can purchase products
        return true;
    }
}
