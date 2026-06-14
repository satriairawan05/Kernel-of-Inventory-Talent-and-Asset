<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Unit;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_service_can_store_update_destroy_and_search_products(): void
    {
        $company = Company::create([
            'company_name' => 'Test Company',
            'company_email' => 'test@example.com',
            'company_phone' => '08123456789',
            'company_address' => 'Jl. Test',
            'company_logo' => null,
            'use_menu' => true,
            'use_service' => true,
            'use_inventory' => true,
        ]);

        $category = Category::create([
            'company_id' => $company->id,
            'category_name' => 'Accessories',
            'description' => 'Accessories test',
        ]);

        $unit = Unit::create([
            'unit_name' => 'Pcs',
            'unit_code' => 'PCS',
            'description' => 'Pieces',
            'is_active' => true,
        ]);

        $service = new ProductService();

        $product = $service->store([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'product_name' => 'Mouse Wireless',
            'product_code' => 'MW-001',
            'description' => 'Mouse wireless',
            'has_variant' => false,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', ['product_code' => 'MW-001']);

        $searchResult = $service->search('Mouse');
        $this->assertTrue($searchResult->contains('id', $product->id));

        $searchByCode = $service->search('MW-001');
        $this->assertTrue($searchByCode->contains('id', $product->id));

        $updated = $service->update($product, [
            'company_id' => $company->id,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'product_name' => 'Mouse Wireless Updated',
            'product_code' => 'MW-001-UPDATED',
            'description' => 'Updated desc',
            'has_variant' => true,
            'is_active' => false,
        ]);

        $this->assertSame('Mouse Wireless Updated', $updated->product_name);
        $this->assertSame('MW-001-UPDATED', $updated->product_code);

        $this->assertTrue($service->destroy($product));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
