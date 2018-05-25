<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends BaseController
{
    public function index()
    {
        echo '<script style="text/javascript">location.href="'.U('Commontasks/index').'"</script>';
        exit;
    }
}