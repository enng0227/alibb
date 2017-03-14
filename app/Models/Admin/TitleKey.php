<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use DB;

class TitleKey extends Model
{
    //
    protected $table = 'hdb_title_category';

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * ��ȡ����ؼ���
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTitleKeyList()
    {
        $data = DB::table('hdb_title_category')
            ->select('id', 'title_key', 'title_pid', 'status')
            ->get();
        return $data;
    }

    //��ȡ������������
    public function getFkeys($status,$userid)
    {
        if ($status == 'all') {
            $data = DB::table('hdb_title_category')
                ->select('id', 'title_key', 'title_pid', 'status')
                ->where('title_pid', 0)
                ->where('userid', $userid)
                ->get();
        } else {
            $data = DB::table('hdb_title_category')
                ->select('id', 'title_key', 'title_pid', 'status')
                ->where('title_pid', 0)
                ->where('status', $status)
                ->where('userid', $userid)
                ->get();
        }
        return $data;
    }

    //���ݷ���ID��ȡ�Ӵ�
    public function getChildrens($fid)
    {
        $data = DB::table('hdb_title_category')
            ->select('id', 'title_key', 'title_pid', 'status')
            ->where('title_pid', $fid)
            ->get();
        return $data;
    }

    //��ȡ�������ɸ�ʽ
    public function getTitleFormat()
    {
        $data = DB::table('hdb_title_format')
            ->select('id', 'title')
            ->get();
        return $data;
    }

    /**
     * ����ID��ȡ�ؼ���
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getKeyById($id)
    {
        $keys = DB::table('hdb_title_category')
            ->where('id', $id)
            ->get();
        return $keys;
    }

}
