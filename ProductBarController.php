<?php

namespace App\Http\Controllers;

use App\Product;
use App\Service\CreateWord;
use Illuminate\Http\Request;


class ProductBarController extends Controller
{
    public function printBarcode()
    {
        $lims_product_list_without_variant = $this->productWithoutVariant();
        $lims_product_list_with_variant = $this->productWithVariant();
        return view('product.printbarcode', compact('lims_product_list_without_variant', 'lims_product_list_with_variant'));
    }

    public function productWithoutVariant()
    {
        return Product::ActiveStandard()->select('id', 'name', 'code')
            ->whereNull('is_variant')->get();
    }

    public function productWithVariant()
    {
        return Product::join('product_variants', 'products.id', 'product_variants.product_id')
            ->ActiveStandard()
            ->whereNotNull('is_variant')
            ->select('products.id', 'products.name', 'product_variants.item_code')
            ->orderBy('position')->get();
    }
    public function wordExport(Request $request)
    {
        $names = $request->get('names');
        $codes = $request->get('codes');
        $qtys = $request->get('qtys');
        $prices = $request->get('prices');
        $size = $request->get('size');
        $create = new CreateWord();
        return $create->create($codes, $qtys, $names, $prices, $size);
    }

    public function down()
    {
        return response()->download(public_path('word-temp/barcode.docx'));
    }
}
