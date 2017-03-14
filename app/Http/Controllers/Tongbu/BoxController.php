<?php

namespace App\Http\Controllers\Tongbu;

use App\Http\Controllers\Controller;
use App\Models\Admin\Product;
use Illuminate\Http\Request;
use Response;
use Session;

/**
 * create by:haohaisheng
 * Class UserController
 * date  2016/7/25
 * @package App\Http\Controllers\Tongbu
 */
class BoxController extends Controller
{
    protected $token;
    protected $userid;

    public function __construct()
    {
        $this->token = get_token(Session::get('login_alibaba'));
        $this->userid = Session::get('login_alibaba');
    }

    /**
     * �û������б�
     * @return Response
     */
    public function index()
    {
        $curr_action = $_SERVER['REQUEST_URI'];
        return view('tongbu/draft_box')->with('curaction', $curr_action);
    }

    public function fabuBox()
    {
        return view('tongbu/fabu_box');
    }

    public function editBox()
    {
        return view('tongbu/edit_box');
    }


    /**
     *
     * @param Request $request
     * @return mixed
     * ��������Ʒ�б�
     */
    public function draftBoxList(Request $request)
    {
        $product = new Product();
        $status = $request->has('status') ? $request->input('status') : '';
        $page = $request->has('page') ? $request->input('page') : 1;
        $num = 15;
        if ($status == 'fabu') {
            $products = $product->getWaitFabuList($num, $this->userid);
        } else {
            $products = $product->getWaitBatchList($num, $this->userid);
        }
        if (count($products) > 0) {
            //����
            $pro = new Product();
            $grouplist = $pro->getGroupList();
            $group = array();
            foreach ($grouplist as $val) {
                $group[$val->groupid] = $val->name;
            }
            //��Ŀ
            $catelist = $pro->getCategoryList();
            $cate = array();
            foreach ($catelist as $val) {
                $cate[$val->categoryID] = $val->enName;
            }
            foreach ($products as $val) {
                $json = json_decode($val->json_data);
                foreach ($json->attributes as $a) {
                    if ($a->attributeName == 'Model Number') {
                        $val->xinghao = $a->value;
                    }
                }
                $val->groupname = $group[$val->groupID];
                $val->catename = $cate[$val->categoryID];
            }
            return $products;
        }
        return array();
    }

    public function searchBox(Request $request)
    {
        $product = new Product();
        $key = $request->has('key') ? $request->input('key') : '';
        $status = $request->has('status') ? $request->input('status') : '';
        $page = $request->has('page') ? $request->input('page') : 1;
        $num = 15;
        $products = $product->getBoxList($num, $status, $key, $this->userid);
        if (count($products) > 0) {
            //����
            $pro = new Product();
            $grouplist = $pro->getGroupList();
            $group = array();
            foreach ($grouplist as $val) {
                $group[$val->groupid] = $val->name;
            }
            //��Ŀ
            $catelist = $pro->getCategoryList();
            $cate = array();
            foreach ($catelist as $val) {
                $cate[$val->categoryID] = $val->enName;
            }
            foreach ($products as $val) {
                $json = json_decode($val->json_data);
                foreach ($json->attributes as $a) {
                    if ($a->attributeName == 'Model Number') {
                        $val->xinghao = $a->value;
                    }
                }
                $val->groupname = $group[$val->groupID];
                $val->catename = $cate[$val->categoryID];
            }
            return $products;
        }
        return array();
    }

    /**\
     * @param $productid
     * @return \Illuminate\Http\JsonResponse
     * ɾ���ݸ����еĴ��༭������Ʒ
     */
    public function removeEditFabu($productid)
    {
        $pro = new Product();
        $flag = $pro->updateTypeBox($productid, $this->userid);
        if ($flag) {
            return response()->json(array(
                'code' => true
            ));
        }
        return response()->json(array(
            'code' => false
        ));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * ����ݸ���
     */
    public function putDraftBox()
    {
        $pro = new Product();
        $flag = $pro->putDraftBox($this->userid);
        if ($flag) {
            return response()->json(array(
                'code' => true
            ));
        }
        return response()->json(array(
            'code' => false
        ));
    }

}
