<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderSuccess extends Component
{
    public Order $order;
    public $orderNumber;

    public function mount($order)
    {
        // Find order by order number
        $this->order = Order::where('order_number', $order)
            ->where('user_id', Auth::id())
            ->with(['orderItems.product', 'user'])
            ->firstOrFail();
            
        $this->orderNumber = $this->order->order_number;
        
        // Ensure the order is loaded and available
        if (!$this->order) {
            abort(404, 'Order not found');
        }
    }

    /**
     * Continue shopping
     */
    public function continueShopping()
    {
        return redirect()->route('products.index');
    }

    /**
     * View order details
     */
    public function viewOrderDetails()
    {
        return redirect()->route('orders.show', $this->order->id);
    }

    public function render()
    {
        return view('livewire.order-success');
    }
}
