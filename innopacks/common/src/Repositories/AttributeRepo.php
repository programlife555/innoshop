<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Common\Repositories;

use Illuminate\Support\Facades\DB;
use InnoShop\Common\Models\Attribute;

class AttributeRepo extends BaseRepo
{
    /**
     * @param  $data
     * @return mixed
     * @throws \Throwable
     */
    public function create($data): mixed
    {
        $translations = array_values($data['translations'] ?? []);

        DB::beginTransaction();

        try {
            $data      = $this->handleData($data);
            $attribute = new Attribute($data);
            $attribute->saveOrFail();

            $attribute->translations()->createMany($translations);
            DB::commit();

            return $attribute;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  mixed  $item
     * @param  $data
     * @return mixed
     */
    public function update(mixed $item, $data): mixed
    {
        $translations = array_values($data['translations'] ?? []);

        DB::beginTransaction();

        try {
            $data = $this->handleData($data);
            $item->update($data);

            if ($translations) {
                $item->translations()->delete();
                $item->saveOrFail();
                $item->translations()->createMany($translations);
            }
            DB::commit();

            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  $requestData
     * @return array
     */
    private function handleData($requestData): array
    {
        return [
            'category_id'        => $requestData['category_id']        ?? 0,
            'attribute_group_id' => $requestData['attribute_group_id'] ?? 0,
        ];
    }
}
