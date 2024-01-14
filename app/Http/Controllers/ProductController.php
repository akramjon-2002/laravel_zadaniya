<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function calculateProducts(Request $request): JsonResponse
    {
        $productsData = $request->input('products', []);

        $result = [];

        foreach ($productsData as $productData) {
            $productName = $productData['product_name'];
            $productQty = $productData['product_qty'];

            $result[] = $this->processProduct($productName, $productQty);
        }

        return response()->json(['result' => $result]);
    }




    protected function processProduct($productName, $quantity): array
    {
        $product = Product::where('product_name', $productName)->first();

        if (!$product) {
            return [];
        }

        $result = [
            'product_name' => $productName,
            'product_qty' => $quantity,
            'product_materials' => [],
        ];

        $usedMaterials = Cache::get('usedMaterials', []);

        foreach ($product->productMaterials()->with('warehouse')->get() as $material) {
            $neededQuantity = $material->pivot->quantity * $quantity;
            $materialData = [];


            $warehouses = $material->warehouse()->orderBy('id')->get();

            foreach ($warehouses as $warehouse) {
                $warehouseId = $warehouse->id;


                if (isset($usedMaterials[$material->id][$warehouseId])) {
                    continue;
                }

                $remainder = $warehouse->remainder;

                if ($remainder == 0) {
                    continue;
                }

                $availableQuantity = min($remainder, $neededQuantity);
                $neededQuantity -= $availableQuantity;

                $materialData[] = [
                    'warehouse_id' => $warehouseId,
                    'material_name' => $material->material_name,
                    'qty' => $availableQuantity,
                    'price' => $warehouse->price,
                ];


                $usedMaterials[$material->id][$warehouseId] = true;

                if ($neededQuantity == 0) {
                    break;
                }
            }

            if (!empty($materialData)) {
                $result['product_materials'][] = $materialData;
            }
        }
        
        Cache::put('usedMaterials', $usedMaterials, now()->addMinutes(60));

        return $result;
    }


}
