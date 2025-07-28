<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index()
    {
        $user = Auth::user();
        
        // Admin and Employee can see all orders, customers see only their own
        if ($user->hasRole('Admin') || $user->hasRole('Employee')) {
            $orders = Order::with(['user', 'orderItems'])->orderBy('created_at', 'desc')->get();
        } else {
            $orders = Order::with(['orderItems'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check if user can view this order
        if (!$user->hasRole('Admin') && !$user->hasRole('Employee') && $order->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this order.');
        }
        
        $order->load(['user', 'orderItems.product.card']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Update order status and payment status
     */
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        
        // Only Admin and Employee can update order status
        if (!$user->hasRole('Admin') && !$user->hasRole('Employee')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update order status.'
            ], 403);
        }

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            $oldStatus = $order->status;
            $oldPaymentStatus = $order->payment_status;

            // Update order status
            $order->status = $request->status;
            $order->payment_status = $request->payment_status;
            
            // Add notes if provided
            if ($request->notes) {
                $order->notes = $order->notes ? $order->notes . "\n\n" . now()->format('Y-m-d H:i:s') . " - " . $request->notes : $request->notes;
            }

            // Set shipped_at timestamp if status is shipped
            if ($request->status === 'shipped' && $oldStatus !== 'shipped') {
                $order->shipped_at = now();
            }

            // Set delivered_at timestamp if status is delivered
            if ($request->status === 'delivered' && $oldStatus !== 'delivered') {
                $order->delivered_at = now();
            }

            $order->save();

            // Log the status change
            DB::table('order_status_log')->insert([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'changed_by' => $user->id,
                'change_reason' => $request->notes ?? 'Status updated via admin panel',
                'created_at' => now()
            ]);

            // Log user activity
            DB::table('user_activity_log')->insert([
                'user_id' => $user->id,
                'activity_type' => 'order_status_update',
                'description' => "Updated order #{$order->order_number} status from {$oldStatus} to {$request->status}",
                'related_table' => 'orders',
                'related_id' => $order->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the order status.'
            ], 500);
        }
    }

    /**
     * Delete an order
     */
    public function destroy(Order $order)
    {
        $user = Auth::user();
        
        // Only admin can delete orders (based on the route middleware)
        if (!$user->hasRole('Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete orders.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Load order items if not already loaded
            $order->load('orderItems');

            // Restore inventory if order was processed
            if (in_array($order->status, ['processing', 'shipped', 'delivered'])) {
                foreach ($order->orderItems as $orderItem) {
                    // Get current stock before increment
                    $currentStock = DB::table('inventory')
                        ->where('product_id', $orderItem->product_id)
                        ->value('stock');
                    
                    // Increment the stock
                    DB::table('inventory')
                        ->where('product_id', $orderItem->product_id)
                        ->increment('stock', $orderItem->quantity);
                        
                    // Log inventory restoration
                    try {
                        DB::table('inventory_logs')->insert([
                            'product_id' => $orderItem->product_id,
                            'old_stock' => $currentStock,
                            'new_stock' => $currentStock + $orderItem->quantity,
                            'change_amount' => $orderItem->quantity,
                            'reason' => "Order #{$order->order_number} deleted - inventory restored",
                            'updated_by' => $user->id,
                            'created_at' => now()
                        ]);
                    } catch (\Exception $e) {
                        Log::warning('Could not log inventory restoration: ' . $e->getMessage());
                    }
                }
            }

            // Log user activity before deletion
            DB::table('user_activity_log')->insert([
                'user_id' => $user->id,
                'activity_type' => 'order_delete',
                'description' => "Deleted order #{$order->order_number} (Total: \${$order->total_amount})",
                'related_table' => 'orders',
                'related_id' => $order->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            // Delete related records that don't have cascade delete
            try {
                DB::table('order_status_log')->where('order_id', $order->id)->delete();
            } catch (\Exception $e) {
                Log::warning('Could not delete order_status_log records: ' . $e->getMessage());
            }
            
            try {
                DB::table('refunds')->where('order_id', $order->id)->delete();
            } catch (\Exception $e) {
                Log::warning('Could not delete refunds records: ' . $e->getMessage());
            }
            
            try {
                DB::table('inventory_adjustments')->where('order_id', $order->id)->delete();
            } catch (\Exception $e) {
                Log::warning('Could not delete inventory_adjustments records: ' . $e->getMessage());
            }

            // Delete order (this will cascade to order_items due to foreign key constraints)
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the order: ' . $e->getMessage()
            ], 500);
        }
    }
}
