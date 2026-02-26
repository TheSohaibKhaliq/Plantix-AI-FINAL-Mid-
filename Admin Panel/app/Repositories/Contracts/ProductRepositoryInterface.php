<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findById(int $id): Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;

    public function featured(int $limit = 8): \Illuminate\Database\Eloquent\Collection;

    public function byVendor(int $vendorId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
}
