<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = InventoryItem::with(['product', 'latestMovement'])
            ->orderBy('stock_level')
            ->paginate(15);

        $lowStockItems = InventoryItem::where('stock_level', '<=', DB::raw('reorder_point'))
            ->with('product')
            ->get();

        return view('admin.inventory.index', compact('inventory', 'lowStockItems'));
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load(['product', 'movements' => function($query) {
            $query->with('user')->latest();
        }]);

        return view('admin.inventory.show', compact('inventory'));
    }

    public function adjust(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:add,subtract',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($inventory, $validated) {
            $quantity = $validated['type'] === 'add' ? $validated['quantity'] : -$validated['quantity'];
            
            $inventory->stock_level += $quantity;
            $inventory->save();

            InventoryMovement::create([
                'inventory_item_id' => $inventory->id,
                'quantity' => $quantity,
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id()
            ]);
        });

        return redirect()->route('admin.inventory.show', $inventory)
            ->with('success', 'Inventory adjusted successfully');
    }

    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255'
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $item) {
                $inventoryItem = InventoryItem::find($item['id']);
                $oldStock = $inventoryItem->stock_level;
                $inventoryItem->stock_level = $item['quantity'];
                $inventoryItem->save();

                InventoryMovement::create([
                    'inventory_item_id' => $item['id'],
                    'quantity' => $item['quantity'] - $oldStock,
                    'type' => 'batch_update',
                    'reason' => $validated['reason'],
                    'user_id' => auth()->id()
                ]);
            }
        });

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Batch update completed successfully');
    }

    public function export()
    {
        $inventory = InventoryItem::with(['product', 'latestMovement'])->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-report.csv"'
        ];

        $callback = function() use ($inventory) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product', 'SKU', 'Current Stock', 'Reorder Point', 'Last Movement', 'Value']);

            foreach ($inventory as $item) {
                fputcsv($file, [
                    $item->product->name,
                    $item->product->sku,
                    $item->stock_level,
                    $item->reorder_point,
                    $item->latestMovement ? $item->latestMovement->created_at : 'N/A',
                    number_format($item->stock_level * $item->unit_cost, 2)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function alerts()
    {
        $alerts = InventoryItem::where('stock_level', '<=', DB::raw('reorder_point'))
            ->with('product')
            ->get();

        return view('admin.inventory.alerts', compact('alerts'));
    }
} 