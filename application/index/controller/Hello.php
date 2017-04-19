<?php

namespace app\index\controller;

use think\Controller;
use think\queue\Job;
use think\Request;

class Hello
{
    public function fire(Job $job, $data) {

        db('test')->insert(['create_at' => date('Y-m-d H:i:s')]);
        db('tp5_test')->insert(['create_at' => date('Y-m-d H:i:s')]);

        $isJobDone = $this->doHelloJob($data);

        if ($isJobDone) {
            //执行成功
            $job->delete();

            print("<info>Hello Job has been done and deleted"."</info>\n");
        } else {

            if ($job->attempts()>3) {
                //通过这个方法可以检查任务已经重复试了几次
                print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
                $job->delete();
            }
        }
    }


    /**
     * 根据消息中的数据进行实际的业务处理
     *
     * @param $data　　　发布任务时自定义的数据
     * @return bool　　　任务执行的结果
     */
    private function doHelloJob($data) {

        print("<info>Hello Job Started. job Data is: ".var_export($data,true)."</info> \n");
        print("<info>Hello Job is Fired at " . date('Y-m-d H:i:s') ."</info> \n");
        print("<info>Hello Job is Done!"."</info> \n");

        return true;
    }

    public function failed($data) {
        echo '任务失败啦';
    }
}
