<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductBatch extends Model
{
    protected $table = 'hdb_product_batch';

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * @return bool
     * ɾ���༭�Ĳ�Ʒ
     */
    public function deleteAllBatch()
    {
        $flag = DB::table('hdb_product_batch')->delete();
        if ($flag) {
            return true;
        }
        return false;
    }


    /**
     * @param $productId
     * @return array|static[]
     * ��ȡ��Ʒ����
     */
    public function getBatchProductAttributes($productId)
    {
        $attributes = DB::table('hdb_product_attribute_batch')
            ->where('productID', $productId)
            ->get();
        return $attributes;
    }


}


