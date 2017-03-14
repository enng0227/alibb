<?php

namespace App\Models\Admin;

use DB;
use Illuminate\Database\Eloquent\Model;
use Log;

class Product extends Model
{
    //
    protected $table = 'hdb_product';

    protected $primaryKey = 'id';

    public $timestamps = false;

    //������Ʒ����
    public function saveAttributes($parm = array())
    {
        $flag = DB::insert('insert into hdb_product_attribute (productID,attributeID, attributeName,value,isCustom) values (?,?,?,?, ?)', [$parm['productID'], $parm['attributeID'], $parm['attributeName'], $parm['value'], $parm['value']]);
        if ($flag) {
            return true;
        }
        return false;
    }

    //������Ʒ�۸�����
    public function savePriceRange($parm = array())
    {
        $flag = DB::insert('insert into hdb_price_ranges (productID, startQuantity,price) values (?,?,?)', [$parm['productID'], $parm['startQuantity'], $parm['price']]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function saveCount($parm = array())
    {
        $flag = DB::insert('insert into hdb_count (log, token,total) values (?,?,?)', [1, $parm['token'], $parm['total']]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function getCount($token)
    {
        $data = DB::table('hdb_count')
            ->select('log', 'token', 'total')
            ->where('token', $token)
            ->get();
        return $data;
    }

    public function delCount($token)
    {
        $flag = DB::delete('delete from hdb_count where token=?', [$token]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function deleteProductById($productid, $userid)
    {
        $flag = DB::delete('delete from hdb_product where productID=? and userid=?', [$productid, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * ��ȡ������Ʒ�б�
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProductList($num, $para, $userid)
    {
        if (!empty($para)) {
            if ($para == 'online') {
                $products = DB::table('hdb_product')
                    ->where('userid', $userid)
                    ->where('status', 'online')
                    ->orWhere('status', 'published ')
                    ->orderBy('createTime', 'desc')
                    ->paginate($num);
            } else if ($para == 'all') {
                $products = DB::table('hdb_product')
                    ->where('userid', $userid)
                    ->orderBy('createTime', 'desc')
                    ->paginate($num);
            } else {
                $products = DB::table('hdb_product')
                    ->where('userid', $userid)
                    ->where('status', $para)
                    ->orderBy('createTime', 'desc')
                    ->paginate($num);
            }
        }
        return $products;
    }

    /**
     * @param $num
     * @param $para
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * �����޸�
     */
    public function getTodayProduct_bak($num, $para, $time)
    {
        if (!empty($para)) {
            if ($para == 'send') {
                $products = DB::table('hdb_product')
                    ->where('status', 'online')
                    ->where('lastUpdateTime', '', $time)
                    ->orderBy('createTime', 'desc')
                    ->paginate($num);
            } else if ($para == 'edit') {
                $date = strtotime(date('Y-m-d'));
                $products = DB::table('hdb_product')
                    ->where('lastUpdateTime', '>', $date)
                    ->orderBy('createTime', 'desc')
                    ->paginate($num);
            }
        }
        return $products;
    }


    /**
     * @param $num
     * @param $time
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * ���ղ���
     */
    public function getTodayProduct($num, $time)
    {
        $products = DB::table('hdb_product')
            ->where('lastUpdateTime', '>', $time)
            ->orderBy('createTime', 'desc')
            ->paginate($num);
        return $products;
    }

    /**
     * @param $num
     * @param $time
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * ���մ���
     */
    public function getTodayCreate($num, $time)
    {
        $products = DB::table('hdb_product')
            ->where('createTime', '>', $time)
            ->orderBy('createTime', 'desc')
            ->paginate($num);
        return $products;
    }

    /**
     * @param $num
     * @param $time
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * ���ո���
     */
    public function getTodayUpdate($num, $time)
    {
        $products = DB::table('hdb_product')
            ->where('lastUpdateTime', '>', $time)
            ->orderBy('createTime', 'desc')
            ->paginate($num);
        return $products;
    }

    /**
     * @param $num
     * @param $para
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * ��Ʒ����
     */
    public function searchProduct($num, $key, $cateid)
    {
        if (!empty($cateid)) {
            $products = DB::table('hdb_product')
                ->where('categoryID', $cateid)
                ->where('subject', 'like', '%' . $key . '%')
                ->orderBy('createTime', 'desc')
                ->paginate($num);
        } else {
            $products = DB::table('hdb_product')
                ->where('subject', 'like', '%' . $key . '%')
                ->orderBy('createTime', 'desc')
                ->paginate($num);
        }

        return $products;
    }

    /**
     * @param $num
     * @param $key
     * @param $cateid
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * ����__���ղ�������
     */
    public function searchProductToday($num, $key, $cateid, $time)
    {
        if (!empty($cateid)) {
            $products = DB::table('hdb_product')
                ->where('categoryID', $cateid)
                ->where('lastUpdateTime', '>', $time)
                ->where('subject', 'like', '%' . $key . '%')
                ->orderBy('createTime', 'desc')
                ->paginate($num);
        } else {
            $products = DB::table('hdb_product')
                ->where('lastUpdateTime', '>', $time)
                ->where('subject', 'like', '%' . $key . '%')
                ->orderBy('createTime', 'desc')
                ->paginate($num);
        }
        return $products;
    }

    /**
     * @param $productid
     * @return bool
     * ɾ����Ʒ������Ϣ
     */
    public function deleteProductBasic($productid, $userid)
    {
        $flag = DB::delete('delete from hdb_product where productID=? and userid=?', [$productid, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * @param $productid
     * @return bool
     * ɾ����Ʒ������Ϣ
     */
    public function deleteProductAttribute($productid, $userid)
    {
        $flag = DB::delete('delete from hdb_product_attribute where productID=? and userid=?', [$productid, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * @param $productid
     * @return bool
     * ���¼۸�����
     */
    public function deleteProductPriceRange($productid)
    {
        $flag = DB::delete('delete from hdb_price_ranges where productID=?', [$productid]);
        if ($flag) {
            return true;
        }
        return false;
    }


    /**
     * @param $productId
     * @return array|static[]
     * ��ȡ������Ʒ��Ϣ
     */
    public function getProductById($productId, $userid)
    {
        $product = DB::table('hdb_product')
            ->where('productID', $productId)
            ->where('userid', $userid)
            ->first();
        return $product;
    }

    /**
     * ����ID��ȡ��Ʒ
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getBatchList($ids)
    {
        $list = DB::table('hdb_product')
            ->whereIn('productID', $ids)
            ->get();
        return $list;
    }

    /**
     * @param $productId
     * @return array|static[]
     * ��ȡ��Ʒ�۸�������Ϣ
     */
    public function getProductPriceRange($productId)
    {
        $ranges = DB::table('hdb_price_ranges')
            ->where('productID', $productId)
            ->get();
        return $ranges;
    }

    /**
     * @param $productId
     * @return array|static[]
     * ��ȡ��Ʒ������Ϣ
     */
    public function getProductAttributes($productId)
    {
        $attributes = DB::table('hdb_product_attribute')
            ->where('productID', $productId)
            ->get();
        return $attributes;
    }

    /**
     * @param $productId
     * @param $json
     * @return bool
     * ���²�Ʒ��json����
     */
    public function updateJsonData($productId, $json)
    {
        $flag = DB::update('update hdb_product set json_data =? where productID = ?', [$json, $productId]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * @param $productId
     * @param $json
     * @return bool
     * �༭��Ʒjson����
     */
    public function updateBatchData($productId, $json, $userid)
    {
        $flag = DB::update('update hdb_product set batch_data =? where productID = ? and userid=?', [$json, $productId, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function updateTempJsonData($productId, $json, $userid)
    {
        $flag = DB::update('update hdb_product_temp set json_data =? where productID = ? and userid=?', [$json, $productId, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * @param $productId
     * @param $title
     * @return bool
     * ���²�Ʒ����
     */
    public function updateTitle($productId, $title)
    {
        $flag = DB::update('update hdb_product set subject =? where productID = ?', [$title, $productId]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function updateSubject1($productId, $title, $userid)
    {
        $flag = DB::update('update hdb_product set subject1 =? where productID = ? and userid=?', [$title, $productId, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * @param $ids
     * @return bool
     * ���²�Ʒ״̬Ϊ�༭״̬
     */
    public function updateType($ids)
    {
        $flag = DB::table('hdb_product')
            ->whereIn('productID', $ids)
            ->update(['type' => 2]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function removeBatchPorduct($userid)
    {
        $flag = DB::update('update hdb_product set type =1 where type=2 and  userid=?', [$userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    /**
     * @return array|static[]
     * ��ȡ�����༭��Ʒ
     */
    public function getProductBatchList($userid)
    {
        $batchs = DB::table('hdb_product')
            ->where('type', 2)
            ->where('userid', $userid)
            ->orderBy('createtime', 'desc')
            ->get();
        return $batchs;
    }


    public function saveDetailImgTemp($parm = array(), $userid)
    {
        $flag = DB::insert('insert into hdb_detail_img_temp (url, count,ids,userid) values (?,?,?,?)', [$parm['url'], $parm['count'], $parm['ids'], $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function getDetailImgTemp($userid)
    {
        $list = DB::table('hdb_detail_img_temp')
            ->where('userid', $userid)
            ->get();
        return $list;
    }

    public function updateDetailImgTemp_BAK($url, $id, $count)
    {
        $flag = DB::update('update hdb_detail_img_temp set count =count+' . $count . ' ,ids=CONCAT(ids,",",?) where url = ?', [$id, $url]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function updateDetailImgTemp($url, $id, $count)
    {
        $flag = DB::update('update hdb_detail_img_temp set count =count+1 ,ids=CONCAT(ids,",",?) where url = ?', [$id, $url]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function deleteDetailImg($userid)
    {
        DB::table('hdb_detail_img_temp')->where('userid', $userid)->delete();
    }

    public function getBatchProductByIds($ids)
    {
        $list = DB::table('hdb_product')
            ->where('type', 2)
            ->whereIn('productID', $ids)
            ->orderBy('createTime', 'desc')
            ->get();
        return $list;
    }

    /**
     * @param $token
     * @return array|static[]
     * ������λ
     */
    public function getUnit()
    {
        $data = DB::table('hdb_unit')
            ->get();
        return $data;
    }

    //��ȡ�����б�
    public function getCountry()
    {
        $data = DB::table('hdb_country')
            ->get();
        return $data;
    }

    public function getCategoryById($cateid)
    {
        $data = DB::table('hdb_category')
            ->where('parentIDs', $cateid)
            ->get();
        return $data;
    }

    public function getAllCount($userid)
    {
        $users = DB::table('hdb_product')
            ->where('userid', $userid)
            ->count();
        return $users;
    }

    public function getCategoryList()
    {
        $data = DB::table('hdb_category')
            ->get();
        return $data;
    }

    public function getGroupList()
    {
        $data = DB::table('hdb_product_group')
            ->get();
        return $data;
    }

    public function getCountTodayCreate($time, $userid)
    {
        $count = DB::table('hdb_product')
            ->where('userid', $userid)
            ->where('createTime', '>', $time)
            ->count();
        return $count;
    }

    public function getCountTodayUpdate($time, $userid)
    {
        $count = DB::table('hdb_product')
            ->where('userid', $userid)
            ->where('lastUpdateTime', '>', $time)
            ->count();
        return $count;
    }

    public function getTotalCount($userid, $userid)
    {
        $count = DB::table('hdb_product')
            ->where('userid', $userid)
            ->count();
        return $count;
    }

    //�ݸ����������Ʒ
    public function getWaitFabuList($num, $userid)
    {
        $products = DB::table('hdb_product_temp')
            ->where('userid', $userid)
            ->orderBy('createTime', 'desc')
            ->paginate($num);
        return $products;
    }

    //�ݸ���༭��������Ʒ
    public function getWaitBatchList($num, $userid)
    {
        $products = DB::table('hdb_product')
            ->where('userid', $userid)
            ->where('type', 3)
            ->orderBy('createTime', 'desc')
            ->paginate($num);
        return $products;
    }

    //�ݸ��䷢��
    public function getBoxList($num, $status, $key, $userid)
    {
        if ($status == 'edit') {
            $products = DB::table('hdb_product')
                ->where('userid', $userid)
                ->where('subject', 'like', '%' . $key . '%')
                ->where('type', 2)
                ->orderBy('createTime', 'desc')
                ->paginate($num);
        } else {
            $products = DB::table('hdb_product_temp')
                ->where('userid', $userid)
                ->where('subject', 'like', '%' . $key . '%')
                ->orderBy('createTime', 'desc')
                ->paginate($num);
        }
        return $products;
    }

    public function getProductBoxTempList($userid)
    {
        $products = DB::table('hdb_product_temp')
            ->where('userid', $userid)
            ->orderBy('createtime', 'desc')
            ->get();
        return $products;
    }

    public function updateTypeBox($productId, $userid)
    {
        $flag = DB::update('update hdb_product set type =1 where productID = ? and userid=?', [$productId, $userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function putDraftBox($userid)
    {
        $flag = DB::update('update hdb_product set type =3 where type=2 and  userid=?', [$userid]);
        if ($flag) {
            return true;
        }
        return false;
    }

    public function deleteProductByIds($ids, $userid)
    {
        $list = DB::table('hdb_product')
            ->where('userid', $userid)
            ->whereIn('productID', $ids)
            ->delete();
        return $list;
    }

    public function deleteProductAttributeByIds($ids, $userid)
    {
        $list = DB::table('hdb_product_attribute')
            ->where('userid', $userid)
            ->whereIn('productID', $ids)
            ->delete();
        return $list;
    }
}
