<?php
return array(
    '_TOKEN_ERROR_'          => '操作失败，请刷新后再试一次',

    'action_log_diff1' 	=>	'修改了 <strong><i>%s</i></strong>，旧值为 "%s"，新值为 "%s"。<br />' . "\n",
    'action_log_diff2'	=>	'修改了 <strong><i>%s</i></strong>，区别为：' . "\n" . "<blockquote>%s</blockquote>" . "\n<div class='hide'>%s</div>",
    'single_work_task' => '单人任务',
    'more_user_work_task' => '多人任务',
    'first_work_task' => '初始发起任务',
    'sale_reservation' => '预估',
    'attr_group_name' => '属性项分组',
    'change_old_file_url' => '|(<img.*?src=\")\/?Public\/Uploads\/(\d{4}\/\d{2}\/\d{2})(\/.*?\".*?\/>)|',

    'PRODUCT_SALE_REPORT' => array(
        'product_delivery_num' => '出货量',
        'product_order_num' => '订单量',
        'history_sale_num' => '历史出货量90天均线',
        'prediction_sale_num' => '正常预测出货量',
        'temp_sale_num' => '临时预测量',
        'abnormal_sale_num' => '异常预测变更量',
        'reservation_num'  => '预约数量',
        'change_sale_num' => array(
            'name' => '预测干预岗位',
            'value' => array(
                '0' => '物控计划干预量',
                '1' => '产品经理干预量',
                '2' => '产品委员会干预量'
            )
        ),
        'final_sale_num' => '最终预测出货量',
        'product_report_remark' => '出货量数据更新截止到'.date('Y-m-d').'凌晨，预测信息每小时更新一次。其中未出货量数据从2017-01-01开始统计，订单量以及入库量从2016-01-01开始统计。',
        'product_report_remark_details' => '出货量数据更新截止到'.date('Y-m-d').'凌晨，预测信息每小时更新一次。其中未出货量数据从2017-01-01开始统计，订单量以及入库量从2016-01-01开始统计。历史出货量90天均线表示每个时间点往前90天的平均天出货量（每月的值为每天的90天均线累加）',
        'product_report_remark_normal' => '以上数据截止到'.date('Y-m-d').'凌晨，信息每小时更新一次',
        'product_combination_report_remark' => '出货量数据更新截止到'.date('Y-m-d').'凌晨，干预预测信息每小时更新一次',
        'product_source_report_remark' => '出货量数据更新截止到'.date('Y-m-d').'凌晨，预测信息每小时更新一次',
        'product_cunstomerSource_report_remark' => '出货量数据更新截止到'.date('Y-m-d').'凌晨，预测和干预信息每小时更新一次',
    ),

    'MATERIAL_PURCHASE_REPORT' => array(
        'material_delivery_num' => '入库量',
        'material_out_num' => '出货量',
        'material_forecast_num' => '预测需求量',
        'material_order_num' => '采购订单量',
        'material_inventory_num' => '历史库存(实际库存+在途调货)',
        'material_in_forecast_num' => '最终采购预测量',
        'material_report_remark' => '采购数据更新截止到'.date('Y-m-d').'凌晨，预测信息每小时更新一次。其中出货量数据从2017-01-01开始统计，入库量从2016-01-01开始统计。',
        'material_report_remark_details' => '采购数据更新截止到'.date('Y-m-d').'凌晨，预测信息每小时更新一次。其中未出货量数据从2017-01-01开始统计，订单量以及入库量从2016-01-01开始统计。历史出货量90天均线表示每个时间点往前90天的平均天出货量（每月的值为每天的90天均线累加）',

    ),

    'PRODUCT_PURCHASE_REPORT' => array(
        'product_delivery_num' => '入库量',
        'product_out_num' => '出库量',
        'product_forecast_num' => '预测需求量',
        'product_order_num' => '采购订单量',
        'product_inventory_num' => '历史库存',
        'product_in_forecast_num' => '最终采购预测量',
        'product_report_remark' => '采购数据更新截止到'.date('Y-m-d').'凌晨，预测信息每小时更新一次。其中出货量数据从2017-01-01开始统计，入库量从2016-01-01开始统计。',
    ),
    'MATERIAL_FIELD_DESCRIBE' => array(
        'status' 			=> '生命周期',
        'customized_type' 	=> '属性',
    ),
    'PRODUCT_FIELD_DESCRIBE' => array(
        'status' => '生命周期',
        'customized_type' => '属性'
    ),
    'CONTROL_MATERIEL_LANGUAGE' => array(
        'product_size' => '产品尺寸',
        'min_bags_num' => '最小包装数量',
        'big_bags_size' => '大包装尺寸'
    ),

    //常会被修改的字段
    'MAY_CHANGE_WORD' => array(
        'procurement_word' => '下单未提货',
    ),

    'ACTION_HISTORY' => array(
        'common' => array(
            'created'		=>	'{$date}, 由 <strong>{$actor}</strong> 创建。' . "\n",
            'edited'		=>	'{$date}, 由 <strong>{$actor}</strong> 编辑。' . "\n",
            'changed'		=>	'{$date}, 由 <strong>{$actor}</strong> 变更。' . "\n",
            'assigned'		=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 指派给 <strong>{$extra}</strong>。' . "\n", 'extra' => array('func' => 'getUserById'), 'main_1' => '{$date}, 由 <strong>{$actor}</strong> 取消了指派'),
            'closed'		=>	'{$date}, 由 <strong>{$actor}</strong> 关闭。' . "\n",
            'deleted'		=>	'{$date}, 由 <strong>{$actor}</strong> 删除。' . "\n",
            'undeleted'		=>	'{$date}, 由 <strong>{$actor}</strong> 还原。' . "\n",
            'commented'		=>	'{$date}, 由 <strong>{$actor}</strong> 添加了备注。' . "\n",
            'deletedfile'	=>	'{$date}, 由 <strong>{$actor}</strong> 删除了附件：<strong><i>{$extra}</i></strong>。' . "\n",
            'editfile'		=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了附件：<strong><i>{$extra}</i></strong>。' . "\n",
            'activated'		=>	'{$date}, 由 <strong>{$actor}</strong> 激活。' . "\n",
            'confirmed'		=>	'{$date}, 由 <strong>{$actor}</strong> 确认通过需求变更，最新版本为<strong>#{$extra}</strong>。' . "\n",
            'bugconfirmed'	=> 	'{$date}, 由 <strong>{$actor}</strong> 确认Bug。' . "\n",
            'started'		=>	'{$date}, 由 <strong>{$actor}</strong> 开启。' . "\n",
            'restarted'		=>	'{$date}, 由 <strong>{$actor}</strong> 继续。' . "\n",
            'paused'		=>	'{$date}, 由 <strong>{$actor}</strong> 暂停。' . "\n",
            'recordestimate'=>	'{$date}, 由 <strong>{$actor}</strong> 记录工时，消耗 <strong>{$extra}</strong> 小时。' . "\n",
            'deleteestimate'=>	'{$date}, 由 <strong>{$actor}</strong> 删除了一条工时。' . "\n",
            'editworktime'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了一条工时。'. "\n",
            'canceled'		=>	'{$date}, 由 <strong>{$actor}</strong> 取消。' . "\n",
            'finished'		=>	'{$date}, 由 <strong>{$actor}</strong> 完成。' . "\n",
            'editworkrel'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了任务关系。' . "\n",
            'editquestioncommon'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了定时通用任务信息。' . "\n",
            'apply_change_operator'	=>	'{$date}, 由 <strong>{$actor}</strong> 申请变更执行人。' . "\n",
        ),
        'demand' => array(
            'action' => array(
                'reviewed'		=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 评审，结果为 <strong>{$extra}</strong>。', 'extra' => 'reviewResultList'),
                'closed'		=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 关闭，原因为 <strong>{$extra}</strong>。', 'extra' => 'resolutionList'),
            ),
            'reviewResultList' => array(
                'pass'			=>	'确认通过',
                'reject'		=>	'拒绝',
                'reviewed_again'=>	'需进一步评审',
                'clarify'		=>	'有待明确',
                'suspended'		=>	'暂时搁置'
            ),
            'resolutionList' => array(
                '1'	=>	'已完成',
                '2'	=>	'已细分',
                '3'	=>	'需求重复',
                '4'	=>	'延期',
                '5'	=>	'不做',
                '6'	=>	'设计如此',
                '7'	=>	'其它'
            )
        ),
        'bug' => array(
            'action' => array(
                'resolved'		=>	array('main' => '{$date}, 由 <strong>$actor</strong> 关闭，关闭原因为 <strong>$extra</strong>。', 'extra' => 'resolutionList'),
                'reviewed'		=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 分析，结果为 <strong>{$extra}</strong>。', 'extra' => 'reviewResultList'),
                'finished'		=>	'{$date}, 由 <strong>{$actor}</strong> 解决。'
            ),
            'resolutionList' => array(
                '1'	=>	'已解决',
                '2'	=>	'设计如此',
                '3'	=>	'重复bug',
                '4'	=>	'外部原因',
                '5'	=>	'无法重现',
                '6'	=>	'延期处理',
                '7'	=>	'不予解决',
                '8'	=>	'非问题',
                '9' =>	'转需求'
            ),
            'reviewResultList' => array(
                'pass'			=>	'确认通过',
                'reject'		=>	'拒绝',
                'reviewed_again'=>	'需进一步分析',
                'clarify'		=>	'有待明确',
                'suspended'		=>	'暂时搁置'
            ),
            'regression_result' 	=> array(
                'pass' => '回归通过',
                'reject' => '回归不通过'
            )
        ),
        'task' => array(
            'action' => array(
                'rdemandclosed'	=>	'{$date}, 相关需求{$extra}被关闭' . "\n",
                'rbugclosed'	=>	'{$date}, 相关bug{$extra}被关闭' . "\n"
            )
        ),
        'originalDemand' => array(
            'action' => array(
                'reviewed'		=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 审核，结果为 <strong>{$extra}</strong>。', 'extra' => 'reviewResultList')
            ),
            'reviewResultList' => array(
                '1'			=>	'已审阅',
                '-2'		=>	'拒绝'
            )
        ),
        'work' => array(
            'action' => array(
                'reviewed'				=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 评审，结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'reviewResultList'),
                'pause'  				=>	'{$date}, 由 <strong>{$actor}</strong> 暂停了上级任务<strong>{$extra}</strong>。'. "\n",
                //'restarte'  			=>	'{$date}, 由 <strong>{$actor}</strong> 继续了上级任务<strong>{$extra}</strong>。'. "\n",
                'recordestimated'		=>	'{$date}, 由 <strong>{$actor}</strong> 记录了<strong>{$extra}</strong>的工时。' . "\n",
                'child_reject_change'	=> 	'{$date}, 子任务{$extra}拒绝了任务变更，任务变更需重新评审。'. "\n",
                'canceled_child_task'	=>	'{$date}, 任务取消， 原因：上级任务{$extra}取消。'. "\n",
                'part_in_reviewed_null'		=>	array('main'=>'{$date}, <strong>{$actor}</strong> 参与了任务评审，评审意见为 <strong>{$extra}</strong>。'. "\n",'extra' => 'partinReviewResultList'),
                'part_in_reviewed'	=>	'{$date}, <strong>{$actor}</strong> 参与了任务评审。'. "\n",
                'choose_questions'		=>	'{$date}, 由 <strong>{$actor}</strong> 选择了考试题' . "\n",
                'edit_choose_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了考试题' . "\n",
                'choose_qnaire_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 选择了问卷题' . "\n",
                'edit_choose_qnaire_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了问卷题' . "\n",
                'del_choose_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 删除了所有问卷题' . "\n",
                'reviewed_entry_question'=>	'{$date}, 由 <strong>{$actor}</strong> 完成考试题评审。'. "\n",
                'auto_submit_exam'		=>	'{$date}, 由 <strong>系统</strong> 自动交卷，考试结束。'."\n",
                'auto_finish_examtask'	=>	'{$date}, 考试时间已到，考试任务完成。'."\n",
                'reviewed_entry_questionnair' => '{$date}, 由 <strong>{$actor}</strong> 完成问卷题评审。'. "\n",
                'start_reviewed_questionnair' => '{$date}, 由 <strong>{$actor}</strong> 开始评审问卷题。'. "\n",
                'start_reviewed_entry_question' => '{$date}, 由 <strong>{$actor}</strong> 开始评审考试题。'. "\n",
                'finish_questionnair' 	=> '{$date}, 由 <strong>{$actor}</strong> 完成问卷调查。'. "\n",
                'auto_submit_qnaire'	=>	'{$date}, 由 <strong>系统</strong> 自动关闭问卷。'."\n",
                'auto_finish_qnairetask'	=>	'{$date}, 结束时间已到，问卷调查任务完成。'."\n",
                'over_time_feedback'	=> '{$date}, 由 <strong>{$actor}</strong> 进行了超期反馈。'. "\n",
                'overdue_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 评审了任务执行超期处理的申请， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'overdueReviewResultList'),
                'overdue_auto_change_deadline' => '{$date}, <strong>系统</strong>自动修改任务的截止日期, 因为任务 <a target="_blank" href="'.urldecode(U('Commontasks/view', array('id' => '{$extra}'))).'"><strong>#{$extra}</strong></a> 通过了 <strong>执行超期处理流程</strong> 影响了本任务。'. "\n",
                'overdue_apply' => '{$date}, <strong>{$actor}</strong> 申请进行 <strong>执行超期处理</strong> 流程，将计划截止日期修改为 <strong>{$extra}</strong>。'. "\n",
                'auto_cancel_overdue' => '{$date}, <strong>系统</strong> 自动取消了 <strong>执行超期处理</strong> 流程，原因：<strong>{$extra}</strong>。'. "\n",
                'start_sales_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 开始进行采购预测。'. "\n",
                'edit_sales_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 修改了采购预测数据。'. "\n",
                'start_change_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 开始进行干预预测。'. "\n",
                'start_control_materiel_task' 	=> '{$date}, 由 <strong>{$actor}</strong> 开始管控物料配置任务。'. "\n",
                'edit_change_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 修改了干预预测任务。'. "\n",
                'edit_control_materiel_task' 	=> '{$date}, 由 <strong>{$actor}</strong> 修改了管控物料配置任务。'. "\n",
                'add_abnormal_info' 		=>	'{$date}, 由 <strong>{$actor}</strong> 添加了反馈信息。' . "\n",
                'edit_abnormal_info' 		=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了反馈信息。' . "\n",
                'set_feedback' 			=>	'{$date}, 由 <strong>{$actor}</strong> 置为了待异常反馈。' . "\n",
                'add_feedback' 			=>	'{$date}, 由 <strong>{$actor}</strong> 进行了异常反馈。' . "\n",
                'start_bs_reserve_intervene' => '{$date}, 由 <strong>{$actor}</strong> 开始进行产品供需干预。'. "\n",
                'intervene_reserve_task' => '{$date}, 由 <strong>{$actor}</strong> 进行了产品供需干预。'. "\n",
                'product_task_intervene' => '{$date}, 由 <strong>{$actor}</strong> 进行了干预。'. "\n",
                'product_intervene_influence' => '{$date}, 由 <strong>{$actor}</strong> 干预了上级，导致取消。'. "\n",

                'start_product_version' => '{$date}, 由 <strong>{$actor}</strong> 开始了产品版本维护。'. "\n",
                'edit_product_version' 	=> '{$date}, 由 <strong>{$actor}</strong> 继续了产品版本维护。'. "\n",

                'start_product_forecast' => '{$date}, 由 <strong>{$actor}</strong> 开始了生产预测。'. "\n",
                'edit_product_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 继续了生产预测。'. "\n",
                'intervene_product_forecast' => '{$date}, 由 <strong>{$actor}</strong> 干预了生产预测。'. "\n",

                'part_in_price_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 参与了评审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'taskReviewResultList'),
                'referee_price_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了主审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'taskReviewResultList'),
                'make_policy_price_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了决策审核， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'taskReviewResultList'),

                'offer_part_in_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 参与了评审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'offerTaskReviewResultList'),
                'offer_referee_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了主审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'offerTaskReviewResultList'),
                'offer_make_policy_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了决策审核， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'offerTaskReviewResultList'),
                'submited'		=>	'{$date}, 由 <strong>{$actor}</strong> 提交。' . "\n",
                'invite_testing_batch' => '{$date}, 由 <strong>{$actor}</strong> 进行批量请检。'. "\n",
                'invite_testing' => '{$date}, 由 <strong>{$actor}</strong> 进行请检。'. "\n",
                'send_testing' => '{$date}, 由 <strong>{$actor}</strong> 进行送检。'. "\n",
                'send_testing_batch' => '{$date}, 由 <strong>{$actor}</strong> 进行批量送检。'. "\n",
                'finish_send_testing' => '{$date}, 由 <strong>{$actor}</strong> 完成送检。'. "\n",
                'signed'		=>	'{$date}, 由 <strong>{$actor}</strong> 进行签收。' . "\n",
                'testing_part_in_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 参与了评审， 建议 <strong>{$extra}</strong>。'. "\n", 'extra' => 'testingTaskReviewResultList'),
                'testing_referee_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了主审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'testingTaskReviewResultList'),
                'testing_make_policy_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了决策审核， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'testingTaskReviewResultList'),
                'create_common_task' => '{$date}, 由 <strong>{$actor}</strong> 发起关联任务。'. "\n",
                'tested'				=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 验证，结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'reviewResultList'),
                'engineering'				=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 工程确认，结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'engineeringList'),
                'materiel_part_in_reviewed'	 =>	array('main' => '{$date},  <strong>{$actor}</strong> 参与了任务评审，评审意见为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'reviewResultList'),

                'part_in_decision_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 参与了协助评审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'decisionReviewResultList'),
                'referee_decision_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 进行了主审， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'decisionReviewResultList'),
            ),
            'reviewResultList' => array(
                'pass'			=>	'确认通过',
                'reject'		=>	'拒绝',
                'reviewed_again'=>	'交由其他人负责评审',
                'reviewed_more'	=>	'需要其他人协助评审',
                'clarify'		=>	'描述不清，有待补充',
                'suspended'		=>	'暂时搁置',
                'restart'       =>  '驳回',
                'yes'           =>  '允许指派',
                'no'            =>  '拒绝指派',
                'adopt'         =>  '允许干预',
                'refuse'        =>  '拒绝干预',
                'abstain'		=>	'弃权',
                'reviewed_up'	=>	'邀请供应委员会进行主审',
                'mybreak'			=>	'按我的建议分解提货',
            ),
            'overdueReviewResultList' => array(
                '1' => '同意',
                '-1' => '拒绝'
            ),
            'partinReviewResultList' => array(
                '1' => '同意',
                '-1' => '拒绝',
//                '0'  =>  '未选择'
            ),
            'taskReviewResultList' => array(
                'pass'					=>	'同意',
                'pass_myprice'			=>	'同意，按我的建议定价',
                'pass_ave'				=>	'同意，取建议的平均值',
                'waiver'				=>	'弃权',
                'invite_other_review'	=> 	'邀请其他人参与评审',
                'reject'				=> 	'驳回',
                'refuse'				=> 	'拒绝',
            ),
            'offerTaskReviewResultList' => array(
                'pass'					=>	'同意,按照申请价报价',
                'pass_myprice'			=>	'同意，按我的建议报价',
                'pass_ave'				=>	'同意，取建议的平均值',
                'waiver'				=>	'弃权',
                'invite_other_review'	=> 	'邀请其他人参与评审',
                'reject'				=> 	'驳回'
            ),
            'testingTaskReviewResultList' => array(
                'return_satisfy' => '退料后补货可以满足计划需求',
                'no_return_satisfy' => '退料后补货无法满足计划需求，提交MRB重新决策评审',
                'return' => '退货',
                'pick' => '挑选使用',
                'special' => '特采',
            ),
            'engineeringList' => array(
                '1'		=>	'增加代替料编码',
                '2'		=>	'增加新料编码',
                '3'		=>	'维持原编码(同品牌不同供应商或原物料改善品）',
            ),

            'decisionReviewResultList' => array(
                'pass' => '通过',
                'restart' => '未通过',
                'reviewed_more'	=> 	'邀请其他人参与评审',
                'invite_other_review'	=> 	'邀请其他人参与评审',
                'refuse' => '拒绝'
            ),
        ),
        'sales_tasks' => array(
            'action' => array(
                'finished'		=>	'{$date}, 由 <strong>{$actor}</strong> 完成。' . "\n",
                'reviewed'				=>	array('main' => '{$date}, 由 <strong>{$actor}</strong> 评审，结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'reviewResultList'),
                'pause'  				=>	'{$date}, 由 <strong>{$actor}</strong> 暂停了上级任务<strong>{$extra}</strong>。'. "\n",
                //'restarte'  			=>	'{$date}, 由 <strong>{$actor}</strong> 继续了上级任务<strong>{$extra}</strong>。'. "\n",
                'recordestimated'		=>	'{$date}, 由 <strong>{$actor}</strong> 记录了<strong>{$extra}</strong>的工时。' . "\n",
                'child_reject_change'	=> 	'{$date}, 子任务{$extra}拒绝了任务变更，任务变更需重新评审。'. "\n",
                'canceled_child_task'	=>	'{$date}, 任务取消， 原因：上级任务{$extra}取消。'. "\n",
                'part_in_reviewed'		=>	array('main'=>'{$date}, <strong>{$actor}</strong> 参与了任务评审，评审意见为 <strong>{$extra}</strong>。'. "\n",'extra' => 'partinReviewResultList'),
                'choose_questions'		=>	'{$date}, 由 <strong>{$actor}</strong> 选择了考试题' . "\n",
                'edit_choose_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了考试题' . "\n",
                'choose_qnaire_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 选择了问卷题' . "\n",
                'edit_choose_qnaire_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 编辑了问卷题' . "\n",
                'del_choose_questions'	=>	'{$date}, 由 <strong>{$actor}</strong> 删除了所有问卷题' . "\n",
                'reviewed_entry_question'=>	'{$date}, 由 <strong>{$actor}</strong> 完成考试题评审。'. "\n",
                'auto_submit_exam'		=>	'{$date}, 由 <strong>系统</strong> 自动交卷，考试结束。'."\n",
                'auto_finish_examtask'	=>	'{$date}, 考试时间已到，考试任务完成。'."\n",
                'reviewed_entry_questionnair' => '{$date}, 由 <strong>{$actor}</strong> 完成问卷题评审。'. "\n",
                'start_reviewed_questionnair' => '{$date}, 由 <strong>{$actor}</strong> 开始评审问卷题。'. "\n",
                'start_reviewed_entry_question' => '{$date}, 由 <strong>{$actor}</strong> 开始评审考试题。'. "\n",
                'finish_questionnair' 	=> '{$date}, 由 <strong>{$actor}</strong> 完成问卷调查。'. "\n",
                'auto_submit_qnaire'	=>	'{$date}, 由 <strong>系统</strong> 自动关闭问卷。'."\n",
                'auto_finish_qnairetask'	=>	'{$date}, 结束时间已到，问卷调查任务完成。'."\n",
                'over_time_feedback'	=> '{$date}, 由 <strong>{$actor}</strong> 进行了超期反馈。'. "\n",
                'overdue_reviewed' => array('main' => '{$date}, 由 <strong>{$actor}</strong> 评审了任务执行超期处理的申请， 结果为 <strong>{$extra}</strong>。'. "\n", 'extra' => 'overdueReviewResultList'),
                'overdue_auto_change_deadline' => '{$date}, <strong>系统</strong>自动修改任务的截止日期, 因为任务 <a target="_blank" href="'.urldecode(U('Commontasks/view', array('id' => '{$extra}'))).'"><strong>#{$extra}</strong></a> 通过了 <strong>执行超期处理流程</strong> 影响了本任务。'. "\n",
                'overdue_apply' => '{$date}, <strong>{$actor}</strong> 申请进行 <strong>执行超期处理</strong> 流程，将计划截止日期修改为 <strong>{$extra}</strong>。'. "\n",
                'auto_cancel_overdue' => '{$date}, <strong>系统</strong> 自动取消了 <strong>执行超期处理</strong> 流程，原因：<strong>{$extra}</strong>。'. "\n",

                'start_sales_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 开始进行出货量预测。'. "\n",
                'edit_sales_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 修改了出货量预测数据。'. "\n",
                'start_bs_reserve_intervene' => '{$date}, 由 <strong>{$actor}</strong> 开始进行产品供需干预。'. "\n",
                'intervene_reserve_task' => '{$date}, 由 <strong>{$actor}</strong> 进行了产品供需干预。'. "\n",
                'product_task_intervene' => '{$date}, 由 <strong>{$actor}</strong> 进行了干预。'. "\n",
                'product_intervene_influence' => '{$date}, 由 <strong>{$actor}</strong> 干预上级，导致取消。'. "\n",

                'start_product_version' => '{$date}, 由 <strong>{$actor}</strong> 开始了产品版本维护。'. "\n",
                'edit_product_version' 	=> '{$date}, 由 <strong>{$actor}</strong> 继续了产品版本维护。'. "\n",

                'start_product_forecast' => '{$date}, 由 <strong>{$actor}</strong> 开始了生产预测。'. "\n",
                'edit_product_forecast' 	=> '{$date}, 由 <strong>{$actor}</strong> 继续了生产预测。'. "\n",
                'intervene_product_forecast' => '{$date}, 由 <strong>{$actor}</strong> 干预了生产预测。'. "\n",
            ),
            'reviewResultList' => array(
                'pass'			=>	'确认通过',
                'reject'		=>	'拒绝',
                'reviewed_again'=>	'交由其他人负责评审',
                'reviewed_more'	=>	'需要其他人协助评审',
                'clarify'		=>	'描述不清，有待补充',
                'suspended'		=>	'暂时搁置',
                'restart'       =>  '不准确，驳回重新预测',
                'need_change'   =>  '预测数据需要变更',
                'no_change'     =>  '预测数据无需变更',
                'yes'           =>  '允许指派',
                'no'            =>  '拒绝指派'
            ),
            'partinReviewResultList' => array(
                'pass' => '同意',
                'reject' => '拒绝',
                'abstain'  =>  '弃权'
            ),
            'overdueReviewResultList' => array(
                '1' => '同意',
                '-1' => '拒绝'
            )
        )
    ),
    'USER_CHANGE_LOG' => array(
        'common' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 创建了 ',
            'edited'		=>	'{$date}, <strong>{$actor}</strong> 编辑了 ',
            'changed'		=>	'{$date}, <strong>{$actor}</strong> 变更了 ',
            'assigned'		=>	'{$date}, <strong>{$actor}</strong> 指派了 ',
            'closed'		=>	'{$date}, <strong>{$actor}</strong> 关闭了 ',
            'deleted'		=>	'{$date}, <strong>{$actor}</strong> 删除了 ',
            'undeleted'		=>	'{$date}, <strong>{$actor}</strong> 还原了 ',
            'commented'		=>	'{$date}, <strong>{$actor}</strong> 评论了 ',
            'activated'		=>	'{$date}, <strong>{$actor}</strong> 激活了 ',
            'reviewed'		=>	'{$date}, <strong>{$actor}</strong> 分析了 ',
            'confirmed'		=>	'{$date}, <strong>{$actor}</strong> 确认了变更 ',
            'bugconfirmed'	=> 	'{$date}, <strong>{$actor}</strong> 确认了 ',
            'started'		=>	'{$date}, <strong>{$actor}</strong> 开启了 ',
            'restarted'		=>	'{$date}, <strong>{$actor}</strong> 继续了 ',
            'paused'		=>	'{$date}, <strong>{$actor}</strong> 暂停了 ',
            'recordestimate'=>	'{$date}, <strong>{$actor}</strong> 记录了工时 ',
            'deleteestimate'=>	'{$date}, <strong>{$actor}</strong> 删除了工时 ',
            'editworktime'	=>	'{$date}, <strong>{$actor}</strong> 编辑了工时 ',
            'canceled'		=>	'{$date}, <strong>{$actor}</strong> 取消了 ',
            'login'			=>	'{$date}, <strong>{$actor}</strong> 登录系统 ',
            'logoutUser'	=>	'{$date}, <strong>{$actor}</strong> 退出系统 ',
            'finished'		=>	'{$date}, <strong>{$actor}</strong> 完成了 ',
            'sign_in'		=>	'{$date}, <strong>{$actor}</strong> 考勤签到 ',
            'check_out'		=>	'{$date}, <strong>{$actor}</strong> 考勤签出 ',
            'checked'		=>	'{$date}, <strong>{$actor}</strong> 审核了 ',
            'editworkrel'	=>	'{$date}, <strong>{$actor}</strong> 编辑了任务关系 ',
            'editquestioncommon'	=>	'{$date}, <strong>{$actor}</strong> 编辑了定时通用任务信息 ',
            'apply_change_operator'	=> 	'{$date}, <strong>{$actor}</strong> 申请变更执行人 ',
            'finish_send_testing'	=> 	'{$date}, <strong>{$actor}</strong> 送检完成，更新了 ',
            'submited'		=> 	'{$date}, <strong>{$actor}</strong> 提交了 ',
            'referee_decision_reviewed' => '{$date}, <strong>{$actor}</strong> 负责评审了',
            'part_in_decision_reviewed' => '{$date}, <strong>{$actor}</strong> 参与评审了',

        ),
        'originalDemand' => array(
            'reviewed'		=>	'{$date}, <strong>{$actor}</strong> 审核了 '
        ),
        'attendance' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 添加了一条考勤记录 ',
            'deleted'		=>	'{$date}, <strong>{$actor}</strong> 删除了一条考勤记录 ',
            'edited'		=>	'{$date}, <strong>{$actor}</strong> 编辑了一条考勤记录 ',
        ),
        'ask_leave' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 提出了 '
        ),
        'resumption_from_leave' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 申请了 '
        ),
        'change_rest' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 申请了 ',
            'canceled'		=>	'{$date}, <strong>{$actor}</strong> 申请取消了 ',
        ),
        'later_sign_in' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 申请了 '
        ),
        'journal' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 提交了 '
        ),
        'bus_application' => array(
            'created'		=>	'{$date}, <strong>{$actor}</strong> 提交了 '
        ),
        'work' => array(
            'canceled_child_task'	=>  '{$date}, <strong>系统</strong> 联动取消了 ',
            'child_reject_change'	=>  '{$date}, 变更需重新评审 ',
            'part_in_reviewed'		=>	'{$date}, <strong>{$actor}</strong> 参与评审了 ',
            'reviewed'				=>	'{$date}, <strong>{$actor}</strong> 负责评审了 ',
            'reviewed_entry_question'=>	'{$date}, <strong>{$actor}</strong> 完成考试题评审 ',
            'reviewed_entry_questionnair' => '{$date}, <strong>{$actor}</strong> 完成问卷题评审 '. "\n",
            'start_reviewed_questionnair' => '{$date}, <strong>{$actor}</strong> 开始评审问卷题 '. "\n",
            'start_reviewed_entry_question' => '{$date}, <strong>{$actor}</strong> 开始评审考试题 '. "\n",

            'choose_questions'		=>	'{$date}, <strong>{$actor}</strong> 选择了考试题' . "\n",
            'edit_choose_questions'	=>	'{$date}, <strong>{$actor}</strong> 编辑了考试题' . "\n",
            'choose_qnaire_questions'	=>	'{$date}, <strong>{$actor}</strong> 选择了问卷题' . "\n",
            'edit_choose_qnaire_questions'	=>	'{$date}, <strong>{$actor}</strong> 编辑了问卷题' . "\n",
            'del_choose_questions'	=>	'{$date}, <strong>{$actor}</strong> 删除了所有问卷题' . "\n",
            'finish_questionnair' 	=> '{$date}, <strong>{$actor}</strong> 完成问卷调查。'. "\n",
            'over_time_feedback' 	=> '{$date}, <strong>{$actor}</strong> 反馈了超期任务。'. "\n",
            'overdue_reviewed'		=> '{$date}, <strong>{$actor}</strong> 评审执行超期申请'. "\n",
            'overdue_apply' 		=> '{$date}, <strong>{$actor}</strong> 申请执行超期处理'. "\n",
            'start_sales_forecast' 	=> '{$date}, <strong>{$actor}</strong> 开始了'. "\n",
            'edit_sales_forecast' 	=> '{$date}, <strong>{$actor}</strong> 修改了'. "\n",
            'add_feedback' 			=> '{$date}, <strong>{$actor}</strong> 反馈了异常'. "\n",
            'start_bs_reserve_intervene' => '{$date}, <strong>{$actor}</strong> 开始了'. "\n",
            'intervene_reserve_task' => '{$date}, <strong>{$actor}</strong> 干预了'. "\n",
            'product_task_intervene' => '{$date}, <strong>{$actor}</strong> 干预了'. "\n",
            'product_intervene_influence' => '{$date}, <strong>{$actor}</strong> 干预影响了'. "\n",

            'start_product_version' => '{$date}, <strong>{$actor}</strong> 开始了'. "\n",
            'edit_product_version' 	=> '{$date}, <strong>{$actor}</strong> 继续了'. "\n",

            'start_product_forecast' => '{$date}, <strong>{$actor}</strong> 开始了'. "\n",
            'edit_product_forecast' 	=> '{$date}, <strong>{$actor}</strong> 继续了'. "\n",
            'intervene_product_forecast' => '{$date}, <strong>{$actor}</strong> 干预了'. "\n",

            'start_control_materiel_task' => '{$date}, <strong>{$actor}</strong> 开始了'. "\n",
            'edit_control_materiel_task' => '{$date}, <strong>{$actor}</strong> 继续了'. "\n",

            'part_in_price_reviewed' => '{$date}, <strong>{$actor}</strong> 参与了评审',
            'referee_price_reviewed' => '{$date}, <strong>{$actor}</strong> 进行了主审',
            'make_policy_price_reviewed' => '{$date}, <strong>{$actor}</strong> 进行了决策审核',

            'finish_send_testing' 			=> '{$date}, <strong>{$actor}</strong> 完成送检',
            'submited'						=> '{$date}, <strong>{$actor}</strong> 提交了',
            'testing_part_in_reviewed' 		=> '{$date}, <strong>{$actor}</strong> 参与评审了',
            'testing_referee_reviewed' 		=> '{$date}, <strong>{$actor}</strong> 进行了主审',
            'testing_make_policy_reviewed' 	=> '{$date}, <strong>{$actor}</strong> 进行了决策审核',
        ),
        'sales_tasks' => array(
            'start_sales_forecast' 	=> '{$date}, <strong>{$actor}</strong> 开始了'. "\n",
            'edit_sales_forecast' 	=> '{$date}, <strong>{$actor}</strong> 修改了'. "\n",
            'part_in_reviewed'		=>	'{$date}, <strong>{$actor}</strong> 参与评审了 ',
            'reviewed'				=>	'{$date}, <strong>{$actor}</strong> 负责评审了 ',
        )
    ),
    'USER_ACTION_NAME' => array(
        'created'		=>	'创建了',
        'edited'		=>	'编辑了',
        'changed'		=>	'变更了',
        'assigned'		=>	'指派了',
        'closed'		=>	'关闭了',
        'deleted'		=>	'删除了',
        'undeleted'		=>	'还原了',
        'commented'		=>	'评论了',
        'activated'		=>	'激活了',
        'reviewed'		=>	'负责评审了',
        'confirmed'		=>	'确认了变更',
        'bugconfirmed'	=> 	'确认了',
        'started'		=>	'开启了',
        'restarted'		=>	'继续了',
        'paused'		=>	'暂停了',
        'recordestimate'=>	'记录了工时',
        'deleteestimate'=>	'删除了工时',
        'canceled'		=>	'取消了',
        'login'			=>	'登录系统',
        'logoutUser'	=>	'退出系统',
        'finished'		=>	'完成了',
        'sign_in'		=>	'签到',
        'check_out'		=>	'签出',
        'checked'		=>	'审核了',
        'editworkrel'	=>	'编辑了任务关系',
        'editquestioncommon'	=>	'编辑了定时通用任务信息',
        'canceled_child_task' => '联动取消',
        'part_in_reviewed' => '参与评审了',
        'reviewed_entry_question' => '完成了考试题的评审',
        'reviewed_entry_questionnair' => '完成了问卷题的评审',
        'start_reviewed_questionnair' => '开始评审问卷题',
        'start_reviewed_entry_question' => '开始评审考试题',
        'choose_questions'		=>	'选择了考试题',
        'edit_choose_questions'	=>	'编辑了考试题',
        'choose_qnaire_questions'	=>	'选择了问卷题',
        'edit_choose_qnaire_questions'	=>	'编辑了问卷题',
        'del_choose_questions'	=>	'删除了所有问卷题',
        'finish_questionnair' 	=> '完成问卷调查',
        'over_time_feedback'	=> '反馈了超期任务',
        'overdue_reviewed'		=> '评审执行超期申请',
        'overdue_apply' 		=> '申请执行超期处理',
        'start_sales_forecast' 	=> '开始了',
        'edit_sales_forecast' 	=> '修改了',
        'add_feedback' 			=> '反馈了',
        'start_bs_reserve_intervene' => '开始了',
        'intervene_reserve_task' => '干预了',
        'product_task_intervene' => '干预了',
        'product_intervene_influence' => '干预影响了',

        'start_product_version' => '开始了',
        'edit_product_version' 	=> '继续了',

        'start_product_forecast' => '开始了',
        'edit_product_forecast' => '继续了',
        'intervene_product_forecast' => '干预了',

        'part_in_price_reviewed' => '参与了评审',
        'referee_price_reviewed' => '进行了主审',
        'make_policy_price_reviewed' => '进行了决策审核',
        'apply_change_operator' => '申请变更执行人',

        'start_control_materiel_task' => '开始了',
        'edit_control_materiel_task' => '继续了',
        'referee_decision_reviewed' => '负责评审了',
        'part_in_decision_reviewed' => '参与评审了',
    ),
    'ACTION_FIELD' => array(
        'demand' => array(
            'id'                    => '需求编号',
            'name'                  => '需求名称',
            'source'                => array(
                'name' => '需求来源',
                'value'=> array(
                    '1'	=>	'客户订单',
                    '2'	=>	'用户',
                    '3'	=>	'市场',
                    '4'	=>	'销售',
                    '5'	=>	'客服',
                    '6'	=>	'产品经理',
                    '7'	=>	'开发人员',
                    '8'	=>	'测试人员',
                    '9'	=>	'bug',
                    '10'=>	'其它'
                )
            ),
            'product_id'            => array('name' => '对应产品', 'func' => 'getProductById'),
            'customer'              => '客户名称',
            'expect_deadline'       => '期望完成日期',
            'priority'              => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'create_by'             => array('name' => '提出人', 'func' => 'getUserById'),
            'original_demand_id'    => array('name' => '相关原始需求', 'func' => 'getOriDemandById'),
            'link_demand_id'        => '相关需求',
            'contact_info'          => '联系方式',
            'mail_to' 				=> array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'deadline'         		=> '计划截止日期',
            'status'                => array(
                'name' => '状态',
                'value' => array(
                    '0' => '待评审',
                    '1' => '激活',
                    '2' => '已变更',
                    '3' => '已完成',
                    '-1' => '删除',
                    '-2' => '拒绝',
                    '-3' => '关闭',
                    '-4' => '暂时搁置'
                )
            ),
            'stage'                => array(
                'name' => '阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '已立项',
                    '2' => '研发中',
                    '3' => '集成中',
                    '4' => '研发完毕',
                    '5' => '测试中',
                    '6' => '测试完毕',
                    '7' => '验收中',
                    '8' => '结束',
                    '9' => '重新激活'
                )
            ),
            'create_time'           => '提出时间',
            'create_by_dept'        => array('name' => '提出部门', 'func' => 'getDeptById'),
            'order_sn'              => '需求单号',
            'assign_time'           => '指派时间',
            'assign'                => array('name' => '指派给', 'func' => 'getUserById'),
            'last_edit_by'          => array('name' => '最后编辑人', 'func' => 'getUserById'),
            'last_edit_time'        => '最后编辑时间',
            'reviewed_by'           => array('name' => '评审人', 'func' => 'getUserInIds'),
            'reviewed_time'         => '评审时间',
            'closed_by'             => array('name' => '关闭人', 'func' => 'getUserById'),
            'closed_time'           => '关闭时间',
            'finished_by'           => array('name' => '完成人', 'func' => 'getUserById'),
            'finished_time'         => '完成时间',
            'resolution'            => array(
                'name' => '关闭原因',
                'value' => array(
                    '1'	=>	'已完成',
                    '2'	=>	'已细分',
                    '3'	=>	'需求重复',
                    // '4'	=>	'延期',
                    '5'	=>	'不做',
                    '6'	=>	'设计如此',
                    '7'	=>	'其它'
                )
            ),
            'version'               => '版本',
            'task_num'              => '分解任务数量',
            'activated_num'         => '激活次数',
            'spec'                  => '需求描述',
            'verify'                => '验收标准',
            'version_info'          => '版本信息',
            'file_name'				=> '附件标题',
            'is_fee'				=> array(
                'name' => '是否收费',
                'value' => array(
                    '0' => '否',
                    '1' => '是'
                )
            ),
            'custom_day'			=> '定制周期',
            'conclusion'			=> '初步评审结论',
            'type'=> array(
                'name' => '需求类型',
                'value' => array(
                    '1' => '通用需求',
                    '2' => '定制需求',
                    '3' => '方案类需求'
                )
            ),
            'add_template' => array(
                'name' => '根据产品添加需求模板',
                'value' => array(
                    '39' => '【APP名称（APP在桌面显示的名字,如在不同语言环境下有变化，请注明）,必选】:<br/>【APP模板：xmeye/超级看看 必选】:<br/>【IOS：bundle id/Android：包名,必选】:<br/>【是否需要上传（需要研发协助上传到app store（Google Play））(是/否)】:<br/>【APP store（Google Play）账号、密码(如果 需要上传 则这点 必填)】:<br/>【发布证书（上传以及打包正式版ipa用，需要提供cer、p12（不设置密码）、mobileprovision 三个文件，iOS必选，文件上传）】:<br/>【发布用推送证书（报警推送功能用，需要提供cer、p12（不设置密码）两个文件，iOS必选，文件上传）】:<br/>【雄迈开放平台 账号、密码】:<br/>【语言包(文件上传)】:<br/>【图片包<br/>(提供给客户基础图片包，客户按照需要修改。<br/>要求：<br/>1、尺寸大小及图片名称必须和原图保持一致   <br/>2、提供图片不带透明通道<br/>3、不允许添加、删除图片<br/>4、欢迎界面，桌面图标，logo必需更换，其他选择性更换文件上传)】:<br/>【其他（有其他需求请注明）】:<br/>'
                )
            ),
        ),
        'bug' => array(
            'id'                    => 'bug编号',
            'name'                  => 'bug名称',
            'product_id'            => array('name' => '对应产品', 'func' => 'getProductById'),
            'project_id'            => array('name' => '对应项目', 'func' => 'getProjectById'),
            'source'                => array(
                'name' => 'bug来源',
                'value'=> array(
                    '1'	=>	'客户订单',
                    '2'	=>	'用户',
                    '3'	=>	'市场',
                    '4'	=>	'销售',
                    '5'	=>	'客服',
                    '6'	=>	'产品经理',
                    '7'	=>	'开发人员',
                    '8'	=>	'测试人员',
                    '9'	=>	'bug',
                    '10'=>	'其它'
                )
            ),
            'customer'              => '客户名称',
            'order_sn'              => '问题单号',
            'type'                  => array(
                'name' => 'bug类型',
                'value'=> array(
                    '1'	=>	'代码错误',
                    '2'	=>	'界面优化',
                    '3'	=>	'设计缺陷',
                    '4'	=>	'配置相关',
                    '5'	=>	'安装部署',
                    '6'	=>	'测试脚本',
                    '7'	=>	'安全相关',
                    '8'	=>	'性能问题',
                    '9'	=>	'标准规范',
                    '10'=>	'其它'
                )
            ),
            'priority'              => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'severity'              => array(
                'name' => '严重性',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '严重',
                    '4' => '非常严重'
                )
            ),
            'demand_id'             => array('name' => '相关需求', 'func' => 'getDemandById'),
            'demand_version'        => '相关需求版本号',
            'task_id'               => array('name' => '相关任务', 'func' => 'getTaskById'),
            'steps'                 => '重现步骤',
            'contact_info'          => '联系方式',
            'version_info'          => '版本信息',
            'create_by'             => array('name' => '提出人', 'func' => 'getUserById'),
            'create_by_dept'        => array('name' => '提出部门', 'func' => 'getDeptById'),
            'create_time'           => '提出时间',
            'assign'                => array('name' => '当前指派给', 'func' => 'getUserById'),
            'assign_time'           => '指派时间',
            'reviewed_by'           => array('name' => '分析人', 'func' => 'getUserInIds'),
            'reviewed_time'         => '分析时间',
            'finished_by'           => array('name' => '解决人', 'func' => 'getUserById'),
            'finished_time'         => '解决时间',
            'resolution'            => array(
                'name'  => '关闭原因',
                'value' => array(
                    '1'	=>	'已解决',
                    '2'	=>	'设计如此',
                    '3'	=>	'重复bug',
                    '4'	=>	'外部原因',
                    '5'	=>	'无法重现',
                    '6'	=>	'延期处理',
                    '7'	=>	'不予解决',
                    '8'	=>	'非问题',
                    '9' =>	'转需求'
                )
            ),
            'closed_by'             => array('name' => '关闭人', 'func' => 'getUserById'),
            'closed_time'           => '关闭时间',
            'last_edit_by'          => array('name' => '最后编辑人', 'func' => 'getUserById'),
            'last_edit_time'        => '最后编辑时间',
            'status'                => array(
                'name' => '状态',
                'value' => array(
                    '0' => '待分析',
                    '1' => '已确认',
                    '2' => '已解决',
                    '-1' => '删除',
                    '-2' => '拒绝',
                    '-3' => '关闭',
                    '-4' => '暂时搁置'
                )
            ),
            'stage'                => array(
                'name' => '阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '已立项',
                    '2' => '修复中',
                    '3' => '集成中',
                    '4' => '修复完毕',
                    '5' => '测试中',
                    '6' => '测试完毕',
                    '7' => '验收中',
                    '8' => '结束',
                    '9' => '重新激活'
                )
            ),
            'task_num'              => '分解任务数量',
            'activated_num'         => '激活次数',
            'mail_to' 				=> array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'expect_deadline'       => '期望完成日期',
            'file_name'				=> '附件标题',
            'deadline'       	    => '计划截止日期',
            'technical_field'       =>array(
                'name'  => '技术领域',
                'value' => array(
                    '1'  =>  '驱动',
                    '2'  =>  '接口库',
                    '3'  =>  '应用',
                    '4'  =>  'IE',
                    '5'  =>  'CMS&VMS',
                    '6'  =>  'MYEYE平台',
                    '7'  =>  '移动客户端',
                    '8'  =>  '云平台',
                    '9'  =>  'Web+开发',
                    '10'  =>  '网络',
                    '11'  =>  '测试',
                    '12'  =>  '硬件',
                    '13'  =>  'PHP开发',
                    '14'  =>  '其他'
                )
            ),
            'recurrent_probability'  		=> array(
                'name' => '复现概率',
                'value' => array(
                    '1'  =>  '有条件必然重现',
                    '2'  =>  '有条件概率重现',
                    '3'  =>  '无条件概率重现',
                    '4'  =>  '很难重现'
                )
            ),
            'soft_name' => '软件名称',
            'soft_compile_date' => '软件编译时间'
        ),
        'file' => array(
            'id'                    => '附件编号',
            'pathname'              => '附件地址',
            'title'                 => '附件标题',
            'extension'             => '附件后缀',
            'size'                  => '附件大小',
            'object_type'           => array(
                'name' => '所属对象类型',
                'value' => array(
                    '1' => '需求',
                    '2' => 'bug',
                    '3' => '研发任务',
                    '4' => '原始需求',
                    '5' => '请假',
                    '6' => '公告',
                    '7' => '通用任务',
                    '8' => '站内信'
                )
            ),
            'object_id'             => '所属对象编号',
            'author'                => array('name' => '添加者', 'func' => 'getUserById'),
            'create_time'           => '创建时间',
            'download_num'          => '下载次数',
            'status'                => array(
                'name' => '状态',
                'value' => array(
                    '1'  => '正常',
                    '0'  => '待审核',
                    '-1' => '删除'
                )
            )
        ),
        'task' => array(
            'id'                    => '任务编号',
            'name'                  => '任务名称',
            'related_type'          => array('name' => '相关类型对象', 'value' => array(''=> '请选择', '1' => '需求', '2' => 'bug')),
            'related_id'            => '相关类型对象编号',
            'demand_version'        => '对应需求版本号',
            'original_demand_id'    => '相关原始需求',
            'type'                  => array('name' => '任务类型', 'value' => array(
                '1' => '开发',
                //'2' => '修复bug',
                '3' => '集成',
                '4' => '测试',
                '5' => '程序发布'
            )),
            'source'                => array(
                'name' => '任务来源',
                'value'=> array(
                    '0' =>	'',
                    '1'	=>	'客户订单',
                    '2'	=>	'用户',
                    '3'	=>	'市场',
                    '4'	=>	'销售',
                    '5'	=>	'客服',
                    '6'	=>	'产品经理',
                    '7'	=>	'开发人员',
                    '8'	=>	'测试人员',
                    '9'	=>	'bug',
                    '10'=>	'其它'
                )
            ),
            'priority'              => array('name' => '优先级', 'value' => array(
                '1' => '低',
                '2' => '普通',
                '3' => '高',
                '4' => '紧急'
            )),
            'estimate'              => '最初预计工时',
            'consumed'              => '已消耗工时',
            'surplus'               => '剩余工时',
            'remark'                => '任务描述',
            'deadline'              => '截止日期',
            'create_by'             => array('name' => '创建人', 'func' => 'getUserById'),
            'create_time'           => '创建时间',
            'assign'                => array('name' => '指派给', 'func' => 'getUserById'),
            'assign_time'           => '指派时间',
            'plan_start'            => '计划开始时间',
            'real_statr'            => '实际开始时间',
            'finished_by'           => array('name' => '完成人', 'func' => 'getUserById'),
            'finished_time'         => '完成时间',
            'cancel_by'             => array('name' => '取消者', 'func' => 'getUserById'),
            'cancel_time'           => '取消时间',
            'closed_by'             => array('name' => '关闭者', 'func' => 'getUserById'),
            'closed_time'           => '关闭时间',
            'last_edit_by'          => array('name' => '最后编辑人', 'func' => 'getUserById'),
            'last_edit_time'        => '最后编辑时间',
            'status'                => array(
                'name' => '任务状态',
                'value' => array(
                    '2' => '已完成',
                    '1' => '进行中',
                    '0' => '未开始',
                    '-1' => '已删除',
                    '-2' => '已暂停',
                    '-3' => '已关闭',
                    '-4' => '已取消'
                )
            ),
            'file_name'				=> 	'附件标题',
            'mail_to' 				=> array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'log_date'				=>	'工时日期',
            'work'					=>	'工作内容',
            'real_start'			=>	'实际开始时间',
            'activated_num'			=>	'激活次数',
            'is_pass'				=>	array('name' => '测试结果', 'value' => array('0' => '', '1' => '通过', '-1' => '未通过')),
            'examination_type'      => array(
                'name' => '测试类型',
                'value' => array(
                    '0' => '无',
                    '1' => '基线',
                    '2' => '烧片',
                    '3' => '接口',
                    '4' => '功能',
                    '5' => '单板',
                    '6' => '整机',
                    '7' => '图像',
                    '8' => '老化测试'
                )
            ),
            'plan_name' => '方案组合',
            'hardware_name' => '硬件版本',
        ),

        'originalDemand'	=>	array(
            'id'			=>	'原始需求编号',
            'name'			=>	'原始需求名称',
            'customer'		=>	'客户名称',
            'create_by'		=>	array('name' => '负责销售', 'func' => 'getUserById'),
            'create_time'	=>	'提出时间',
            'company'		=>	array(
                'name' => '所属公司',
                'value' => array(
                    '1' => '杭州巨峰科技有限公司',
                    '2' => '杭州雄迈信息技术有限公司',
                    '3' => '杭州云阔科技有限公司',
                    '5' => '杭州雄迈集成电路技术有限公司',
                    '4' => '其它'
                )
            ),
            'endtime'		=>	'期望截止',
            'status'		=>	array(
                'name'	=> '状态',
                'value'	=>	array(
                    '0'		=>	'待审核',
                    '1'		=>	'已审阅',
                    // '-1'	=>	'拒绝'
                    '-2'	=>	'拒绝'
                )
            ),
            'assign'		=>	array('name' => '指派给', 'func' => 'getUserByid'),
            'assign_time'	=>	'指派时间',
            'check_by'		=>	'由谁审核',
            'check_time'	=>	'审核时间',
            'closed_by'		=>	'由谁关闭',
            'closed_time'	=>	'关闭时间',
            'priority'		=>	array(
                'name'	=>	'优先级',
                'value'	=>	array(
                    '1'	=>	'低',
                    '2'	=>	'普通',
                    '3'	=>	'高',
                    '4'	=>	'紧急'
                )
            ),
            'file_name'				=> '附件标题'
        ),
        'project'=>array(
            'stage'		=>	array(
                'name'	=> '阶段',
                'value'	=>	array(
                    '1'		=>	'概念计划',
                    '2'		=>	'立项',
                    '3'		=>	'开发与测试',
                    '4'		=>	'验证与发布',
                    '5'		=>	'结项'
                )
            ),
            'status'		=>	array(
                'name'	=> '状态',
                'value'	=>	array(
                    '0'		=>	'待审核',
                    '1'		=>	'正常',
                    '2'		=>	'结束',
                    '-1'	=>	'删除',
                    '-2'	=>	'暂停',
                    '-3'	=>	'异常终止',
                    '-4'	=>	'延时'
                )
            ),
            'global_stage'		=>	array(
                'name'	=> '总阶段',
                'value'	=>	array(
                    '1'		=>	'概念计划',
                    '2'		=>	'立项',
                    '3'		=>	'开发与测试',
                    '4'		=>	'验证与发布',
                    '5'		=>	'结项'
                )
            ),
            'priority'		=>	array(
                'name'	=> '优先级',
                'value'	=>	array(
                    '1'		=>	'低',
                    '2'		=>	'普通',
                    '3'		=>	'高',
                    '4'		=>	'紧急'
                )
            ),
            'is_import'		=>	array(
                'name'	=> '是否为研发重要事项',
                'value'	=>	array(
                    '1'	=>	'是',
                    '0'	=> 	'否'
                )
            ),

        ),
        'user' => array(
            'user_id' => '用户id',
            'status' => array(
                'name'	=> '状态',
                'value'	=>	array(
                    '1'		=>	'正式',
                    '2'		=>	'试用',
                    '3'		=>	'实习',
                    '-2'	=>	'离职'
                )
            ),
            'work_attendance' => array(
                'name'	=> '考勤类型',
                'value'	=>	array(
                    '1'	=>	'单双休',
                    '2'	=>	'双单休',
                    '3'	=>	'单休'
                )
            ),
            'check_attendance'	=> array(
                'name'	=> '是否参与考勤',
                'value'	=>	array(
                    '1'	=>	'是',
                    '0'	=> 	'否'
                )
            )
        ),
        'scheduling' => array(
            'id'		=>	'编号',
            'dates'		=>	'日期',
            'isOnWork'	=>	'是否上班',
            'type'		=>	array(
                'name' => '类型',
                'value' => array(
                    '1' => array('work' => 1, 'show' => 0, 'name' => '上班（特殊）'),
                    '2' => array('work' => 0, 'show' => 0, 'name' => '休息（特殊）'),
                    '3' => array('work' => 0, 'show' => 0, 'name' => '元旦'),
                    '4' => array('work' => 0, 'show' => 0, 'name' => '春节'),
                    '5' => array('work' => 0, 'show' => 0, 'name' => '清明节'),
                    '6' => array('work' => 0, 'show' => 0, 'name' => '劳动节'),
                    '7' => array('work' => 0, 'show' => 0, 'name' => '端午节'),
                    '8' => array('work' => 0, 'show' => 0, 'name' => '胜利日'),
                    '9' => array('work' => 0, 'show' => 0, 'name' => '中秋节'),
                    '10' => array('work' => 0, 'show' => 0, 'name' => '国庆节'),
                    '11' => array('work' => 0, 'show' => 1, 'name' => 'G20峰会')
                )
            )

        ),
        'Changerest'=>array(
            'status' => array(
                'name'	=> '状态',
                'value'	=>	array(
                    '0'	=>	'待审核',
                    '1'	=>	'同意 ',
                    '-1'	=>	'拒绝'
                )
            )
        ),
        'CancelChangeRest'=> array(
            'status' => array(
                'name'	=> '状态',
                'value'	=>	array(
                    '0'	=>	'待审核',
                    '1'	=>	'同意 ',
                    '-1'=>	'拒绝'
                )
            )
        ),
        'Journal'=>array(
            'type' => array(
                'name'	=> '类型',
                'value'	=>	array(
                    '1'	=>	'日报',
                    '2'	=>	'周报'
                )
            ),
            'status' => array(
                'name'	=> '状态',
                'value'	=>	array(
                    '0'	=>	'未审阅',
                    '1'	=>	'已审阅',
                    '2' =>	'已反馈'
                )
            )

        ),
        'attendance' => array(
            'id'				=>	'编号',
            'sign_in_type'		=>	array(
                'name'	=>	'签到类型',
                'value'	=>	array(
                    '1'	=>	'日常签到',
                    '2'	=>	'周末签到',
                    '3'	=>	'假日签到',
                    '4'	=>	'请假签到'
                )
            ),
            'sign_in_result'	=>	array(
                'name'	=>	'签到结果',
                'value'	=>	array(
                    '1'	=>	'正常',
                    '2'	=>	'加班',
                    '3'	=>	'请假',
                    '4'	=>	'迟到'
                )
            ),
            'check_out_type'	=>	array(
                'name'	=>	'签出类型',
                'value'	=>	array(
                    '1'	=>	'日常签出',
                    '2'	=>	'周末签出',
                    '3'	=>	'假日签出',
                    '4'	=>	'请假签出'
                )
            ),
            'check_out_result'	=>	array(
                'name'	=>	'签出结果',
                'value'	=>	array(
                    '1'	=>	'正常',
                    '2'	=>	'加班',
                    '3'	=>	'请假',
                    '4'	=>	'早退'
                )
            )
        ),

        'askleave'	=> array(
            'type'		=> array(
                'name'	=> '类型',
                'value'	=> array(
                    '1'	=>	'事假',
                    '2'	=>	'病假',
                    '3'	=>	'年休假',
                    '4'	=>	'带薪假',
                    '5'	=>	'其它'
                )
            ),

            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '0'		=> '待审批',
                    '1'		=> '同意',
                    '-1'	=> '拒绝'
                )
            )
        ),

        'resumptionLeave'	=>	array(
            'status' => array(
                'name'	=> '状态',
                'value'	=> array(
                    '0'		=> '待审批',
                    '1'		=> '同意',
                    '-1'	=> '拒绝'
                )
            )
        ),

        'latersignin'	=> array(
            'type'		=> array(
                'name'	=> '补签类型',
                'value'	=> array(
                    '1'	=>	'签到',
                    '2'	=>	'签出'
                )
            ),

            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '0'		=> '待审批',
                    '1'		=> '同意',
                    '-1'	=> '拒绝'
                )
            )
        ),
        'dept'	=>	array(
            'property'	=>	array(
                'name' 	=>	'部门性质',
                'value'	=>	array(
                    '1'	=>	'研发',
                    '2'	=>	'市场',
                    '3'	=>	'销售',
                    '4'	=>	'测试',
                    '5'	=>	'其他'
                )
            )

        ),
        'station'=>	array(
            'status'	=>	array(
                'name' 	=>	'是否启用',
                'value'	=>	array(
                    '1'	=>	'是',
                    '0'	=>	'否'
                )
            )
        ),
        'route'=>	array(
            'type'	=>	array(
                'name' 	=>	'类别',
                'value'	=>	array(
                    '1'	=>	'日常班车',
                    '2'	=>	'加班车（19:30）',
                    '3'	=>	'加班车（20:30）',
                    '4'	=>	'东洲班车'
                )
            ),
            'status'	=>	array(
                'name' 	=>	'是否启用',
                'value'	=>	array(
                    '1'	=>	'是',
                    '0'	=>	'否'
                )
            )
        ),
        'bus_route' => array(
            'type' => array(
                'name' => '班车类型',
                'value'	=>	array(
                    '1'	=>	'日常班车',
                    '2'	=>	'加班车（19:30）',
                    '3'	=>	'加班车（20:30）',
                    '4'	=>	'东洲班车'
                )
            )
        ),
        'bus_application' => array(
            'type' => array(
                'name' => '申请类型',
                'value' => array(
                    '1' => '季度申请',
                    '2' => '加班车（19:30）',
                    '3' => '加班车（20:30）',
                    '4' => '临时班车'
                )
            ),
            'status' => array(
                'name' => '审批结果',
                'value' => array(
                    '1' => '同意',
                    '0' => '待审核',
                    '-1' => '拒绝'
                )
            )
        ),
        'work' => array(
            'id' => '编号',
            'name' => '名称',
            'pid' => '上级任务编号',
            'version' => '任务版本号',
            'priority'		=>	array(
                'name'	=> '优先级',
                'value'	=>	array(
                    '1'		=>	'低',
                    '2'		=>	'普通',
                    '3'		=>	'高',
                    '4'		=>	'紧急'
                )
            ),
            'estimate' => '最初预计工时',
            'consumed' => '已消耗工时',
            'surplus' => '剩余工时',
            'expect_deadline' => '期望截止日期',
            'deadline' => '计划截止日期',
            'work_type' => array('name' => '任务模式', 'value' => array('0' => '', '1' => '单人模式', '2' => '多人模式')),
            'create_by' => array('name' => '提出人', 'func' => 'getUserById'),
            'create_time' => '提出时间',
            'create_by_dept' => array('name' => '提出部门', 'func' => 'getDeptById'),
            'assign' => array('name' => '指派给', 'func' => 'getUserById'),
            'assign_time' => '指派时间',
            'plan_start' => '计划开始时间',
            'real_start' => '实际开始时间',
            'finish_by' => array('name' => '完成人', 'func' => 'getUserById'),
            'finish_time' => '完成时间',
            'cancel_by' => array('name' => '取消者', 'func' => 'getUserById'),
            'cancel_time' => '取消时间',
            'closed_by' => array('name' => '关闭者', 'func' => 'getUserById'),
            'closed_time' => '关闭时间',
            'satisfaction_star_num' => array(
                'name' => '完成满意度',
                'value' => array(
                    '0' => '',
                    '1' => '1分，很不满意',
                    '2' => '2分，不满意',
                    '3' => '3分，一般',
                    '4' => '4分，满意',
                    '5' => '5分，很满意'
                )
            ),
            'status' => array(
                'name' => '任务状态',
                'value' => array(
                    '0' => '待评审',
                    '1' => '激活',
                    '2' => '已变更',
                    '3' => '已完成',
                    '12' => '执行超期评审',
                    '-1' => '删除',
                    '-2' => '拒绝',
                    '-3' => '关闭',
                    '-4' => '暂时搁置',
                    '-5' => '取消',
                    '-6' => '超期取消',
                )
            ),
            'stage' => array(
                'name' => '任务阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待执行/待细分',
                    '2' => '执行中/已细分',
                    '3' => '验收中',
                    '4' => '结束',
                    // '5' => '重新激活'
                )
            ),
            'stage_price' => array(//产品定价阶段
                'name' => '任务阶段',
                'value'	=> array(
                    '0'		=> '未开始',
                    '2'		=> '执行中',
                    '3' 	=> '待审核',
                    '4' 	=> '结束'
                )
            ),
            'stage_common' => array(//规范任务阶段
                'name' => '任务阶段',
                'value'	=> array(
                    '0'		=> '未开始',
                    '2'		=> '执行中',
                    '3' 	=> '待审核',
                    '4' 	=> '结束'
                )
            ),
            'status_purchasing' => array(//采购跟踪
                'name' => '状态',
                'value' => array(
                    '0' 	=> '待继续',
                    '1' 	=> '激活',
                    '3' 	=> '完成',
                    '-2' 	=> '暂停',
                    '-3'	=> '关闭',
                    '-5'	=> '取消',
                    '-6'	=> '超期取消',
                )
            ),
            'stage_purchasing' => array(
                'name' => '阶段',
                'value' => array(
                    '0' 	=> '未开始',
                    '1' 	=> '待细分/待执行',
                    '2' 	=> '已细分/执行中',
                    '3' 	=> '待评审',
                    '4'		=> '结束'
                )
            ),
            'activated_num' => '激活次数',
            'child_num' => '子任务数量',
            'level' => '任务层级',
            'related_task' => '相关任务编号',
            'cc' => array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'spec' => '任务描述',
            'reason' => array(
                'name' => '变更原因',
                'value' => array(
                    '0' => '',
                    '1' => '本任务变更',
                    '2' => '上级任务变更'
                )
            ),
            'part_in_review' => array(
                'name' => '邀请协助评审的人员',
                'func' => 'getUserInIds'
            ),
            'allow_child_view' => array(
                'name'	=> '子任务权限',
                'value'	=>	array(
                    '0'		=>	'不允许子任务查看',
                    '1'		=>	'允许所有子任务查看'
                )
            ),
            'special_work_type' => array(
                'name' => '任务类型',
                'value' => array(
                    '0' => '通用',
                    '1' => '考试',
                    '2' => '问卷调查',
                    '3' => '录考题',
                    '4' => '录问卷题',
                    '5' => '出货量预测',
                    '6' => '产品委员会干预',
                    '7' => '产品经理干预',
                    '8' => '物控技术干预',
                    '9' => '采购预测',
                    '10' => '生产任务反馈',
                    '11' => '供需预约跟踪',
                    '12' => '产品供需干预',
                    '13' => '产品版本维护',
                    '14' => '生产预测',
                    '15' => '产品报价',
                    '16' => '产品定价',
                    '17' => '产品规格维护',
                    '18' => '产品新增',
                    '19' => '属性组维护',
                    '20' => '产品类别维护',
                    '21' => '模组模块关系',
                    '22' => '采购跟踪',
                    '23' => '批量试生产',
                    '24' => '物料替代关系',
                    '25' => '物料生产交期维护',
                    '26' => '管控物料配置',
                    '27' => '定期通用任务',
                    '28' => '物料新增任务',
                    '29' => '物料导入任务',
                    '30' => '商务策略导入任务',
                    '31' => '权限申请',
                    '32' => '任务委托',
                )
            ),
            'special_work_type_module' => array(
                'name' => '可以委托的任务Controller',
                'value' => array(
                    'Commontasks' => '0'  ,//> '通用',
                    'Examtask' => '1'  ,//> '考试',
                    'Questionnairetask' => '2'  ,//> '问卷调查',
                    'Entryexamquestion' => '3'  ,//> '录考题',
                    'Entryquestionnair' => '4'  ,//> '录问卷题',
                    'Salestasks' => '5'  ,//> '出货量预测',
                    'Productmanager' => '6'  ,//> '产品委员会干预',
                    'Productmanaging' => '7'  ,//> '产品经理干预',
                    'Materielplanning' => '8'  ,//> '物控技术干预',
                    'Procurementtasks' => '9'  ,//> '采购预测',
                    'Productivetask' => '10' ,//=> '生产任务反馈',
                    'Salesreservation' => '11' ,//=> '供需预约跟踪',
                    'Businessreservation' => '11' ,//=> '供需预约跟踪',
                    'Businessintervene' => '12' ,//=> '产品供需干预',
                    'Productforecast' => '14' ,//=> '生产预测',
                    'Salesproductionoffer' => '15' ,//=> '产品报价',
                    'Salesproductionprice' => '16' ,//=> '产品定价',
                    'Salesproductionattr' => '17' ,//=> '产品规格维护',
                    'Salesproductionadd' => '18' ,//=> '产品新增',
                    'Productattributesgroup' => '19' ,//=> '属性组维护',
                    'Salesproductcategory' => '20' ,//=> '产品类别维护',
                    'Materielrelation' => '21' ,//=> '模组模块关系',
                    'Procurementtrack' => '22' ,//=> '采购跟踪',
                    'Batchproduct' => '23' ,//=> '批量试生产',
                    'Materielrelationaa' => '24' ,//=> '物料替代关系',
                    'Materielproductdeliverydate' => '25' ,//=> '物料生产交期维护',
                    'Controlmateriel' => '26' ,//=> '管控物料配置',
                    'Regularcommontasks' => '27' ,//=> '定期通用任务',
                    'Materiel' => '28' ,//=> '物料新增任务',
                    'Materielimport' => '29' ,//=> '物料导入任务',
                    'Decisionimport' => '30' ,//=> '商务策略导入任务',
                    'Accessapplytasks' => '31' ,//=> '权限申请',
                    'Entrusttasks' => '32' ,//=> '任务委托',
                )
            ),
            'feedback_object' => array(
                'name' => '超期反馈对象',
                'func' => 'getUserInIds'
            ),
            'feedback_level' => '超期反馈阶段',
            'purpose' => '考试目的',
            'start_time' => '考试开始时间',
            'end_time' => '考试结束时间',
            'choice_type' => array(
                'name' => '选题方式',
                'value' => array(
                    '1' => '系统随机选题',
                    '2' => '手动选题'
                )
            ),
            'break_type' => array(
                'name' => '发卷方式',
                'value' => array(
                    '1' => '每人试题随机',
                    '2' => '每人试题相同'
                )
            ),
            'join_user' => array('name' => '参考人员', 'func' => 'getUserInIds'),
            'exam_info' => '试卷基本信息',
            'allow_view_report' =>array(
                'name' => '查看统计结果',
                'value' => array(
                    '0' => '不允许子任务查看',
                    '1' => '问卷结束后允许子任务查看'
                )
            ),
            'allow_anonymous' => array(
                'name' => '是否允许匿名',
                'value' => array(
                    '0' => '否',
                    '1' => '是'
                )
            ),
            'questionnaire_info' => '问卷基本信息',
            'overdueReviewResult' => array(
                'name' => '执行超期评审结果',
                'value' => array(
                    '0' => '等待审核中',
                    '1' => '同意',
                    '-1' => '拒绝'
                )
            ),
            'next_overdue_review_by' => array('name' => '评审人', 'func' => 'getUserById'),
            'overdue_review_time' => '评审时间',
            'change_num' => '干预数量',
            'title' => '任务名称',
            'product_id' => '产品id',
            'final_num' => '最终值',
            'sale_order_sn' => '销售订单号',
            'prs_order_sn' => 'PRS单号',
            'product_name' => '生产产品',
            'produce_palce' => array(
                'name'	=> '产地',
                'value'	=> array(
                    '0'	=> '',
                    '1'	=> '杭州',
                    '2'	=> '东莞',
                    '3'	=> '深圳'
                )
            ),
            'workshop' => '生产车间',
            'division' => array('name' => '所属事业部', 'func' => 'getDivisionById'),
            'production_line' => array('name' => '所属产品线', 'func' => 'getProlineById'),
            'regular_type_way'=>'重复方式',
            'regular_next_time'=>'任务下次发起时间',
            'regular_days'=>'任务截止时限',
        ),
        'work_content' => array(
            'name' => '任务名称',
            'version' => '任务版本号',
            'spec' => '任务描述',
            'reason' => array(
                'name' => '变更原因',
                'value' => array(
                    '1' => '本任务变更',
                    '2' => '上级任务变更'
                )
            )
        ),
        'work_estimate' => array(
            'log_date' => '记录日期',
            'consumed' => '已消耗工时',
            'surplus' => '剩余工时',
            'create_by' => '记录人',
            'create_time' => '记录时间',
            'content' => '工时日志内容',
        ),
        'work_review_log' => array(
            'wid' => '评审的任务编号',
            'review_by' => array('name' => '评审人', 'func' => 'getUserById'),
            'review_time' => '评审时间',
            'result' => array(
                'name' => '评审结果',
                'value' => array(
                    'pass'			=>	'确认通过',
                    'reviewed_again'=>	'交由其他人负责评审',
                    'reviewed_more'=>	'需要其他人协助评审',
                    'reject'		=>	'拒绝',
                    'clarify'		=>	'描述不清，有待补充',
                    'suspended'		=>	'暂时搁置'
                )
            ),
            'content' => '评审内容'
        ),
        'work_exam' => array(
            'wid' => '任务主表编号',
            'name' => '考试名称',
            'exam_type' => array(
                'name' => '考试类型',
                'value' => array(
                    '1' => '正式',
                    '2' => '临时',
                    '3' => '模拟'
                )
            ),
            'start_time' => '考试开始时间',
            'end_time' => '考试结束时间',
            'choice_type' => array(
                'name' => '选题方式',
                'value' => array(
                    '1' => '系统随机选题',
                    '2' => '手动选题'
                )
            ),
            'break_type' => array(
                'name' => '发卷方式',
                'value' => array(
                    '1' => '每人试题随机',
                    '2' => '每人试题相同'
                )
            ),
            'join_user' => array('name' => '参考人员', 'func' => 'getUserInIds'),
            'exam_info' => '试卷基本信息',
            'purpose' => '考试目的',
            'spec' => '考试内容',
            'version' => '版本号',
            'exam_model' => array(
                'name' => '题型',
                'value' => array(
                    '1' => '单选题',
                    '2' => '判断题',
                    '3' => '多选题'
                )
            ),
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '0' => '待评审',
                    '1' => '激活',
                    '2' => '已变更',
                    '3' => '已完成',
                    '-1' => '删除',
                    '-2' => '拒绝',
                    '-3' => '关闭',
                    // '-4' => '暂时搁置',
                    '-5' => '取消'
                )
            ),
            'stage' => array(
                'name' => '阶段',
                'value' => array(
                    // '1' => array(		//考试申请任务
                    '0' => '未开始',
                    '1' => '等待出卷',
                    '2' => '待分发试卷',
                    '3' => '已发卷/考试中',
                    '4' => '考试完成',
                    '5' => '结束'
                    // ),
                    // '2' => array(		//考试任务
                    // '0' => '未开始',
                    // '3' => '考试中',
                    // '4' => '已交卷',
                    // '5' => '考试结束'
                    // )
                )
            ),
            'stageFmt' => array(
                'name' => '阶段',
                'value' => array(
                    '1' => array(		//考试申请任务
                        '0' => '未开始',
                        '1' => '等待出卷',
                        '2' => '待分发试卷',
                        '3' => '已发卷',
                        '4' => '考试完成',
                        '5' => '结束'
                    ),
                    '2' => array(		//考试任务
                        '0' => '未开始',
                        '3' => '考试中',
                        '4' => '已交卷',
                        '5' => '考试结束'
                    )
                )
            ),
            'assign' => array('name' => '指派给', 'func' => 'getUserById'),
            'work_type' => array('name' => '任务模式', 'value' => array('0' => '', '1' => '单人模式', '2' => '多人模式')),
            'related_task' => '相关任务编号',
            'cc' => array('name' => '抄送给', 'func' => 'getUserInIds'),
            'cancel_by' => array('name' => '取消者', 'func' => 'getUserById'),
            'cancel_time' => '取消时间',
            'part_in_review' => array(
                'name' => '邀请协助评审的人员',
                'func' => 'getUserInIds'
            ),
        ),
        'write_exam_question' => array(
            'name' => '名称',
            'version' => '任务版本号',
            'create_by' => array('name' => '提出人', 'func' => 'getUserById'),
            'create_time' => '提出时间',
            'create_by_dept' => array('name' => '提出部门', 'func' => 'getDeptById'),
            'assign' => array('name' => '指派给', 'func' => 'getUserById'),
            'assign_time' => '指派时间',
            'closed_by' => array('name' => '关闭者', 'func' => 'getUserById'),
            'closed_time' => '关闭时间',
            'related_task' => '相关任务编号',
            'cc' => array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'spec' => '任务描述',

            'question_library'	=>	array(
                'name'		=>	'题库类型',
                'value'		=>	array(
                    '1' => '正式',
                    '2' => '临时'
                )
            ),
            // 'question_status'   =>  array(
            // 'name'		=>	'审核状态',
            // 'value'		=>	array(
            // '0' => '待审核',
            // '1' => '审核中',
            // '3' => '审核完成',
            // '-3' => '关闭',
            // )
            // ),
            'status' => array(
                'name'		=>	'审核状态',
                'value'		=>	array(
                    '0' => '待审核',
                    '1' => '审核中',
                    '3' => '审核完成',
                    '-3' => '关闭',
                )
            ),
            'question_model'	=>	array(
                'name'		=>	'题型',
                'value'		=>	array(
                    '1' => '单选题',
                    '2' => '判断题',
                    '3' => '多选题'
                )
            ),
            'questions'			=>	'题目内容',
            'select_options'	=>	'选择项',
            'correct_answers'	=>	'参考答案',
            'answer_analysis'	=>	'答案解析',
            'level'				=>	array(
                'name' => '难易度',
                'value' => array(
                    '1' => '易',
                    '2' => '中',
                    '3' => '难'
                )
            ),
            'create_time'		=>	'录入时间',
            'create_by'			=>	array('name' => '录入人', 'func' => 'getUserById'),
        ),
        'write_exam_question_types' => array(
            'wqid'		=>	'录入试题ID',
            'type'		=>	'试题分类',
            'status'	=>	array(
                'name' => '状态',
                'value' => array(
                    '0' 	=> '待审核',
                    '-1' 	=> '拒绝',
                    '1'		=> '通过'
                )
            ),
            'review_by' => array('name' => '评审人', 'func' => 'getUserById'),
            'review_time' => '评审时间',
            'refuse_reason' => '拒绝理由'
        ),
        'exam_official_question_library' => array(
            'question_model' => array(
                'name' => '题型',
                'value' => array(
                    '1' => '单选题',
                    '2' => '判断题',
                    '3' => '多选题',
                )
            ),
            'questions' => '题目内容',
            'select_options' => '选择项',
            'correct_answers' => '参考答案',
            'answer_analysis' => '答案解析',
            'level' => array(
                'name' => '难易度',
                'value' => array(
                    '1' => '易',
                    '2' => '中',
                    '3' => '难'
                )
            ),
            'write_qid' => '编写试题表id'
        ),

        'work_questionnaire' => array(
            'wid' => '任务主表编号',
            'name' => '问卷任务名称',
            'choice_type' => array(
                'name' => '选题方式',
                'value' => array(
                    '1' => '系统随机选题',
                    '2' => '手动选题'
                )
            ),
            'join_user' => array('name' => '参与调查人员', 'func' => 'getUserInIds'),
            'questionnaire_info' => '问卷基本信息',
            'purpose' => '调查目的',
            'spec' => '调查内容',
            'end_time' => '问卷结束时间',
            'allow_anonymous' => array(
                'name' => '是否允许匿名',
                'value' => array(
                    '0' => '否',
                    '1' => '是'
                )
            ),
            'version' => '版本号',
            'allow_ignore' => array(
                'name' => '是否允许忽略',
                'value' => array(
                    '0' => '否',
                    '1' => '是'
                )
            ),

            'common_title' => '通用任务名称',
            'common_person' => array('name' => '通用任务指派人', 'func' => 'getUserById'),
            'common_deadline' => '通用任务期望截至日期',
            'common_spec' => '通用任务描述',
            'is_common' => array('name' => '定时通用任务', 'value' => array('0' => '问卷结束后不发起', '1' => '问卷结束后自动发起')),

            'start_date_2' 		=> '批量试生产干预开始时间',
            'end_date_2' 		=> '批量试生产干预结束时间',
            'create_by_2' 		=> array('name' => '批量试生产提出人', 'func' => 'getUserById'),
            'create_by_dept_2' 	=> array('name' => '批量试生产提出部门', 'func' => 'getDeptById'),
            'assign_2' 			=> array('name' => '批量试生产指派人', 'func' => 'getUserById'),
            'spec_2' 			=> '批量试生产描述',
            'cc_2' 				=> array('name'=>' 批量试生产抄送给', 'func' => 'getUserInIds'),

            'autoTask' => array(
                'name' => '问卷结束定时任务',
                'value' => array(
                    '0' => '不发起',
                    '1' => '发起通用任务',
                    '2' => '发起批量试生产任务'
                )
            ) ,

            'status' => array(
                'name' => '状态',
                'value' => array(
                    '0' => '待审核',
                    '1' => '激活',
                    '2' => '已变更',
                    '3' => '已完成',
                    '-1' => '删除',
                    '-2' => '拒绝',
                    '-3' => '关闭',
                    // '-4' => '暂时搁置',
                    '-5' => '取消'
                )
            ),
            'stage' => array(
                'name' => '阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '等待出卷',
                    '2' => '待分发问卷',
                    '3' => '已发卷/问卷中',
                    '4' => '问卷完成',
                    '5' => '结束'
                )
            ),
            'stageFmt' => array(
                'name' => '阶段',
                'value' => array(
                    '1' => array(		//问卷申请任务
                        '0' => '未开始',
                        '1' => '等待出卷',
                        '2' => '待分发问卷',
                        '3' => '已发卷',
                        '4' => '问卷完成',
                        '5' => '结束'
                    ),
                    '2' => array(		//问卷任务
                        '0' => '未开始',
                        '3' => '问卷中',
                        '4' => '问卷完成',
                        '5' => '结束'
                    )
                )
            ),
            'assign' => array('name' => '指派给', 'func' => 'getUserById'),
            'work_type' => array('name' => '任务模式', 'value' => array('0' => '', '1' => '单人模式', '2' => '多人模式')),
            'related_task' => '相关任务编号',
            'cc' => array('name' => '抄送给', 'func' => 'getUserInIds'),
            'cancel_by' => array('name' => '取消者', 'func' => 'getUserById'),
            'cancel_time' => '取消时间',
            'part_in_review' => array(
                'name' => '邀请协助评审的人员',
                'func' => 'getUserInIds'
            ),
            'create_time' => '提出时间',
            'allow_view_report' =>array(
                'name' => '查看统计结果',
                'value' => array(
                    '0' => '不允许子任务查看',
                    '1' => '问卷结束后允许子任务查看'
                )
            ),
        ),
        'questionnairel_question_library' => array(
            'question_model' => array(
                'name' => '题型',
                'value' => array(
                    '1' => '单选题',
                    '2' => '多选题',
                    '3' => '填空题',
                    '4' => '多行填空题',
                    '5' => '枚举题',
                    '6' => '复合单选题',
                    '7' => '复合多选题',
                    '8' => '组合填空题'
                )
            ),
            'questions' => '问题',
            'select_options' => '选择项'
        ),
        'write_questionnaire' => array(
            'name' => '名称',
            'version' => '任务版本号',
            'create_by' => array('name' => '提出人', 'func' => 'getUserById'),
            'create_time' => '提出时间',
            'create_by_dept' => array('name' => '提出部门', 'func' => 'getDeptById'),
            'assign' => array('name' => '指派给', 'func' => 'getUserById'),
            'assign_time' => '指派时间',
            'closed_by' => array('name' => '关闭者', 'func' => 'getUserById'),
            'closed_time' => '关闭时间',
            'related_task' => '相关任务编号',
            'cc' => array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'spec' => '任务描述',

            'status'   =>  array(
                'name'		=>	'审核状态',
                'value'		=>	array(
                    '0' => '待审核',
                    '1' => '审核中',
                    '3' => '审核完成',
                    '-3' => '关闭',
                )
            ),
            'question_model'	=>	array(
                'name'		=>	'题型',
                'value'		=>	array(
                    '1' => '单选题',
                    '2' => '多选题',
                    '3' => '填空题',
                    '4' => '多行填空题',
                    '5' => '枚举题',
                    '6' => '复合单选题',
                    '7' => '复合多选题',
                    '8' => '组合填空题'
                )
            ),
            'questions'			=>	'题目内容',
            'select_options'	=>	'选择项',
            'create_time'		=>	'录入时间',
            'create_by'			=>	array('name' => '录入人', 'func' => 'getUserById'),
        ),
        'write_questionnaire_question_types' => array(
            'wqid'		=>	'录入试题ID',
            'type'		=>	'试题分类',
            'status'	=>	array(
                'name' => '状态',
                'value' => array(
                    '0' 	=> '待审核',
                    '-1' 	=> '拒绝',
                    '1'		=> '通过'
                )
            ),
            'review_by' => array('name' => '评审人', 'func' => 'getUserById'),
            'review_time' => '评审时间',
            'refuse_reason' => '拒绝理由'
        ),
        'questionnaire_question_option' => array(
            'check_rule' => array(
                'name' => '检查规则',
                'value' => array(
                    '1' => 'email',
                    '2' => '字符串长度',
                    '3' => '数字',
                    '4' => '整数',
                    '5' => '手机号码',
                    '6' => '电话号码（包括手机和固定电话）',
                    '7' => '日期',
                    '8' => '网址'
                )
            )
        ),

        'journal_review' => array(
            'journal_type'=> array(
                'name'  => '日志类型',
                'value' => array(
                    '1' => '日报',
                    '2' => '周报'
                )
            ),
            'journal_content'       => '今日工作内容',
            'journal_plan_content'  => '明日计划内容',
            'journal_problem'       => '问题与建议'
        ),

        'materiel' => array(
            'name'		=> '物料名称',
            'code'		=> '物料代码',
            'status'	=> array(
                'name'	=> '状态',
                'value' => array(
                    '0' => '',
                    '1' => '样品',
                    '2' => '小批量',
                    '3' => '小批量不合格',
                    '4' => '首件',
                    '5' => '首件不合格',
                    '6' => '批量',
                    '7' => '清库',
                    '8' => '禁用'
                )
            ),
            'pro_line'	=> '生产线',
            'is_crux'	=> array(
                'name' => '是否关键物料',
                'value' => array(
                    '0' => '否',
                    '1' => '是'
                )
            ),
            'promise_delivery'	=> '承诺交期',
            'life_cycle' => array(
                'name' => '生命周期',
                'value' => array(
                    '1' => '研发阶段',
                    '2' => '中试阶段',
                    '3' => '量产阶段',
                    '4' => '停产阶段',
                    '5' => '终止阶段',
                    '6' => '升级切换阶段',
                )
            ),
            'place' => array(
                'name' => '产地',
                'value' => array(
                    '1' => '杭州',
                    '2' => '东莞'
                )
            ),
            'sale_type' => array(
                'name' => '产品类型（是否出售）',
                'value' => array(
                    '1' => '原材料（外购物料）',
                    '2' => '半成品（生产中间件）',
                    '3' => '半成品（模块）',
                    '4' => '成品（可销售）',
                )
            ),
            'p_attr' => array(
                'name' => '产品属性',
                'value' => array(
                    '1' => '自制',
                    '2' => '外购',
                    '3' => '委外加工',
                    '4' => '配置类',
                    '5' => '其他',
                )
            ),
            'customized_type' => array(
                'name' => '定制类型',
                'value' => array(
                    '1' => '通用',
                    '2' => '定制',
                )
            ),
            'pro_type' => array(
                'name' => '产品类型判断',
                'value' => array(
                    '1' => '前端相关',
                    '2' => '后端相关',
                )
            ),
            'materiel_type' => array(
                'name' => '物料类型',
                'value' => array(
                    '1' => '电子类',
                    '2' => '包材类',
                    '3' => '结构类',
                    '4' => '配件类',
                    '5' => '线材、紧固件',
                    '6' => '生产辅料类',
                    '7' => 'Flash、内存、加密',
                )
            ),
            'import_attr' => array(
                'name' => '物料导入性质',
                'value' => array(
                    '1' => '新料',
//					'2' => '替代料',
                    '3' => '中性材料上仅丝印',
                )
            ),
            'import_place' => array(
                'name' => '拟稿部门',
                'value' => array(
                    '1' => '研发部',
                    '2' => '杭州中试部',
                    '3' => '东莞产业化',
                    '4' => '东莞工程部',
                )
            ),
            'erp_account' => array(
                'name' => '所属账套',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '雄迈集成电路',
                    '4' => '香港雄迈'
                )
            ),
            'company' => array(
                'name' => '所属公司',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成电路',
                    '4' => '云阔'
                )
            ),
            'ratio' => array(
                'name' => '系数',
                'value' => array(
                    'purchase_ratio' => '采购系数',
                    'turnover_ratio' => '仓储系数',
                    'produce_ratio' => '生产系数',
                    'develop_ratio' => '研发系数',
                    'strategy_ratio' => '战略系数',
                    'secret_ratio' => '保密系数',
                    'sales_radio' => '销售周转系数',
                    'quality_ratio' => '品质系数',
                    'flow_level' => '采购流通等级',
                )
            ),
        ),

        'materiel_work' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value' => array(
                    '1' => '激活',
                    '2' => '变更',
                    '3' => '完成',
                    '-3'=> '关闭',
                    '-2'=> '拒绝'
                )
            ),
            'stage'	=> array(
                'name'	=> '阶段',
                'value' => array(
                    '1' => '待执行',
                    '2' => '执行中',
                    '3' => '审核中',
                    '4' => '结束',
                )
            ),
            'review_result' => array(
                'name' => '评审',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '未通过',
                    'reviewed_more'	=>	'邀请其他人协助评审'
                )
            ),
            'materiel_cycle' => array(
                'name' => '产品周期状态',
                'value' => array(
                    '1' => '是（批量）',
                    '2' => '否（小批量）',
                    '3' => '新料不合格',
//					'4' => '替代料不合格'
                )
            ),
            'engineering_confirm' => array(
                'name' => '工程确认结果',
                'value' => array(
                    '1' => '增加替代料编码',
                    '2' => '增加新料编码',
                    '3' => '维持原编码（同品牌不同供应商或原物料改善品）',
                )
            )
        ),

        'sales_tasks' => array( //预测任务
            'name'		=> '任务名称',
            'status'	=> array(
                'name'	=> '状态',
                'value' => array(
                    '0' => '待评审',
                    '1' => '激活',
                    '2' => '申请变更',
                    '3' => '完成',
                    '-3'=> '关闭',
                    '-5' =>'执行超期关闭',
                    '-6' =>'审核超期关闭'/*方便条件查询，列表页显示没有用到-6状态*/

                )
            ),
            'stage'	=> array(
                'name'	=> '阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待执行',
                    '2' => '执行中',
                    '3' => '验收审核',
                    '4' => '结束',
                    '5' => '待关闭'
                )
            ),
            'finished_review' => array(
                'name' => '已完成预测',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '不准确，驳回重新预测'
                )
            ),
            'to_review' => array(
                'name' => '待评审预测',
                'value' => array(
                    'no_change' => '预测数据无需变更',
                    'need_change' => '预测数据需要变更'
                )
            ),
            'assign_review_result' => array(
                'name' => '审核申请变更指派人',
                'value' => array(
                    'yes' => '同意',
                    'no' => '拒绝'
                )
            ),
            'task_type' => array(
                // 'name' => '任务类型',
                // 'value' => array(
                '1' => '正常预测',
                '2' => '临时预测',
                // '3' => '补预测'
                // )
            ),
            'id'                    => '任务编号',
            'related_type'          => array('name' => '相关类型对象', 'value' => array(''=> '请选择', '1' => '需求', '2' => 'bug')),
            'related_id'            => '相关类型对象编号',
            'demand_version'        => '对应需求版本号',
            'original_demand_id'    => '相关原始需求',
            'type'                  => array('name' => '任务类型', 'value' => array(
                '1' => '开发',
                //'2' => '修复bug',
                '3' => '集成',
                '4' => '测试'
            )),
            'source'                => array(
                'name' => '任务来源',
                'value'=> array(
                    '0' =>	'',
                    '1'	=>	'客户订单',
                    '2'	=>	'用户',
                    '3'	=>	'市场',
                    '4'	=>	'销售',
                    '5'	=>	'客服',
                    '6'	=>	'产品经理',
                    '7'	=>	'开发人员',
                    '8'	=>	'测试人员',
                    '9'	=>	'bug',
                    '10'=>	'其它'
                )
            ),
            'priority'              => array('name' => '优先级', 'value' => array(
                '1' => '低',
                '2' => '普通',
                '3' => '高',
                '4' => '紧急'
            )),
            'estimate'              => '最初预计工时',
            'consumed'              => '已消耗工时',
            'surplus'               => '剩余工时',
            'remark'                => '任务描述',
            'deadline'              => '截止日期',
            'expect_deadline'       => '期望完成时间',
            'create_by'             => array('name' => '创建人', 'func' => 'getUserById'),
            'create_time'           => '创建时间',
            'assign'                => array('name' => '指派给', 'func' => 'getUserById'),
            'assign_time'           => '指派时间',
            'plan_start'            => '计划开始时间',
            'real_statr'            => '实际开始时间',
            'finish_by'           	=> array('name' => '完成人', 'func' => 'getUserById'),
            'finish_time'        	 => '完成时间',
            'cancel_by'             => array('name' => '取消者', 'func' => 'getUserById'),
            'cancel_time'           => '取消时间',
            'closed_by'             => array('name' => '关闭者', 'func' => 'getUserById'),
            'closed_time'           => '关闭时间',
            'last_edit_by'          => array('name' => '最后编辑人', 'func' => 'getUserById'),
            'last_edit_time'        => '最后编辑时间',
            'file_name'				=> 	'附件标题',
            'cc' 					=> array('name'=>' 抄送给', 'func' => 'getUserInIds'),
            'log_date'				=>	'工时日期',
            'work'					=>	'工作内容',
            'real_start'			=>	'实际开始时间',
            'activated_num'			=>	'激活次数',
            'is_pass'				=>	array('name' => '测试结果', 'value' => array('0' => '', '1' => '通过', '-1' => '未通过')),
            'date_node' => array(
                'judge_deadline'  => '08',
                'uncommon_date'   => '11',
            ),
            'reviewContent' => array(
                'name' => '评审内容',
                'value' => array(
                    '1' => '正常预测',
                    '2' => '激进预测',
                    '3' => '保守预测',
                    '4' => '预测出货量偏差过大，请在备注里面详细说明缘由',
                    '5' => '预测出货量的产品为定制产品，出货数量需要再次确认',
                    '6' => '预测出货量的产品为定制产品，产品BOM会存在变更，可以先使用临时预测来实现关键物料的提前备料',
                    '7' => '预测出货量的产品为定制产品，暂时需求还未确定，可以先使用临时预测来实现关键物料的提前备料',
                    '0' => '其他',
                )
            ),
            'partinReviewResult' => array(
                'name' => '参与评审内容',
                'value' => array(
                    'pass' => '同意',
                    'reject' => '拒绝',
                    'abstain'  =>  '弃权'
                )
            ),
            'passReviewContent' => array(
                'name' => '通过评审内容',
                'value' => array(
                    '1' => '正常预测',
                    '2' => '激进预测',
                    '3' => '保守预测',
                )
            ),
            'rejectReviewContent' => array(
                'name' => '拒绝评审内容',
                'value' => array(
                    '4' => '预测出货量偏差过大，请在备注里面详细说明缘由',
                    '5' => '预测出货量的产品为定制产品，出货数量需要再次确认',
                    '6' => '预测出货量的产品为定制产品，产品BOM会存在变更，可以先使用临时预测来实现关键物料的提前备料',
                    '7' => '预测出货量的产品为定制产品，暂时需求还未确定，可以先使用临时预测来实现关键物料的提前备料',
                    '0' => '其他',
                )
            ),
            'forecast_accuracy_type' => array(
                'name' => '预测准确率保证',
                'value' => array(
                    '1' => '绝对保证',
                    '2' => '基本保证'
                )
            ),
            'bom_change_possibility_type' => array(
                'name' => 'BOM变更概率',
                'value' => array(
                    '1' => '绝对不变',
                    '2' => '基本不变'
                )
            ),
            'forecast_item_reasons' => array(
                'name' => '预测产品依据',
                'value' => array(
                    '1' => '正常范围预测',
                    '2' => '异常范围预测，原因是已由本司其他部门跟进',
                    '3' => '异常范围预测，原因是客户上次预测过多',
                    '4' => '异常范围预测，原因是客户预测周期还没到，客户预测是按不定次数不是按月',
                    '5' => '异常范围预测，原因是客户当前型号向我司采购份额减少，向友商采购份额增加',
                    '6' => '异常范围预测，原因是客户被友商抢走，当前型号暂时不合作',
                    '7' => '异常范围预测，原因是客户暂时处于前期开发阶段，还未进入实际预测阶段',
                    '8' => '异常范围预测，其它原因',
                )
            ),
            'forecast_sum_reasons' => array(
                'name' => '合计预测产品依据',
                'value' => array(
                    '1' => '正常范围预测',
                    '2' => '异常范围预测，原因是客户已由本司其他部门跟进',
                    '3' => '异常范围预测，原因是上次预测过多',
                    '4' => '异常范围预测，原因是客户预测是按不定次数不是按月',
                    '5' => '异常范围预测，原因是客户向我司采购型号减少，向友商采购增加',
                    '6' => '异常范围预测，原因是客户被友商抢走，暂时不合作',
                    '7' => '异常范围预测，原因是客户暂时处于前期开发阶段，还未进入实际预测阶段',
                    '8' => '异常范围预测，其它原因'
                )
            ),
            'forecast_reasons' => array(
                'name' => '客户预测依据',
                'value' => array(
                    '1' => '正常合作范围预测，预计出货量在未来3个月基本平稳',
                    '2' => '异常合作范围预测，预计出货量在未来3个月大幅度增长',
                    '3' => '异常合作范围预测，预计出货量在未来3个月大幅度降低',
                    '4' => '异常合作范围预测，预计出货量在未来3个月完全取决于我司支撑',
                    '5' => '异常合作范围预测，预计出货量在未来3个月不可预测，客户丢失出货量骤降风险巨大',
                    '6' => '异常合作范围预测，预计出货量在未来3个月不可预测，客户机会和决心巨大，出货量暴增风险巨大',
                    '7' => '异常合作范围预测，其它原因',
                )
            ),
            'erp_account' => array(
                'name' => '所属公司',
                'value'=> array(
                    '1'  => '雄迈',
                    '2'  => '巨峰',
                    '3'  => '集成',
                    '4'  => '香港雄迈'
                ),
            )
        ),
        'business' => array(
            'property' => array(
                'name' => '组织类型',
                'value' => array(
                    '0' => '其它',
                    '1' => '产品委员会',
                    '2' => '质量管理委员会',
                    '3' => '技术管理委员会',
                    '4' => '产品经理',
                    '5' => '报价评审委员会',
                    '6' => '定价评审委员会',
                    '7' => '供应委员会',
                    '8' => '经营决策委员会',
                )
            )
        ),
        'product_committee' => array(
            'reviewResult' => array(
                'name' => '评审结果',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '驳回'
                )
            ),
            'status'	=> array(
                'name'	=> '状态',
                'value' => array(
                    '1' => '激活',
                    '3' => '完成',
                    '-3'=> '关闭',
                    '-5'=> '取消',
                    '-2'=> '拒绝',

                )
            ),
            'stage'	=> array(
                'name'	=> '阶段',
                'value' => array(
                    '1' => '待执行',
                    '2' => '执行中',
                    '3' => '验收审核',
                    '4' => '结束'
                )
            ),


        ),
        //采购预测
        'procurement_tasks' =>array( //预测任务
            'name'		=> '任务名称',
            'status'	=> array(
                'name'	=> '状态',
                'value' => array(
                    '1' => '激活',
                    '2' => '变更',
                    '3' => '完成',
                    '-3'=> '关闭',
                    '-5' =>'执行超期关闭',
                    '-6' =>'审核超期关闭'/*方便条件查询，列表页显示没有用到-6状态*/
                )
            ),
            'stage'	=> array(
                'name'	=> '阶段',
                'value' => array(
                    '1' => '待执行',
                    '2' => '执行中',
                    '3' => '验收审核',
                    '4' => '结束',
                    '5' => '待关闭'
                )
            ),
            'reviewResult' => array(
                'name' => '评审结果',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '不准确，驳回重新预测'
                )
            ),
            'reviewContent' => array(
                'name' => '评审内容',
                'value' => array(
                    '1' => '正常预测',
                    '2' => '激进预测',
                    '3' => '保守预测',
                    '0' => '其他',
                )
            ),
            'assignReview' => array(
                'name' => '指派审核',
                'value' => array(
                    'yes' => '允许指派',
                    'no' => '拒绝指派'
                )
            ),
            'forecast_item_reasons' => array(
                'name' => '物料预测依据',
                'value' => array(
                    '1' => '正常预测',
                    '2' => '实际库存过多，需要减少预测',
                    '3' => '实际库存过少，需要增加预测',
                    '4' => '物料供应充足并且降价风险大增，需要减少预测',
                    '5' => '系统预测数据未达到最小包装起订量，需要增加预测',
                    '10' => '系统预测数据未达到最小包装起订量，暂不需要预测',
                    '6' => '物料合格率低，质量不可控，需要增加预测',
                    '7' => '系统预测数据不准确，无法提供参考',
                    '8' => '当前物料存在多个供应商，总预测量不变',
                    '9' => '其它'
                )
            ),
            'forecast_demand_type' => array(
                'name' => '采购需求量类型',
                'value' => array(
                    '1' => '正常预测',
                    '2' => '临时预测',
                    '3' => '委员会干预',
                    '4' => '产品经理干预',
                    '5' => '物控计划干预',
                )
            ),
            'task_type' => array(
                'name' => '采购预测任务类型',
                'value' => array(
                    '1' => '正常预测'
                ),
            )
        ),
        'productive_task' => array(//生产反馈任务
            'name'		=> '任务名称',
            'status'	=> array(
                'name'	=> '状态',
                'value' => array(
                    '0' => '待评审',
                    '1' => '激活',
                    '2' => '变更',
                    '3' => '完成',
                    '-3'=> '关闭',
                    '-5'=> '取消'
                )
            ),
            'stage'	=> array(
                'name'	=> '阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待执行/待细分',
                    '2' => '反馈中',
                    '5' => '已细分',/*多人任务时，2 为已细分，为方便条件查询，该阶段放到5*/
                    '3' => '已完成',
                    '4' => '结束'
                ),
            ),
            'erp_account' => array(
                'name' => '所属公司',
                'value'=> array(
                    '1'  => '雄迈',
                    '2'  => '巨峰',
                    '3'  => '集成',
                    '4'  => '香港雄迈'
                ),
            ),
