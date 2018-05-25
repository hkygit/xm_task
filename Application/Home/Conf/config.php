<?php
return array(
	//'配置项'=>'配置值'
    'READ_DATA_MAP'			=> true,   // 开启自动映射

    /*语言包*/
    'LANG_SWITCH_ON' 		=> true,   // 开启语言包功能
    'LOAD_EXT_CONFIG' => 'menu',       // 菜单配置语言包

    /*令牌验证*/
    'TOKEN_ON'      =>    true,         // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',   // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',        //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,         //令牌验证出错后是否重置令牌 默认为true

    //url配置
    'URL_MODEL'				=>	2,      //重写模式
    'URL_PATHINFO_DEPR'		=>	'-',    //更改PATHINFO参数分隔符


    /*配置数据库信息*/
    'DB_HOST'               =>  '127.0.0.1',    // 服务器地址
    'DB_NAME'               =>  'task',         // 数据库名
    'DB_USER'               =>  'root',         // 用户名
    'DB_PWD'                =>  '123456',       // 密码
    'DB_TYPE'               =>  'mysql',        // 数据库类型
    'DB_PORT'               =>  '3306',         // 端口
    'DB_PREFIX'             =>  'xm_',          // 数据库表前缀

    /*权限认证*/
    'USER_AUTH_ON'			=>	true,	                 //权限认证模式
    'USER_AUTH_TYPE' 		=>	2,	                     //1普通模式 2实时验证
    'USER_AUTH_KEY'			=>	'xm_user_access_auth',

    'NOT_AUTH_MODULE'		=>	'Passport, getRemind,File,Homenotice',
    'NOT_AUTH_ACTION'		=>	'searchUser,searchDepartment,_upload,search,searchOriginaldemand,searchProduct,searchDemand,influenceList,searchTask,searchTaskByUser,searchTaskByDemand,searchTaskByBug,searchDemandORBug,searchSource,advancedSearch,searchBug,influenceTask,searchExecuteTask,searchAnalysisTask,searchPro,advancedSearchPro,customerSearch,searchMaterielCat,advancedSearchMatCat',

    'NOT_LOGIN_MODULE'		=>	'Passport',
    'NOT_LOGIN_ACTION'		=>	'',


);