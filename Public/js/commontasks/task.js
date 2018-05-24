$(function() {
    //翻页函数搜索任务列表
    $.turn_page($('#formCondition').attr('action'), 'formContent', 'formCondition');
    $('#formCondition .search_btn').on('click', function() {
        $('#formCondition').find('input[name="quickSelectStatus"]').val('');
        $('#quickSelectBtnContent').find('li a').removeClass('on');
        $.turn_page($('#formCondition').attr('action'), 'formContent', 'formCondition');
    })

    $('#formCondition .select_contrainer').chosen({
        width: "180px",
        allow_single_deselect: true,
        disable_search_threshold: 10,
        no_results_text: "没有匹配结果",
        search_contains: true
    });

    $('#quickSelectBtnContent').find('li a').on('click', function() {
        var id = $(this).data('id');

        var search_type = $('#search_option').val();
        $('#search_val_'+search_type).children().val('');
        $('#search_val_'+search_type).find('.select_contrainer').trigger("chosen:updated");
        if($('#formCondition .chosen_search .reset_search_input').length >= 1) {
            $('#formCondition .chosen_search .reset_search_input').click();
        }

        $('#formCondition').find('input[name="quickSelectStatus"]').val(id);
        $('#quickSelectBtnContent').find('li a').removeClass('on');
        $(this).addClass('on');
        $.turn_page($('#formCondition').attr('action'), 'formContent', 'formCondition');
    })

    $('#formCondition .search_type_box').show_custom_select().change(function() {
        var id = $(this).val();
        $('#formCondition .search_content_box').hide();
        $('#search_val_'+id).show();
        $('#formCondition .search_val').val('');
        $('#search_val_'+id).find('.select_contrainer').trigger("chosen:updated");
        if($('#formCondition .chosen_search .reset_search_input').length >= 1) {
            $('#formCondition .chosen_search .reset_search_input').click();
        }
    });

    $('#formCondition .date_select').on('click', function() {
        WdatePicker();
    })

    $(document).on('click', '#formContent .sort_icon', function() {
        var sort_order = $(this).hasClass('drop_sort') ? 'ASC' : 'DESC';
        $('#formCondition').find('input[name="sort_field"]').val($(this).data('field'));
        $('#formCondition').find('input[name="sort_order"]').val(sort_order);
        $.turn_page($('#formCondition').attr('action'), 'formContent', 'formCondition');
    })


    $(document).on('click', '#confirmPauseBox .btn_submit', function() {
        obj = $(this);
        $.ajax({
            type: 'post',
            url: obj.data('url'),
            beforeSend: function() {
                obj.prev('.btn_loading').remove();
                obj.hide();
                obj.before('<a class="btn btn btn_loading"></a>');
            },
            success: function(data) {
                var dg = dialog({
                    id: 'pauseTaskFormDialog',
                    title: $('.ui-dialog-title').text(),
                    content: data.content,
                    onshow: function () {
                        $('.ui-dialog input[type="text"]').each(function () {
                            if ($(this).attr('chosen_search') && $(this).attr('chosen_search') == '1') {
                                var multi = $(this).data('multi') && $(this).data('multi') == '1' ? true : false;
                                var advanced = $(this).data('advanced') ? $(this).data('advanced') : false;
                                $(this).chosen_search({url: $(this).data('url'), multi: multi, advanced: advanced, initShow: $(this).data('init'), formData: $(this).data('formdata')});
                            }
                        })
                    }
                });
                dg.showModal();
                //$('.ui-dialog-header').show();
                dialog.get('pauseTaskDialog').remove();
            },
            dataType: 'json'
        })
    })

    $('#formContent').initCheckAll('delId', 'chkall', '1');

    $(document).on('click', '#batchDelBtn', function() {
        var delData = '', self = $(this);
        $('#formContent').find('input[name="delId[]"]').each(function() {
            if($(this).prop('checked')) {
                if(delData) {
                    delData += '&';
                }
                delData += 'delId[]='+$(this).val();
            }
        })
        if(!delData) {
            return false;
        }

        $.dialogMsg.confirm('删除后无法恢复，确认要删除您选择的数据？', function() {
            $.ajax({
                type: 'post',
                url: self.data('url'),
                data: delData,
                success: function(data) {
                    if(!data) {
                        $.dialogMsg.alert('操作失败，请刷新后再试一次~');
                        return false;
                    } else if(data.error) {
                        $.dialogMsg.alert(data.msg);
                        return false;
                    }

                    $.trunTocurrenPage('formContent', 'formCondition');
                },
                error: function() {
                    $.dialogMsg.alert('操作失败，请刷新后再试一次~');
                },
                dataType: 'json'
            })
        })
    })

})