//            'work_category' => array(
//                'name' => '生产任务类型',
//                'value'=> array(
//                    '1' => '模组生产任务',
//                    '2' => '整机生产任务',
//                    '3' => '程序烧录任务',
//                    '4' => 'SMT贴片任务',
//                    '5' => '注塑生产任务'
//                ),
//            ),
            'process_type' => array(
                'name' => '工序类型',
                'value' => array(
                    '1' => '普通生产加工',
                    '2' => '检验',
                    '3' => '入库',
                )
            ),
            'category' => array(
                'name' => '异常类型',
                'value' => array(
                    '1' => '任务异常',
                    '2' => '产品故障'
                )
            ),
            'workshop_position' => array(
                'name' => '车间地点',
                'value' => array(
                    '1' => '杭州',
                    '2' => '东莞'
                )
            ),
            'source' => array(
                'name' => '生产任务类型',
                'value' => array(
                    '1' => '生产任务单',
                    '2' => '外协生产任务',
                    '3' => '开模任务'
                )
            )
        ),
        //物料套包
        'materiel_package'	=> array(
            'name'			=> '套包名称',
            'materiel_num'	=> '套包物料数量',
            'status' => array(
                'name' 	=> '状态',
                'value' => array(
                    '-1'	=> '删除',
                    '0' 	=> '禁用',
                    '1' 	=> '正常'
                )
            )
        ),
        //物料套包分类
        'materiel_package_cate'	=> array(
            'name'			=> '套包分类',
            'status' => array(
                'name' 	=> '状态',
                'value' => array(
                    '-1'	=> '删除',
                    '0' 	=> '禁用',
                    '1' 	=> '正常'
                )
            )
        ),

        //产品供需干预
        'shipping_reservation_intervene_work' => array(
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '0' => '待分析',
                    '1' => '激活',
                    '-3' => '关闭'
                )
            ),
            'stage' => array(
                'name' => '阶段',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待干预',
                    '2' => '干预中',
                    '4' => '结束'
                )
            )
        ),

        //产品供需预约跟踪
        'shipping_reservation_work' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '1' 	=> '激活',
                    '-3'	=> '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '1'		=> '等待出货',
                    // '2' 	=> '已出货',
                    '3' 	=> '待异常反馈',
                    '4' 	=> '结束'
                )
            ),
            'abnormal_reason' => array(
                'name'	=> '异常理由',
                'value'	=> array(
                    '1'		=> '客户款项未到',
                    '2'		=> '客户取消订单',
                    '3'		=> '客户变更数量',
                    '4'		=> '产品供应不足',
                    '5'		=> '产品提前发货',
                    '6'		=> '产品质量异常',
                    '7'		=> '公司供需干预',
                    '8'		=> '其他'
                )
            ),
            'reservation_reason' => array(
                'name'	=> '预约依据',
                'value'	=> array(
                    '1'		=> '客户已经付款，确定按时出货',
                    '7'		=> '客户已经付款，不确定按时出货',
                    '2'		=> '客户未付款，确定按时出货',
                    '3'		=> '客户未付款，不确定按时出货',
                    '5'		=> '未超额度，确定按时出货',
                    '6'		=> '未超额度，不确定按时出货',
                    '4'		=> '其它'
                )
            ),
            'closed_time'	=> '关闭时间',
            'cc' => array('name' => '抄送给', 'func' => 'getUserInIds'),
            'real_reservation_num' => '实际预约数量',
            'reservation_num'	=> '预约出货数量',
            'assign' => array('name' => '指派给', 'func' => 'getUserById'),
            'assign_time' => '指派时间',
            'cancel_by' => array('name' => '取消者', 'func' => 'getUserById'),
            'cancel_time' => '取消时间'
        ),

        //产品版本维护
        'product_version' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '0'     => '未开始',
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '4'     => '关闭'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '1'		=> '待执行',
                    '2' 	=> '执行中',
                    '3' 	=> '待审核',
                    '4' 	=> '结束'
                )
            ),
            'erp_account'  =>array(
                'name'  => '维护数据所属公司',
                'value' => array(
                    '1'  => '雄迈/巨峰',
                    '2'  => '集成电路',
                )
            ),
            'reviewResult' => array(
                'name' => '评审结果',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '数据有误，驳回重新维护',
                    'reviewed_more'	=>	'需要其他人协助评审'
                )
            ),
        ),

        //生产任务单
        'production_order' => array(
            'order_status' => array(
                'name' => '单据状态',
                'value' => array(
                    '1' => '计划',
                    '2' => '下达',
                    '3' => '结案'
                )
            )
        ),

        //生产预测任务
        'production_forecast' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '1' 	=> '激活',
                    '2' 	=> '已变更',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '0'		=> '未开始',
                    '2'		=> '预测中',
                    '3' 	=> '待验收',
                    '4' 	=> '结束'
                )
            ),
            'stage_view'	=>	array(
                'name'	=>	'过程环节',
                'value'	=>	array(
                    '0'		=> '系统发起任务',
                    '2'		=> '开始预测',
                    '3' 	=> '完成任务',//3待验收
                    '-3' 	=> '上级领导审批',//3待验收
                    '4' 	=> '结束',
                    '5' 	=> '驳回'
                )
            ),
            'company'  =>array(
                'name'  => '生产公司',
                'value' => array(
                    '1'  => '杭州阔恒科技有限公司',
                    '2'  => '东莞市云阔信息技术有限公司'
                )
            ),
            'source'	=>	array(
                'name'	=>	'排产数据来源',
                'value'	=>	array(
                    '1'	=>	'销售订单',
                    '2'	=>	'计划排产'
                )
            ),
            'scheduling_status'	=>	array(
                'name'	=>	'排产状态',
                'value'	=>	array(
                    '1'	=>	'正常',
                    '0'	=>	'取消'
                )
            ),
            'finished_review' => array(
                'name' => '已完成预测评审',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '不准确，驳回重新预测'
                )
            ),
            'forecast_review' => array(
                'name' => '变更预测人评审',
                'value' => array(
                    'yes' => '允许变更',
                    'no' => '拒绝变更'
                )
            ),
            'intervene_review' => array(
                'name' => '干预评审',
                'value' => array(
                    'adopt' => '允许干预',
                    'refuse' => '拒绝干预'
                )
            ),
            'erp_account' => array(
                'name' => '所属账套',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成电路',
                    '4' => '香港雄迈'
                )
            ),
        ),
        //生产任务反馈内容表
        'production_feedback' => array(
            'produce_palce' => array(
                'name' => '产地',
                'value'	=> array(
                    '0' => '请选择产地',
                    '1' => '杭州',
                    '2' => '东莞',
                    '3' => '深圳'
                )
            )
        ),

        'customer' => array(
            'company' => array(
                'name'	=> '所属公司',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成电路',
                    '4' => '云阔'
                )
            ),
            'level'	=> array(
                'name' => '客户等级',
                'value' => array(
                    '1'	=> '普通客户',
                    '2'	=> '重大客户',
                    '3' => '核心客户',
                    '4'	=> '战略客户'
                )
            ),
            'erp_account' => array(
                'name' => '账套',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成电路',
                    '4' => '香港雄迈'
                )
            ),
        ),
        'material_place' => array(
            'produce_palce' => array(
                'name' => '产地',
                'value'	=> array(
                    '1' => '杭州',
                    '2' => '东莞',
                    '3' => '深圳'
                )
            ),
        ),
        'real_materiel' => array(
            'erp_account' => array(
                'name' => '所属账套',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '雄迈集成电路',
                    '4' => '香港雄迈',
                )
            ),
            'control_materiel_life_cycle' => array(
                'name' => '管控生命周期',
                'value' => array(
                    '1' => '研发阶段',
                    '2' => '中试阶段',
                    '3' => '量产阶段',
                    '4' => '停产阶段',
                    '5' => '终止阶段',
                    '6' => '升级切换阶段',
                    '7' => '批量试生产阶段',
                )
            ),
            'buy_type'	=> array(
                'name' => '物料来源',
                'value' => array(
                    '1' => '外购',
                    '2' => '客供',
                    '3' => '常规自制',
                    '4' => '派生自制',
                    '5' => '委外加工',
                )
            )
        ),

        'workshop_list'	=> array(
            'position' => array(
                'name'	=> '车间地点',
                'value'	=> array(
                    '1'	=> '杭州',
                    '2'	=> '东莞'
                )
            )
        ),

        //属性项维护
        'product_attributes_group' => array(
            'attr_change_field' => array(
                'name' => '变更内容',
                'value' => array(
                    'name' 					=> '名称',
                    'status' 				=> '状态',
                    'create_by' 			=> '创建人',
                    'create_time' 			=> '创建时间',
                    'last_edit_by' 			=> '最后维护人',
                    'last_edit_time' 		=> '最后维护时间',
                    'level' 				=> '重要等级',
                    'input_type' 			=> '属性维护方式',
                    'input_value' 			=> '属性列表',
                    'sort' 					=> '排序',
                    'normal_value' 			=> '正常值',
                    'normal_value_reason' 	=> '正常值依据',
                    'abnormal_value_type' 	=> '异常值类型',
                    'abnormal_value' 		=> '异常值内容',
                    'abnormal_value_reason' => '异常值依据',
                )

            ),
            'input_type'	=> array(
                'name'	=> '维护方式',
                'value'	=> array(
                    '1'	=> '手工录入',
                    '2'	=> '下拉菜单选择',
                    '3'	=> '多项选择'
                )
            ),
//			'category'	=> array(
//				'name'	=> '属性组类别',
//				'value'	=> array(
//					'1'	=> '规格属性组',
//					'2'	=> '后勤属性组'
//				)
//			),
            'abnormal_value_type'	=> array(
                'name'	=> '异常值类型',
                'value'	=> array(
                    '1'	=> '文字描述',
                    '2'	=> '偏差率（同正常值比）'
                )
            ),
            'type'	=> array(
                'name'	=> '更新类型',
                'value'	=> array(
                    '1'	=> '修改',
                    '0'	=> '新增',
                    '-1'=> '禁用',
                )
            ),
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消',
                    '-6'	=> '超期取消',
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '2'		=> '执行中',
                    '3' 	=> '待评审',
                    '4' 	=> '结束'
                )
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'review' => array(
                'name' => '属性组评审',
                'value' => array(
                    'pass'				=>	'同意',
                    'reject'			=>	'驳回',
                    'waiver'			=>	'弃权',
                    'part_in_review'	=>	'邀请其他人协助评审'
                ),
            ),
            'attr_status' => array(
                'name' => '状态',
                'value' => array(
                    '1' 	=> '启用',
                    '0' 	=> '待审核',
                    '-1'	=> '禁用',
                )
            ),
        ),

        //产品属性
        'product_attributes_list' => array(
            'level'	=> array(
                'name'	=> '属性级别',
                'value'	=> array(
                    '1'	=> '核心属性',
                    '2'	=> '普通属性'
                )
            )
        ),

        //产品新增任务
        'add_production_work' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '-2'    => '拒绝',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '2'		=> '执行中',
                    '3' 	=> '待评审',
                    '4' 	=> '结束'
                )
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'review' => array(
                'name' => '评审',
                'value' => array(
                    'pass'					=> '同意',
                    'invite_other_review'	=> '邀请其他人参与评审',
                    'reject'				=> '驳回',
                    'refuse'				=> '拒绝'
                ),
            )
        ),

        //销售产品定价
        'set_production_price' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '2'		=> '执行中',
                    '3' 	=> '待评审',
                    '4' 	=> '结束'
                )
            ),
            'stage_view'	=>	array(
                'name'	=>	'流程环节',
                'value'	=>	array(
                    '2'		=> '起草任务_完成',
                    '3'		=> '评审任务',
                    '-3' 	=> '评审结果_完成_驳回',
                    '4' 	=> '结束',
                    '5' 	=> '编辑任务信息_完成'
                )
            ),
            'company'  =>array(
                'name'  => '生产公司',
                'value' => array(
                    '1'  => '杭州阔恒科技有限公司',
                    '2'  => '东莞市云阔信息技术有限公司'
                )
            ),
            'review' => array(
                'name' => '定价评审',
                'value' => array(
                    'pass'				=>	'同意',
                    'pass_myprice'		=>	'同意，按我的建议定价',
                    'pass_ave'			=>	'同意，取建议的平均值',
                    // 'pass_ave_nomaxmin'	=>	'同意，取去掉最高价和最低价后的平均值',
                    'waiver'			=>	'弃权'
                ),
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )),
            'category' => array(
                'name' => '产品类别',
                'value' => array(
                    '1' => 'DVR整机',
                    '2' => 'IPC整机',
                    '3' => 'NVR整机',
                    '4' => 'XAC整机',
                    '5' => '服务器',
                    '6' => '解码器',
                    '7' => '拼接屏',
                    '8' => '球机整机',
                    '9' => 'DVR方案',
                    '10' => 'DVR主板',
                    '11' => 'ES芯片',
                    '12' => 'IPC模组',
                    '13' => 'XVI ISP芯片',
                    '14' => 'NVR方案',
                    '15' => 'NVR主板',
                    '16' => 'IP SoC芯片',
                    '17' => 'XAC模组',
                    '18' => '消费监控整机',
                    '19' => '移动视频整机',
                    '20' => '智能家居整机',
                    '21' => '一体机芯',
                    '22' => '精品配件类',
                    '23' => '传统配件类',
                    '24' => '贸易类',
                )
            ),
            'unit'	=> array(
                'name'	=> '币别',
                'value'	=> array(
                    '1'	=> '人民币',
                    '2'	=> '美元',
                    '3'	=> '欧元',
                    '4'	=> '英镑',
                    '5'	=> '港币',
                    '6'	=> '日元',
                    '7'	=> '加元',
                    '8'	=> '澳元',
                    '9'	=> '瑞郎',
                )
            ),
            'unit_symbol'	=> array(
                'name'	=> '币别符号',
                'value'	=> array(
                    '1'	=> '¥',
                    '2'	=> '$',
                    '3'	=> '€',
                    '4'	=> '£',
                    '5'	=> 'HK$',
                    '6'	=> 'J￥',
                    '7'	=> 'C$',
                    '8'	=> 'A$',
                    '9'	=> 'SF',
                )
            ),
            'sales_class'	=> array(	//销售分类
                'name'	=> '销售分类',
                'value'	=> array(
                    '1'	=> '渠道主流',
                    '2'	=> '渠道扩展',
                    '3'	=> '工程主流',
                    '4'	=> '工程扩展',
                    '5'	=> '普通定制',
                    '6'	=> 'OEM定制'
                )
            ),
            'pro_positioning'	=> array(	//产品定位
                'name' => '产品定位',
                'value'	=> array(
                    '1'	=> '竞品',
                    '2'	=> '普通',
                    '3'	=> '拓展'
                )
            ),
            'price_range'	=> array(	//定价范围
                'name'	=> '定价范围',
                'value'	=> array(
                    '1'	=> '全部客户',
                    '2'	=> '普通客户',
                    '3'	=> '重大客户',
                    '4'	=> '核心客户',
                    '5'	=> '战略客户',
                    '6'	=> '无限制'
                )
            ),
            'price_factor'	=> array(	//价格因素
                'name'	=> '价格因素',
                'value'	=> array(
                    '1'	=> '产品成本',
                    '2'	=> '产品平衡',
                    '3'	=> '公司平衡',
                    '4'	=> '战略统筹',
                    '5'	=> '市场竞争'
                )
            ),
            'type' => array(
                'name'	=> '比较类型',
                'value'	=> array(
                    '1'	=> array(
                        '1'	=> '成本左右',
                        '2'	=> '成本*1.1左右',
                        '3'	=> '成本*1.2左右',
                        '4'	=> '成本*1.3左右',
                        '5'	=> '成本*1.4左右',
                        '6'	=> '未考虑'
                    ),
                    '2'	=> array(
                        '1'	=> '高于',
                        '2'	=> '低于',
                        '3'	=> '替代',
                        '4'	=> '未考虑'
                    ),
                    '3'	=> array(
                        '1'	=> '高于',
                        '2'	=> '低于',
                        '3'	=> '替代',
                        '4'	=> '未考虑'
                    ),
                    '5'	=> array(
                        '1'	=> '高于',
                        '2'	=> '低于',
                        '3'	=> '替代',
                        '4'	=> '未考虑'
                    )
                )
            )
        ),

        'sale_product' => array(
            'first_category' => array(
                'name' => '行业大类',
                'value' => array(
                    '1' => '传统整机类',
                    '2' => '方案授权类',
                    '3' => '模块模组类',
                    '4' => '半导体IC类',
                    '5' => '雄迈精品类',
                )
            ),
            'category' => array(
                'name' => '产品类别',
                'value' => array(
                    '1' => 'DVR整机',
                    '2' => 'IPC整机',
                    '3' => 'NVR整机',
                    '4' => 'XAC整机',
                    '5' => '服务器',
                    '6' => '解码器',
                    '7' => '拼接屏',
                    '8' => '球机整机',
                    '9' => 'DVR方案',
                    '10' => 'DVR主板',
                    '11' => 'ES芯片',
                    '12' => 'IPC模组',
                    '13' => 'XVI ISP芯片',
                    '14' => 'NVR方案',
                    '15' => 'NVR主板',
                    '16' => 'IP SoC芯片',
                    '17' => 'XAC模组',
                    '18' => '消费监控整机',
                    '19' => '移动视频整机',
                    '20' => '智能家居整机',
                    '21' => '一体机芯',
                    '22' => '配件类'
                )
            ),
            'brand' => array(
                'name' => '产品品牌',
                'value' => array(
                    '1'	=> '中性',
                    '2'	=> '雄迈',
                    '3'	=> '巨峰',
                    '4'	=> 'OEM'
                ),
            ),
            'first_quality_standard' => array(
                'name' => '一级品质标准',
                'value' => array(
                    '1'	=> '完全供方标准',
                    '2'	=> '完全需方标准',
                    '3'	=> '供需双方联合标准'
                ),
            ),
            'second_quality_standard' => array(
                'name' => '二级品质标准',
                'value' => array(
                    '1'	=> array(
                        '1' => '通用标准',
                    ),
                    '2'	=> array(
                        '1' => '通用标准',
                    ),
                    '3'	=> array(
                        '1' => '通用标准',
                    )
                ),
            ),
            'provide_type' => array(
                'name' => '物料提供方式',
                'value' => array(
                    '1'	=> '完全供方提供',
                    '2'	=> '完全需方提供',
                    '3'	=> '供需双方联合提供'
                ),
            ),
            'processing_cooperation_type' => array(
                'name' => '加工合作方式',
                'value' => array(
                    '1'	=> '完全供方加工',
                    '2'	=> '完全需方加工',
                    '3'	=> '供需双方联合加工'
                ),
            ),
            'delivery_warehouse_place' => array(
                'name' => '出货仓库所在地',
                'value' => array(
                    '1'	=> '东洲东莞两地',
                    '2'	=> '杭州东洲',
                    '3'	=> '东莞长龙',
                    '4'	=> '香港',
                    '5'	=> '其它地方'
                ),
            ),
            'delivery_warehouse_place' => array(
                'name' => '出货仓库所在地',
                'value' => array(
                    '1'	=> '东洲东莞两地',
                    '2'	=> '杭州东洲',
                    '3'	=> '东莞长龙',
                    '4'	=> '香港',
                    '5'	=> '其它地方'
                ),
            ),
        ),

        //属性配置对象类别
        'sale_product_category'	=> array(
            'category_type'	=> array(
                'name'	=> '属性配置对象类别类型',
                'value'	=> array(
                    '1'	=> '产品',
                    '2'	=> '客户',
                    '3'	=> '供应商',
                    '4'	=> '员工',
                    '5'	=> '组织',
                    '6'	=> '有形资产',
                    '7'	=> '无形资产',
                    '8'	=> '金融',
                    '9'	=> '物料',
                    '10'	=> 'BOM',
                    '11'	=> '积分'
                )
            )
        ),

        //产品报价
        'production_quoted_price_work' => array(
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                ),
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '0'		=> '未开始',
                    '2'		=> '执行中',
                    '3' 	=> '待审核',
                    '4' 	=> '结束'
                )
            ),
            'price_unit' =>array(
                'name'  => '报价币别',
                'value' => array(
                    '1'   =>  '人民币',
                    '2'   =>  '美元',
                ),
            ),
            'quotations_category' => array(
                'name'  => '报价类型',
                'value' => array(
                    '1'  => '标准报价',
                    '2'  => '客户报价',
                ),
            ),
            'review' => array(
                'name' => '报价评审',
                'value' => array(
                    'pass'				=>	'同意，按照申请价报价',
                    'pass_myprice'		=>	'同意，按我的建议报价',
                    'pass_ave'			=>	'同意，取建议的平均值',
                    //'pass_ave_nomaxmin'	=>	'同意，取去掉最高价和最低价后的平均值',
                    'waiver'			=>	'弃权',
                    'invite_other_review' => '邀请他人评审'
                ),
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )),

            'stage_view'	=>	array(
                'name'	=>	'流程环节',
                'value'	=>	array(
                    '2'		=> '起草任务_完成',
                    '-2'	=> '报价类型_小于公开价',
                    '3'		=> '评审任务',
                    '-5' 	=> '编辑报价信息_同意_驳回',
                    '4' 	=> '结束',
                    '5' 	=> '评审结果_完成'
                )
            ),
            'company'   => array(
                'name'	=>	'division表中的company',
                'value'	=>	array(
                    '1'	=> '杭州雄迈信息技术有限公司',
                    '2'	=> '杭州巨峰科技有限公司',
                    '3'	=> '杭州雄迈集成电路技术有限公司',
                )
            ),
        ),

        //销售产品类别维护
        'production_category_work' => array(
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '2'		=> '执行中',
                    '3' 	=> '待评审',
                    '4' 	=> '结束'
                )
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'stage_view'	=>	array(
                'name'	=>	'流程环节',
                'value'	=>	array(
                    '2'		=> '起草任务_完成',
                    '3'		=> '评审任务',
                    '-3' 	=> '评审结果_完成_驳回',
                    '4' 	=> '结束',
                    '5' 	=> '编辑任务信息_完成'
                )
            ),
            'review' => array(
                'name' => '评审',
                'value' => array(
                    'pass'					=> '同意',
                    'invite_other_review'	=> '邀请其他人参与评审',
                    'reject'				=> '驳回'
                ),
            )
        ),
        //模组模块关系
        'materiel_relation' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '1' 	=> '激活',
                    '0' 	=> '待评审',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '2'		=> '执行中',
                    '3' 	=> '待评审',
                    '4' 	=> '结束'
                )
            ),
            'stage_view'	=>	array(
                'name'	=>	'流程环节',
                'value'	=>	array(
                    '2'		=> '起草任务_完成',
                    '3'		=> '评审任务',
                    '-3' 	=> '评审结果_完成_驳回',
                    '4' 	=> '结束',
                    '5' 	=> '编辑任务信息_完成'
                )
            ),
            'review' => array(
                'name' => '定价评审',
                'value' => array(
                    'pass'	=>	'同意',
                    'waiver'	=>	'弃权'
                )
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'company' => array(
                'name' => '所属公司',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成电路'
                )
            ),
        ),
        //物料替代关系
        'aa_materiel_relation' => array(
            'apply_range'	=> array(
                'name'	=> '适用范围',
                'value'	=> array(
                    '0' 	=> '全部适用',
                    '1'	    => '部分适用',
                    '2'   	=> '部分不适用'
                )
            ),
            'review' => array(
                'name' => '物料替换评审',
                'value' => array(
                    'pass'				=>	'通过',
                    'restart'			=>	'驳回',
                    'reject'			=>	'拒绝',
                    'reviewed_again'	=>	'邀请物料相关的产品经理进行决策',
                    'reviewed_more'		=>	'邀请其他人参与评审',
                    'task_common' 		=> 	'发起关联任务',
                    'abstain' 			=> 	'弃权'
                ),
            ),
            'company' => array(
                'name' => '所属公司',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成'
                )
            ),
        ),
        //物料生产交期维护
        'materiel_product_delivery_date' => array(
            'status'	=> array(
                'name'	=> '状态',
                'value'	=> array(
                    '1' 	=> '激活',
                    '3'	    => '完成',
                    '-3'    => '关闭',
                    '-5'	=> '取消'
                )
            ),
            'stage'		=> array(
                'name'	=> '阶段',
                'value'	=> array(
                    '2'		=> '执行中',
                    '3' 	=> '待评审',
                    '4' 	=> '结束'
                )
            ),
            'stage_view'	=>	array(
                'name'	=>	'流程环节',
                'value'	=>	array(
                    '2'		=> '起草任务_完成',
                    '3'		=> '评审任务',
                    '-3' 	=> '评审结果_完成_驳回',
                    '4' 	=> '结束',
                    '5' 	=> '编辑任务信息_完成'
                )
            ),
            'priority' => array(
                'name' => '优先级',
                'value' => array(
                    '1' => '低',
                    '2' => '普通',
                    '3' => '高',
                    '4' => '紧急'
                )
            ),
            'company' => array(
                'name' => '所属公司',
                'value' => array(
                    '1' => '雄迈',
                    '2' => '巨峰',
                    '3' => '集成电路'
                )
            ),
        ),
        //采购跟踪
        'procurement_track' => array(
            'process' => array(
                'name'	=> '提货进度',
                'value'	=> array(
                    '1' 	=> '备货中',
                    '2'	    => '已发货',
                    '3'	    => '已到货',
                )
            ),
            'progress' => array(
                'name' => '当前进度',
                'value' => array(
                    '1' => '备货中',
                    '2' => '发货进行中',
                    '3' => '签收中',
                    '4' => '请检中',
                    '5' => '送检中',
                    '6' => '品质检验中',
                    '6_2' => '品质异常评审中',
                    '7' => '送库中',
                    '7_2' => '入库中',
                    '8' => '入账中',
                    '9' => '退货中',
                    '10' => '不良挑选中',
                    '11' => '签收异常处理中'
                )
            ),
            'taken_status' => array(
                'name' => '当前进度',
                'value'	=> array(
                    '1'	=> '备货中',
                    '2'	=> '部分发货',
                    '3'	=> '已发货',
                    '4'	=> '部分到货',
                    '5'	=> '已到货'
                )
            ),
            'is_normal' => array(
                'name'	=> '是否异常',
                'value'	=> array(
                    '1' 	=> '是',
                    '2'	    => '否',
                )
            ),
            'abnormal_type' => array(
                'name'	=> '异常类型',
                'value'	=> array(
                    '1' 	=> '物流异常',
                    '2'	    => '供货异常',
                    '3'	    => '品质异常',
                    '4'	    => '价格异常',
                    '6'	    => '提前交货',
                    '7'	    => '推迟交货',
                    '8'	    => '收料异常',
                    '5'	    => '其它',
                )
            ),
            'express_type' => array(
                'name' => '快递公司',
                'value' => array(
                    '1' 	=> '顺丰速运',
                    '2' 	=> '圆通快递',
                    '3' 	=> '中通快递',
                    '4'		=> '跨越速运',
                    '5'		=> '百世汇通',
                    '6'		=> '德邦',
                    '7'		=> '速尔快递',
                    '8' 	=> '申通快递',
                    '9'		=> '京广速递',
                    '10' 	=> '天天快递',
                    '11'	=> '龙邦快运',
                    '12'	=> '信丰物流',
                    '13'	=> '联昊通速递 ',
                    '14'	=> '加运美',
                    '15' 	=> '韵达快递',
                    '16' 	=> '邮政',
                    '17' 	=> '全峰快递',
                    '18' 	=> '全一快递',
                    '19' 	=> '京东',
                    '20' 	=> '优速物流',
                    '21' 	=> '速腾',
                    '22' 	=> '联邦',
                    '23' 	=> '平安达',
                    '24' 	=> '运通',
                )
            ),
            'taken_result' => array(
                'name' => '交货结果',
                'value' => array(
                    '0' 	=> '初始化',
                    '1' 	=> '正常交货',
                    '2'	=> '提前交货',
                    '3'	=> '延迟交货',
                )
            ),
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '0' 	=> '待继续',
                    '1' 	=> '激活',
                    '3' 	=> '完成',
                    '-2' 	=> '暂停',
                    '-3'	=> '关闭',
                    '-5'	=> '取消',
                    '-6'	=> '超期取消',
                )
            ),
            'stage' => array(
                'name' => '阶段',
                'value' => array(
                    '0' 	=> '未开始',
                    '1' 	=> '待细分/待执行',
                    '2' 	=> '已细分/执行中',
                    '3' 	=> '待评审',
                    '4'	=> '结束'
                )
            ),
            'stage_with_child' => array(
                'name' => '有子任务的阶段',
                'value' => array(
                    '0' 	=> '未开始',
                    '1' 	=> '待细分',
                    '2' 	=> '已细分',
                    '3' 	=> '待评审',
                    '4'	=> '结束'
                )
            ),
            'stage_without_child' => array(
                'name' => '没有子任务的阶段',
                'value' => array(
                    '0' 	=> '未开始',
                    '1' 	=> '待执行',
                    '2' 	=> '执行中',
                    '3' 	=> '待评审',
                    '4'	=> '结束'
                )
            ),
            'task_type' => array(
                'name' => '任务类型',
                'value' => array(
                    '1' 		=> '订单跟踪',
                    '2'	    => '提货跟踪',
                    '3'	    => '日常反馈',
                    '4'	    => '排产缺料分解',
                )
            ),
            'entry_task_type' => array(
                'name' => '任务类型',
                'value' => array(
                    '1' 	=> '来料签收',
                    '2' 	=> '来料请检',
                    '3' 	=> '来料送检',
                    '4'	=> '品质检验',
                    '5'	=> '入库',
                    '6'	=> '入账',
                    '7'	=> '签收异常处理',
                    '8' 	=> '来料挑选',
                    '9'	=> '退货',
                )
            ),
            'sign_status' => array(
                'name' => '签收状态',
                'value' => array(
                    '1' 	=> '待签收',
                    '2' 	=> '签收中',
                    '3' 	=> '已签收'
                )
            ),
            'sign_result' => array(
                'name' => '签收结果',
                'value' => array(
                    '1' 	=> '签收正常',
                    '2' 	=> '签收异常'
                )
            ),
            'materiel_attr' => array(
                'name' => '来料属性',
                'value' => array(
                    '1' 	=> '正常外购',
                    '2' 	=> '委外加工',
                    '3' 	=> '东莞/杭州调拨',
                    '4' 	=> '小批量',
                    '5' 	=> '样品',
                    '6' 	=> '客供件',
                )
            ),
            'company' => array(
                'name' => '所属公司',
                'value' => array(
                    '1' => '雄迈之家 ',
                    '2' => '杭州巨峰科技有限公司 ',
                    '3' => '杭州雄迈集成电路技术有限公司'
                )
            ),
            'place' => array(
                'name' => '交货地',
                'value' => array(
                    '1' => '杭州',
                    '2' => '东莞',
                    '3' => '深圳',
                    '4' => '香港',
                )
            ),
            'deal_type' => array(
                'name' => '交易类型',
                'value' => array(
                    '0' => '非同仓',
                    '1' => '同仓',
                )
            ),
            'shipping_type'	=> array(
                'name'	=> '运输方式',
                'value'	=> array(
                    '1'	=> '送货',
                    '2' => '物流',
                    '3'	=> '自提'
                )
            ),
            'abnormal_bad_level' => array(
                'name'	=> '签收异常严重程度',
                'value'	=> array(
                    '1'	=> '轻微',
                    '2' 	=> '一般',
                    '3'	=> '严重'
                )
            ),
            'abnormal_review_result' => array(
                'name'	=> '签收异常评审结果',
                'value'	=> array(
                    '1'	=> '接受',
                    '2'  => '不接受'
                )
            ),
            'abnormal_progress' => array(
                'name'	=> '签收异常评审结果',
                'value'	=> array(//0：未开始 1：待处理  2：处理中 3：已处理
                    '0' => '未开始',
                    '1' => '待处理',
                    '2' => '处理中',
                    '3' => '已处理'
                )
            ),
            'return_reason' => array(
                'name'	=> '退货来源',
                'value'	=> array(
                    '1'	=> '签收异常退货',
                    '2'  => '检验不合格退货'
                )
            ),
            'return_progress_status' =>array(
                'name' => '退货进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待退货',
                    '2' => '退货中',
                    '3' => '已退货'
                )
            ),
            'abnormal_reason'	=> array(
                'name'	=> '运输方式',
                'value'	=> array(
                    '1'	=> '节假日休息',
                    '2' => '请假休息',
                    '3'	=> '忘记反馈',
                    '4'	=> '物料未到货',
                    '5'	=> '其他 '
                )
            ),
            'sign_progress' => array(
                'name' => '签收进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待签收',
                    '2' => '签收中',
                    '3' => '已签收'
                )
            ),
            'test_type' => array(
                'name' => '检验方式',
                'value' => array(
                    '1' => '抽检',
                    '2' => '全检',
                    '3' => '免检'
                )
            ),
            'test_result' => array(
                'name' => '检验结果',
                'value' => array(
                    '1' => '合格',
                    '2' => '不合格'
                )
            ),
            'test_purchase_review' => array(
                'name' => '检验采购评审',
                'value' => array(
                    'return_satisfy' => '退料后补货可以满足计划需求',
                    'no_return_satisfy' => '退料后补货无法满足计划需求'
                )
            ),
            'plan_break_review' => array(
                'name' => '排产分解评审',
                'value' => array(
                    'pass'				=>	'确认通过',
                    'mybreak'			=>	'按我的建议分解提货',
                    'reviewed_up' 		=> 	'邀请供应委员会进行主审'
                )
            ),
            'review_role' => array(
                'name' => '评审职能角色',
                'value' => array(
                    'PLAN' => '计划',
                    'PURCHASING' => '采购',
                    'MRB' => 'MRB',
                    'MANAGER' => '品质经理',
                )
            ),
            'test_review_result' => array(
                'name' => '检验评审',
                'value' => array(
                    'return' => '退货',
                    'pick' => '挑选使用',
                    'special' => '特采',
                )
            ),
            'test_review' => array(
                'name' => '检验评审',
                'value' => array(
                    '1' => '退货',
                    '2' => '挑选使用',
                    '3' => '特采',
                )
            ),
            'test_review_progress' => array(
                'name' => '检验评审进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待评审',
                    '2' => '评审中',
                    '3' => '评审结束'
                )
            ),
            'invite_status' => array(
                'name' => '请检状态',
                'value' => array(
                    '0' => '待请检',
                    '1' => '请检中',
                    '2' => '已请检'
                )
            ),
            'invite_test_progress' =>array(
                'name' => '请检进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待请检',
                    '2' => '请检中',
                    '3' => '已请检'
                )
            ),
            'send_test_progress' =>array(
                'name' => '送检进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待送检',
                    '2' => '送检中',
                    '3' => '已送检'
                )
            ),
            'test_progress' =>array(
                'name' => '检验进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待检验',
                    '2' => '检验中',
                    '3' => '已检验'
                )
            ),
            'pick_progress' =>array(
                'name' => '挑选进度',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待挑选',
                    '2' => '挑选中',
                    '3' => '已挑选'
                )
            ),
            'send_status' => array(
                'name' => '送检状态',
                'value' => array(
                    '1' => '待送检',
                    '2' => '送检中',
                    '3' => '已送检'
                )
            ),
            'into_account_progress_status' => array(
                'name' => '送库',
                'value' => array(
                    '0' => '未开始',
                    '1' => '待入账',
                    '2' => '入账中',
                    '3' => '已入账'
                )
            ),
            'send_warehouse'=> array(
                'name' => '送库',
                'value' => array(
                    '1' => '原材料仓',
                    '2' => '半成品仓',
                    '3' => '成品仓'
                )
            ),
            'send_warehouse_status'=> array(
                'name' => '送库',
                'value' => array(
                    '0' => '待送库',
                    '1' => '送库中',
                    '2' => '仓库接收中',
                    '3' => '已入库'
                )
            ),
            'send_warehouse_review' => array(
                'name' => '送库评审',
                'value' => array(
                    'pass' => '通过',
                    'restart' => '驳回'
                )
            )
        ),

        //采购跟踪统计
        'procurement_track_report'	=> array(
            'show_abnormal_type' => array(
                'name'	=> '展示异常信息',
                'value'	=> array(
                    '1'	=> '全部',
                    '2'	=> '只展示异常订单',
                    '3' => '只展示无异常订单'
                )
            ),

            'abnormal_type' => array(
                'name'	=> '异常类型',
                'value'	=> array(
                    '1' 	=> '物流异常',
                    '2'	    => '供货异常',
                    '3'	    => '品质异常',
                    '4'	    => '价格异常',
                    '6'	    => '提前交货',
                    '7'	    => '推迟交货',
                    '8'	    => '收料异常',
                    // '6'	    => '任务执行超期',
                    // '7'	    => '签收异常',
                    '5'	    => '其它'
                )
            ),
            'expire_type' => array(
                'name'	=> '异常类型',
                'value'	=> array(
                    '1' 	=> '节假日休息',
                    '2'	    => '请假休息',
                    '3'	    => '忘记反馈',
                    '4'	    => '物料未到货',
                    '5'	    => '其它'
                )
            ),
        ),
        'control_materiel_setting_log' => array(
            'setting_type' => array(
                'name' => '管控物料设置方式',
                'value' => array(
                    '1' => '产品委员会',
                    '2' => '产品经理',
                    '3' => '计划干预',
                    '4' => '批量试生产',
                    '5' => '管控物料配置任务',
                    '6' => '物料新增'
                )
            )
        ),

        //定期生成通用任务
        'regular_common_tasks' => array(
            'status' => array(
                'name'  => '状态',
                'value' => array(
                    '1' => '激活',
                    '-4' => '暂时搁置',
                    '-5' => '取消',
                )
            ),
            'type' => array(
                'name'	=> '重复方式',
                'value'	=> array(
                    '6' 	=> '只执行一次',
                    '1' 	=> '每天',
                    '2'	    => '每周',
                    '3'	    => '每月',
                    '4'	    => '每季度',
                    '5'	    => '每年',
                )
            ),
        ),

        //站内信
        'message' => array(
            'status' => array(
                'name'  =>  '站内信',
                'value' =>  array(
                    '0' =>  '未读',
                    '1' =>  '已读',
                    '2' =>  '全部'
                )
            ),
        ),

        //供应商业务员配置
        'supplier_salesman_relation' => array(
            'status' => array(
                'name'  =>  '供应商状态',
                'value' =>  array(
                    '0' =>  '不可用',
                    '1' =>  '可用'
                )
            ),
            'account' => array(
                'name'  =>  '账套',
                'value' =>  array(
                    '1' =>  '雄迈',
                    '2' =>  '巨峰',
                    '3' =>  '集成',
                    '4' =>	'香港雄迈'
                )
            ),
        ),

        //商务策略导入任务
        'decision_import' => array(
            'status' => array(
                'name'  =>  '供应商状态',
                'value' =>  array(
                    '1'  => '激活',
                    '3'  => '完成',
                    '-3' => '关闭',
                    '-5' => '取消'
                )
            ),
            'stage' => array(
                'name'  =>  '供应商状态',
                'value' =>  array(
                    '1' => '待执行',
                    '2' => '执行中',
                    '3' => '待审核',
                    '4' => '结束'
                )
            ),

        ),

        //任务委托
        'entrust_tasks' => array(
            'status' => array(
                'name'  => '状态',
                'value' =>  array(
//                    '1'  => '激活',
                    '3'  => '完成',
                    '-2' => '拒绝',
                    '-3' => '关闭',
                    '-5' => '取消'
                )
            ),
            'stage' => array(
                'name'  =>  '阶段',
                'value' =>  array(
                    '1' => '待执行',
                    '2' => '执行中',
                    '3' => '待审核',
                    '4' => '结束'
                )
            ),
            'object_type' => array(
                'name'	=> '委托类型',
                'value'	=> array(
                    '1' 	=> '账号',
                    '2' 	=> '角色',
                )
            ),
            'review' => array(
                'name' => '评审',
                'value' => array(
                    'pass'	=> '同意',
                    'refuse'=> '拒绝'
                ),
            )
        ),
        'warehouse_list' => array(
            'pid' => array(
                'name' => '仓库类型',
                'value' => array(
                    '0' => '全部',
                    '1' => '深圳仓库',
                    '2' => '杭州仓库',
                    '3' => '成都仓库',
                    '4' => '借用仓',
                    '5' => '东莞仓库',
                    '6' => '中试仓库',
                    '7' => '京东仓库',
                    '8' => '菜鸟仓库',
                    '9' => '杭州阔恒仓库',
                    '10' => '东莞云阔仓库',
                    '11' => '杭州雄迈集成电路仓库',
                    '12' => '东莞集成电路仓库',
                    '13' => '香港雄迈仓库'
                )
            )
        ),
        //权限申请
        'access_apply_tasks' => array(
            'status' => array(
                'name'  =>  '状态',
                'value' =>  array(
                    '3'  => '完成',
                    '-2'  => '拒绝',
                    '-3' => '关闭',
                    '-5' => '取消'
                )
            ),
            'stage' => array(
                'name'  =>  '阶段',
                'value' =>  array(
                    '3' => '待审核',
                    '4' => '结束'
                )
            ),
        )
    ),
    //干预任务
    'productManager' => array(
        'title' => '产品委员会干预',
        'field' => array(
            'product' => '干预对象',
            'change_num' => '干预数量',
            'date' => '干预时间',
            'reason' => '干预理由'
        ),
        'reasons' => array(
            'name' => '干预依据',
            'value' => array(
                '1' => '销售旺季，需要增加预测',
                '2' => '销售淡季，需要减少预测',
                '3' => '新品发布，需要提前预测',
                '4' => '产品切换，需要减少预测',
                '5' => '战略考虑，需要增加预测',
                '6' => '战略考虑，需要减少预测',
                '8' => '预估较多，可转化为预测进行备货',
                '7' => '其它',
            )
        )
    ),
    'productManaging' => array(
        'title' => '产品经理干预',
        'field' => array(
            'product' => '干预对象',
            'change_num' => '干预数量',
            'date' => '干预时间',
            'reason' => '干预理由'
        ),
    ),
    'materielPlanning' => array(
        'title' => '物控计划干预',
        'field' => array(
            'product' => '干预对象',
            'change_num' => '干预数量',
            'date' => '干预时间',
            'reason' => '干预理由'
        )
    ),
    'batchProduct' => array(
        'title' => '批量试生产',
        'field' => array(
            'product' => '干预对象',
            'change_num' => '干预数量',
            'date' => '任务时间',
            'reason' => '干预理由'
        ),
        'sale_status' => array(
            'name' => '销售状态',
            'value' => array(
                '1' => '公开销售',
                '2' => '限定销售',
                '3' => '暂缓销售',
            )
        ),

        'materiel_type' => array(
            'name' => '管控物料类型',
            'value' => array(
                '1' => '关键',
                '2' => '扩展',
                '3' => '限制',
                '4' => '禁用',
                '5' => '测试',
                '6' => '试用',
            )
        ),
        'materiel_basis' => array(
            'name' => '管控物料依据',
            'value' => array(
                '1' => '供货周期长',
                '2' => '资金占比大',
                '3' => '可替代性弱',
                '4' => '质量风险大',
                '5' => '降价风险大',
                '6' => '淘汰风险大',
                '7' => '替代风险大',
                '8' => '其它原因',
            )
        ),
        'materiel_classify' => array(
            'name' => '管控物料分类',
            'value' => array(
                '1' => 'PCB',
                '2' => 'SOC主控',
                '3' => 'DDR内存',
                '4' => '传感器',
                '5' => 'FLASH',
                '6' => 'WIFI芯片',
                '7' => 'AD芯片',
                '8' => '电源芯片',
                '9' => '镜头',
                '10' => '外购模组',
                '11' => '结构件',
                '12' => '附件',
                '13' => '其它',
                '14' => '整机',
                '15' => '模组',
                '16' => '包装',
                '17' => 'ICR',
                '18' => '天线',
                '19' => '电池',
                '20' => '液晶屏',
                '21' => '电机',
                '22' => '接插件',
                '23' => '连接线',
                '24' => '红外灯板',
                '25' => 'ISP芯片',
                '26' => 'DA芯片',
                '27' => '其它芯片',
            ),
        ),
        'make_price_reason' => array(
            'name' => '重新定价理由',
            'value' => array(
                '1' => '首次发布，未定过价',
                '2' => '成本上升，建议重新定价',
                '3' => '成本下降，建议重新定价',
                '4' => '竞争提升，建议重新定价',
                '5' => '其它原因',
            ),
        ),
        'make_train_reason' => array(
            'name' => '重新培训理由',
            'value' => array(
                '1' => '首次发布，未培训过',
                '2' => '规格，性能，功能变化，需要重新培训',
                '3' => '使用场景，环境，条件等变化，需要重新培训',
                '4' => '其它原因',
            ),
        ),
        'life_cycle_person' => array(
            'name' 	=> '物料生命周期维护任务指派人',//1：雄迈 2：巨峰 3：集成
            'value' 	=> array(
                '1' => 1760,//雄迈：    俞佳霏1760
                '2' => 1066,//巨峰账套：杨怡1066
                '3' => 1760 //集成电路：俞佳霏1760
            )
        ),
        'marketing_director' => 207, //刘岳新 市场总监
        'materiel_price_notice' => 859, //许明伦 市场经理

        'relate_task_name' => array(
            'name' 	=> '批量试生产阶段关联任务',
            'value' => array(
                '1' => '产品清库确定',
                '2' => '产品生产停工确定',
                '3' => '产品生产返工确定',
                '4' => '产品团队活动确定',
                '5' => '产品出差支持确定',
                '6' => '产品相关认证确定',
                '7' => '产品规格更新',
                '8' => 'BOM更新',
                '9' => '固件程序更新',
                '10' => '配套程序更新',
                '11' => '产测软件和工装更新',
                '12' => '产品检验标准更新',
                '13' => '产品售前售后服务标准更新',
                '14' => '产品说明书更新',
                '15' => '产品出货特批',
                '16' => '产品采购特批',
                '17' => '产品生产特批',
                '18' => '产品对应物料采购特批',
                '19' => '产品项目积分奖励考核',
                '20' => '销售型号关联默认小版本更新',
                '21' => '销售策略',
                '22' => '管控物料数据变更',
                '23' => '减少产品库存',
            )
        )
    ),
    'access' => array(
        'functional_status' => array(
            'name' 	=> '功能模块状态',
            'value' => array(
                '1' => '正常',
                '0' => '禁用'
            )
        ),
        'type' => array(
            'name' 	=> '视图类型',
            'value' => array(
                '1' => array(
                    'name' => '任务',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我提出的任务及其子任务',
                                '2' => '指派给我的任务及其子任务',
                                '3' => '我完成的',
                                '4' => '我关闭的',
                                '5' => '我取消的',
                                '6' => '我负责评审的任务及其子任务',
                                '7' => '我已参与评审的',
                                '8' => '抄送给我的任务及其子任务',
                                '9' => '邀请我参与评审的',
                                '10' => '允许我查看的上级任务',
                            ),
                        ),
                    ),
                ),
                '2' => array(
                    'name' => '研发需求',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我创建的',
                                '2' => '指派给我的',
                                '3' => '我关闭的',
                                '4' => '我提的原始需求相关的需求',
                                '5' => '我评审的',
                                '6' => '抄送给我的'
                            ),
                        ),
                    ),
                ),
                '3' => array(
                    'name' => '研发任务',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我创建的的任务',
                                '2' => '我完成的任务',
                                '3' => '指派给我的任务',
                                '4' => '我取消的任务',
                                '5' => '我关闭的任务',
                                '6' => '我提的原始需求相关的任务',
                                '7' => '我提的需求或BUG相关的任务',
                                '8' => '抄送给我的任务',
                                '9' => '抄送给我的需求或BUG相关的任务',
                                '10' => '我评审的需求或BUG相关的任务',
                            ),
                        ),
                    ),
                ),
                '10' => array(
                    'name' => '研发BUG',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我创建的Bug',
                                '2' => '指派给我的Bug',
                                '3' => '我关闭的Bug',
                                '4' => '我提的原始需求相关的Bug',
                                '5' => '我提的需求相关的Bug',
                                '6' => '我评审的Bug',
                                '7' => '抄送给我的的Bug',
                            ),
                        ),
                    ),
                ),
                '4' => array(
                    'name' => '原始需求',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我创建的的任务',
                                '2' => '指派给我原始需求',
                                '3' => '我审核的原始需求'
                            ),
                        ),
                    ),
                ),
                '5' => array(
                    'name' => '产品线',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '2' => array('name' => '固定数据'),
                        '4' => array('name' => '所属事业部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我负责的产品线',
                                '2' => '我负责事业部的产品线',
                            ),
                        ),
                    ),
                ),
                '6' => array(
                    'name' => '客户',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '2' => array('name' => '所属账套'),
                        '4' => array('name' => '所属事业部'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我负责的客户 ',
                                '2' => '我负责跟单的客户 ',
                            ),
                        ),
                    ),
                ),
                '7' => array(
                    'name' => '供应商',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '2' => array('name' => '所属账套'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我负责的供应商',
                            )
                        ),
                    ),
                ),
                '8' => array(
                    'name' => '用户',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '2' => array('name' => '所属部门'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我所有下属',
                            ),
                        ),
                    ),
                ),
                '9' => array(
                    'name' => '物料',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '2' => array('name' => '所属账套'),
                        '4' => array('name' => '所属事业部'),
                        '5' => array('name' => '所属产品线'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我负责产品线的物料',
                                '2' => '我负责事业部的物料',
                            ),
                        ),
                    ),
                ),
                '11' => array(
                    'name' => '角色',
                    'value' => array(
                        '1' => array('name' => '全部'),
                        '2' => array('name' => '固定数据'),
                        '3' => array(
                            'name' => '与我有关的',
                            'value' => array(
                                '1' => '我维护的角色',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'access_role' =>array(
            'allow_apply' => array(
                'name' => '是否可申请',
                'value' => array(
                    '1' => '是',
                    '0' => '否'
                )
            ),
            'status' => array(
                'name' => '状态',
                'value' => array(
                    '1' => '正常',
                    '0' => '禁用'
                )
            ),
            'role_type' => array(
                'name' => '角色类型',
                'value' => array(
                    '1' => '岗位角色',
                    '2' => '职能角色',
                    '3' => '业务角色',
                    '4' => '专业角色',
                    '5' => '社团角色',
                    '6' => '其他角色'
                ),
            ),
        ),
    ),
    //视图权限维护列表
    'View' => array(
        'company' => array(//所属公司
            '1' => '雄迈',
            '2' => '巨峰',
            '3' => '集成电路',
            '4' => '云阔',
        )
    ),
    'Inventory' => array(
        'place' => array(
            'name' => '库存地点',
            'value' => array(
                '1' => '杭州',
                '2' => '东莞',
                '3' => '深圳'
            )
        ),
        'type' => array(
            'name' => '库存类型',
            'value' => array(
                '1' => '即时库存',
                '2' => '在途调货',
                '3' => '产线库存',
                '4' => '在途采购',
            )
        ),
    ),
);