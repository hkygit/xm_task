(function ($) {
    $.dialogMsg = {
        //消息提示
        alert: function (msg, url, timer) {
            var timer = typeof(timer) !== 'number' ? 5000 : timer;

            var d = dialog({
                id: 'alertDialog',
                width: 200,
                title: '提示',
                skin: 'min-dialog tips',
                content: msg,
                ok: function () {
                    if (url) {
                        window.location.href = url;
                    }
                },
                okValue: '确认'
            });
            d.show();

			if(timer > 0) {
				setTimeout(function () {
					d.close().remove();
					if (url) {
						window.location.href = url;
					}
				}, timer);
			}
        },
        confirm: function (msg, okFunc, cancelFunc, okValueTxt, cancelValueTxt, dialogID, width) {
            dialog({
                id: dialogID || 'confirmDialog',
                width: width || 250,
                title: '提示',
                skin: 'min-dialog tips',
                content: msg,
				button: [
					{
						value: okValueTxt ? okValueTxt : '确认',
						callback: function () {
							$.isFunction(okFunc) && okFunc();
						},
						autofocus: true
					},
					{
						value: cancelValueTxt ? cancelValueTxt : '取消',
						callback: function () {
							$.isFunction(cancelFunc) && cancelFunc();
						}
					}
				]
            }).show();
        }
    };

    //翻页函数
    $.turn_page = function (url, ajaxdiv, ajaxform, func, dataType) {
        if (typeof(ajaxform) === "undefined") {
            var data = "{}";
        } else {
            var data = $("#" + ajaxform).serialize();
        }

		if(typeof(dataType) === 'undefined') {
			dataType = 'html';
		}
		dataType = dataType.toLowerCase();
		if(dataType != 'html' && dataType != 'json') {
			 dataType = 'html';
		}

		$.ajax({
			type: "POST",
			url: url,
			data: data,
			beforeSend: function () {
				$("#" + ajaxdiv).html('<p class="data_load">正在加载数据，请稍后</p>');
			},
			success: function (data) {
				if(dataType == 'json') {
					if(data.error) {
						$.dialogMsg.alert(data.msg);
						return false;
					}
					var contentHtml = data.content;
				} else {
					var contentHtml = data;
				}

				$("#" + ajaxdiv).html(contentHtml);
				$('.pageSetPerNumSelect').chosen({
					width: "55px",
					allow_single_deselect: true,
					disable_search_threshold: 10,
					no_results_text: "没有匹配结果",
					search_contains: true
				});

				if(func !== undefined && $.isFunction(func)) {
					func();
				}
				return false;
			},
			dataType: dataType
		});
    };

    //ajax刷新当前页
    //@param contentid 要被刷新的容器ID
    //@param conditionid 搜索条件的表单ID，没有则传入空
    $.trunTocurrenPage = function (contentid, conditionid, func, dataType) {
        var url = $('#currenPageId').val();
        $.turn_page(url, contentid, conditionid, func, dataType);
    };

	$.pageBack = function(url, type) {
		var brownAgent = navigator.userAgent;

		if(/msie/i.test(brownAgent) && brownAgent.match(/msie (\d+\.\d+)/i)[1] < 10) {
			if(history.length > 0 && document.referrer) {
                if (type == 'review') {
                    window.location = url;
                } else {
                    window.location = document.referrer;
                }
			} else if(url) {
				window.location = url;
			} else {
				window.opener = null;
				window.close();
			}
		} else {
			if(window.history.length > 1 && document.referrer) {
                if (type == 'review') {
                    window.location = url;
                } else {
                    window.location = document.referrer;
                }
				// window.history.back();
				// window.location.load(window.location.href);
			} else if(url) {
				window.location = url;
			} else if(brownAgent.indexOf('Firefox') >= 0) {
				window.open("about:blank","_self").close();
			} else {
				window.opener = null;
				window.close();
			}
		}
	};

    //附件相关操作
    $.attachments = {
        init: function (obj) {
			var show_attachments_box = obj.next('.common_attachments'), file_name = obj.attr('name');
			var path = obj.data('path'); path = (typeof path != 'undefined') ? path : '';
			if(isIE9Browser == '1') {
				obj.uploadify({
					'formData': {
						'timestamp': obj.data('time'),
						'token': obj.data('token'),
						'path': path
					},
					'buttonText': '添加文件',
					'swf': './Public/image/img/uploadify.swf',
					'uploader': obj.data('url'),
					'fileTypeExts': '*.*',
					'fileSizeLimit': '500MB',
					'multi': true,
					'onUploadSuccess': function (file, data, response) {
						$.attachments.afterUpload(show_attachments_box, file_name, file, data);
					}
				});
			} else {
				obj.uploadifive({
					'formData': {
						'timestamp': obj.data('time'),
						'token': obj.data('token'),
						'path': path
					},
					'buttonText': '添加文件',
					'uploadScript': obj.data('url'),
					'fileSizeLimit': '500MB',
					'multi': true,
					'onUploadComplete': function(file, data) {
						$.attachments.afterUpload(show_attachments_box, file_name, file, data);
					}
				})
			}
        },
        del: function (obj) {
            $.dialogMsg.confirm('确认删除？', function () {
                obj.parent('div').parent('.page_attachments').remove();
            })
        },
		afterUpload: function(show_attachments_box, file_name, file, data) {
			var json_data = $.parseJSON(data);
			if (json_data.error != 0) {
				if (json_data.msg == '') {
					json_data.msg = '上传失败';
				}

				$.dialogMsg.alert(json_data.msg);
				return false;
			}
			
			//判断是否是上传产品图片
			var isUploadProPic = show_attachments_box.hasClass('salesProPic');
			if(!isUploadProPic) {
				show_attachments_box.append('<div class="page_attachments"><div class="fl work_attachments">' + file.name + '</div><div class="fr work_attachments_title input_box"><input type="hidden" name="' + file_name + '_name[]" value="' + file.name + '" /><input type="text" name="' + file_name + '_title[]" placeholder="请输入附件标题"><input type="hidden" name="' + file_name + '_pathname[]" value="' + json_data.content + '" /><input type="hidden" name="' + file_name + '_ext[]" value="' + json_data.ext + '" /><input type="hidden" name="' + file_name + '_size[]" value="' + file.size + '" /><a class="btn btn_img btn_delete_icon"></a></div></div>');
			} else {
				show_attachments_box.append('<div class="page_attachments"><div class="fl work_attachments">' + file.name + '</div><div class="fr work_attachments_title input_box"><input type="hidden" name="' + file_name + '_name[]" value="' + file.name + '" /><input type="radio" name="defaultPic" value="' + json_data.content + '"><i style="font-size:12px;">默认封面</i>&nbsp;&nbsp;<input type="text" name="' + file_name + '_title[]" placeholder="请输入附件标题"><input type="hidden" name="' + file_name + '_pathname[]" value="' + json_data.content + '" /><input type="hidden" name="' + file_name + '_ext[]" value="' + json_data.ext + '" /><input type="hidden" name="' + file_name + '_size[]" value="' + file.size + '" /><a class="btn btn_img btn_delete_icon"></a></div></div>');
			}
			show_attachments_box.find('.btn_delete_icon').eq(-1).on('click', function () {
				$.attachments.del($(this));
			})
		}
    };

    var checkFieldTips = function (value, type, tips_Obj, msg, extend) {
        switch (type) {
            //不能为空
            case 'mustInput':
                if (value.length <= 0) {
                    tips_Obj.html(msg);
                }
                break;
            //检测两个值是否相等
            case 'mustEqualTo':
                if ($('#' + extend).val().length > 0 && $('#' + extend).val() != value) {
                    tips_Obj.html(msg);
                }
                break;
            //必须email格式
            case 'mustEmail':
                if (value.length > 0 && !/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            //长度必须大于
            case 'lengthMustMoreThan':
                if (value.length > 0 && value.length <= extend) {
                    tips_Obj.html(msg);
                }
                break;
            //长度必须不大于
            case 'lengthNotMoreThan':
                if (value.length > 0 && value.length > extend) {
                    tips_Obj.html(msg);
                }
                break;
            //长度必须小于
            case 'lengthMustLessThan':
                if (value.length > 0 && value.length >= extend) {
                    tips_Obj.html(msg);
                }
                break;
            //长度必须不小于
            case 'lengthNotLessThan':
                if (value.length > 0 && value.length < extend) {
                    tips_Obj.html(msg);
                }
                break;
            //必须为数字
            case 'mustNum':
                if (value.length > 0 && isNaN(value)) {
                    tips_Obj.html(msg);
                }
                break;
            //值必须大于
            case 'mustMoreThan':
                if (value.length > 0 && value <= extend) {
                    tips_Obj.html(msg);
                }
                break;
            //值必须不大于
            case 'notMoreThan':
                if (value.length > 0 && value > extend) {
                    tips_Obj.html(msg);
                }
                break;
            //值必须小于
            case 'mustLessThan':
                if (value.length > 0 && value >= extend) {
                    tips_Obj.html(msg);
                }
                break;
            //值必须不小于
            case 'notLessThan':
                if (value.length > 0 && value < extend) {
                    tips_Obj.html(msg);
                }
                break;
            //值必须为整数
            case 'mustIntNum':
                if (value.length > 0 && (isNaN(value) || value.indexOf(".") != -1)) {
                    tips_Obj.html(msg);
                }
                break;
            //值必须为正数
            case 'mustPositiveNum':
                if (!/^\d+(\.\d+)?$/.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            //正数部分长度必须不小于
            case 'PositiveLengthNotMoreThan':
                if (value.length > 0 && String(parseInt(value)).length > extend) {
                    tips_Obj.html(msg);
                }
                break;
            //必须为url格式
            case 'mustUrl':
                if (!/(http[s]?:\/\/)?[a-zA-Z0-9-]+(\.[a-zA-Z0-9]+)+/.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            //由字母数字和下划线组成
            case 'mustLetter':
                if (!/^\w+$/i.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            //检测手机号码格式
            case 'telephone':
                if (value.length > 0 && !/^0?1((3|8|7)[0-9]|5[0-35-9]|4[579])\d{8}$/.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            //检测固定电话格式
            case 'phone':
                if (value.length > 0 && !/^(\d{3}-\d{8}|\d{4}-\d{7,8})(-\d{1,4})?$/.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            case 'mustQQ':
                if (value.length > 0 && !/^[1-9][0-9]{4,}$/.test(value)) {
                    tips_Obj.html(msg);
                }
                break;
            case 'function':
                if (value.length > 0 && typeof(eval(extend)) == 'function') {
                    eval(extend)();
                    return false;
                }
                break;
			case 'mustIP':
				if (value.length > 0 && !/^(?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))$/.test(value)) {
                    tips_Obj.html(msg);
                }
				break;
        }
    };

    $.blurCheckForm = function (data, live) {
        for (var key in data) {
            if (live) {
                $(document).on('blur', '#' + key, function () {
                    var obj = $('#' + this.id);
                    if (typeof(obj) == 'undefined') {
                        return false;
                    }

                    if ($('#' + data[this.id][0].tips_id)) {
                        $('#' + data[this.id][0].tips_id).html('');
                    }

                    for (var k in data[this.id]) {
                        checkFieldTips(obj.val(), data[this.id][k].type, $('#' + data[this.id][k].tips_id), data[this.id][k].msg, data[this.id][k].extend);
                    }
                });
            } else {
                $('#' + key).on('blur', function () {
                    var obj = $('#' + this.id);
                    if (typeof(obj) == 'undefined') {
                        return false;
                    }

                    if ($('#' + data[this.id][0].tips_id)) {
                        $('#' + data[this.id][0].tips_id).html('');
                    }

                    for (var k in data[this.id]) {
                        checkFieldTips(obj.val(), data[this.id][k].type, $('#' + data[this.id][k].tips_id), data[this.id][k].msg, data[this.id][k].extend);
                    }
                });
            }
        }
    };

    $.fn.extend({
        show_custom_select: function () {
            var obj = $(this);
            var _ul = obj.find('.search_type');
            var search_type_on = obj.find('.search_type_on');
            var select_val_input = search_type_on.find('input[type="hidden"]');

            $(this).find('input[type="text"]').val('');
            $(this).find('select').val('');
            select_val_input.val(_ul.find('li a').eq(0).data('id'));

            search_type_on.on('click', function (e) {
                var _input = $(this).find('span');
                var _arrow = $(this).find('.arrow');

                if (_ul.is(':hidden')) {
                    _ul.fadeIn("fast");
                    _arrow.removeClass('down_arrow').addClass('up_arrow');
                } else {
                    _ul.fadeOut("fast");
                    _arrow.removeClass('up_arrow').addClass('down_arrow');
                }

                e.stopImmediatePropagation();
            });

            _ul.find('li a').on('click', function () {
                if (select_val_input.val() != $(this).data('id')) {
                    search_type_on.find('span').text($(this).text());
                    select_val_input.val($(this).data('id')).trigger('change');
                }
            });

            $(document).on('click', function () {
                _ul.fadeOut("fast");
                obj.find('.search_type_on .arrow').removeClass('up_arrow').addClass('down_arrow');
            });

            return select_val_input;
        },
        show_search_content: function (fn1, fn2) {
            var input_obj = $(this), search_common_list = input_obj.parent().siblings('.search_common_list'), ul = search_common_list.find('ul'), reset_search_input = input_obj.siblings('.reset_search_input');
            reset_search_input.hide();
            input_obj.on('click', function (e) {
                if (!search_common_list.is(':hidden')) {
                    return false;
                }

                search_common_list.fadeIn(200);
                search_common_list.find('.search_text_box').val('').focus();
                search_common_list.find('.search_result_empty').hide();
                ul.html('');
                e.stopImmediatePropagation();
            });

            reset_search_input.on('click', function () {
                input_obj.val('');
                fn2 && fn2();
                reset_search_input.hide();
            });

            search_common_list.find('.search_text_box').on('keyup', function () {
                var btn_obj = $(this), option = btn_obj.val();
                $.ajax({
                    type: 'post',
                    url: btn_obj.data('url'),
                    data: {option: option},
                    beforeSend: function () {
                        ul.html('');
                        search_common_list.find('.search_result_loading').show();
                        search_common_list.find('.search_result_empty').hide();
                    },
                    complete: function () {
                        search_common_list.find('.search_result_loading').hide();
                    },
                    success: function (data) {
                        if (!data) {
                            $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                            return false;
                        } else if (data.error == '1') {
                            search_common_list.find('.search_result_empty').show();
                            return false;
                        }

                        for (i in data.content) {
                            ul.append('<li><a href="javascript:void(0)" data-id="' + data.content[i].id + '">' + data.content[i].name + '</a><li>');
                        }

                        ul.find('li a').on('click', function () {
                            input_obj.val($(this).text());
                            search_common_list.hide();
                            reset_search_input.show();
                            fn1 && fn1($(this).data('id'));
                        })
                    },
                    error: function () {
                        $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                        return false;
                    },
                    dataType: 'json'
                })
            });

            search_common_list.on('click', function (e) {
                e.stopImmediatePropagation();
            });

            $(document).on('click', function () {
                search_common_list.fadeOut(200);
            });
        },
        chosen_search: function (config) {
            var setting = {
                'multi': false,
                'advanced': false,
                'title': '没有找到匹配结果',
                'initShow': '',
                'afterSelect': function () {},
				'afterClear': function() {}
            };
            setting = $.extend(setting, config);

            var obj = $(this), width = obj.width(), content = obj.parent();
            if (obj.next('.chosen_search').length > 0) {
                return false;
            }
            obj.hide();
            if (!setting.initShow) {
                obj.val('');
            }

            if (setting.multi) {
                var search_select_name = obj.attr('name');
                var default_str = '';

                if (setting.initShow) {
                    var defaultVal = obj.val().split(",");
                    var defaultTxt = setting.initShow.split(",");

                    for (var default_i in defaultVal) {
                        default_str += '<li class="search_choice"><i title="' + defaultTxt[default_i] + '">' + defaultTxt[default_i] + '</i><a href="javascript:void(0)" class="btn btn_img btn_search_del"></a><input type="hidden" value="' + defaultVal[default_i] + '" name="' + search_select_name + '[' + defaultVal[default_i] + ']' + '" /></li>'
                    }
                }
            }
            var advancedWidth = 47;
            if (!setting.advanced) {
                advancedWidth = 0;
            }
            //placeholder
            var placeholderText = obj.data('placeholder') || '';

            obj.after('<div class="search_common_box chosen_search"><div class="search_input_box"></div><div class="search_common_list hide"><div class="search_text_contrainer input_box commen_input"><input type="text" class="fl search_text_box" placeholder="请输入查询关键字" style="width: ' + (width - 13 - advancedWidth) + 'px;" /><a class="fr btn">高级</a></div><ul class="chosen_search_res_ul"></ul><div class="hide search_result_empty">' + setting.title + '</div><div class="hide search_result_loading"></div></div></div>');
            var chosen_search_box = obj.next('.chosen_search');
            var search_show_text = setting.multi ? '<ul class="multiple_label_box select_chosen" style="width:' + width + 'px;">' + default_str + '<li class="search_field"><input type="text" class="search_common_text" placeholder="' + placeholderText + '" /></li></ul>' : '<input type="text" value="' + setting.initShow + '" class="search_common_text" style="width:' + width + 'px;" readOnly="readOnly" unselectable="on" placeholder="' + placeholderText + '"/><a class="btn btn_img reset_search_input' + (setting.initShow ? '' : ' hide') + '"></a>';
            chosen_search_box.find('.search_input_box').append(search_show_text);
            var search_common_list = chosen_search_box.find('.search_common_list');
            var search_text_box = search_common_list.find('.search_text_box');
            var search_text_contrainer = search_common_list.find('.search_text_contrainer');
            var chosen_search_res_ul = search_common_list.find('.chosen_search_res_ul');
            if (setting.advanced) {
                search_text_contrainer.find('.btn').show().on('click', function() {
					var ajaxData = {"multi": (setting.multi ? "1" : "0")};
					if(setting.multi) {
						chosen_search_box.find('.search_input_box .multiple_label_box input[type="hidden"]').each(function() {
							ajaxData["selectedIds["+$(this).val()+"]"] = $(this).val();
						})
					} else {
						if(obj.val().length >= 1) {
							ajaxData["selectedIds[]"] = obj.val();
						}
					}
					if (obj.data('formdata')) {
                        ajaxData = $.extend(ajaxData, obj.data('formdata'));
                    }

					$.ajax({
						type: 'post',
						url: setting.advanced,
						data: ajaxData,
						success: function(data) {
							if(!data) {
								$.dialogMsg.alert('系统错误，请刷新后再试一次~');
								return false;
							}

							var d=dialog({
								title: data.title,
								content: data.content,
								onshow: function () {
									if ($('.ui-dialog-body .select_contrainer')) {
										$('.ui-dialog-body .select_contrainer').chosen({
											allow_single_deselect: true,
											disable_search_threshold: 10,
											no_results_text: "没有匹配结果",
											search_contains: true
										});
									}
									
									var self = this;
									$('.ui-dialog-body .advanced_search .btn_submit').on('click', function() {
										var advancedSearchResult = $('.ui-dialog-body .advanced_search').find('input[name="'+data.name+'"]').val();

										if(setting.multi) {
											var multi_search_select = chosen_search_box.find('.search_input_box ul');
											multi_search_select.find('li').each(function() {
												if(multi_search_select.find('li').size() > 1) {
													$(this).remove();
												}
											})
										} else {
											chosen_search_box.find('.search_common_text').val('');
											obj.val('');
											chosen_search_box.find('.search_input_box .reset_search_input').hide();
										}
										if(advancedSearchResult.length >= 1) {
											var advancedSearchResObj = $.parseJSON(advancedSearchResult);
											$.each(advancedSearchResObj, function(key, name) {
												if (setting.multi) {
													if (chosen_search_box.find('.search_input_box input[name="' + search_select_name + '[' + key + ']"]').length >= 1) {
														return false;
													}
													
													multi_search_select.find('li').eq(-1).before('<li class="search_choice"><i title="' + name + '">' + name + '</i><a href="javascript:void(0)" class="btn btn_img btn_search_del"></a><input type="hidden" value="' + key + '" name="' + search_select_name + '[' + key + ']' + '" /></li>');
													multi_search_select.find('li').eq(-2).find('.btn_search_del').on('click', function (e) {
														$(this).parent('li').remove();
														search_common_list.hide();
														e.stopImmediatePropagation();
													});
												} else {
													chosen_search_box.find('.search_common_text').val(name);
													search_common_list.hide();
													chosen_search_box.find('.search_input_box .reset_search_input').show();
													obj.val(key);
													if (setting.afterSelect && $.isFunction(setting.afterSelect)) {
														setting.afterSelect(key);
													}
												}
											})
										}
										self.remove();
									})
								}
							}).showModal();
						},
						dataType: 'json'
					})
				});
            } else {
                search_text_contrainer.find('.btn').hide();
            }
            if (setting.multi) {
                chosen_search_box.find('.search_input_box ul li .btn_search_del').on('click', function (e) {
                    $(this).parent('li').remove();
                    search_common_list.hide();
                    e.stopImmediatePropagation();
                });

                chosen_search_box.find('.search_input_box ul').on('click', function () {
                    chosen_search_box.find('.search_common_text').focus();
                    return false;
                })
            } else {
                chosen_search_box.find('.search_common_text').on('click', function () {
                    $(this).focus();
                    return false;
                })
            }

            $(document).on('click', function () {
                search_common_list.hide();
            });

            chosen_search_box.on('click', function (e) {
                var this_hid = search_common_list.is(':hidden');
                $('.chosen_search').find('.search_common_list').hide();
                if (!this_hid) {
                    search_common_list.show();
                }
                e.stopImmediatePropagation();
            });

            search_text_box.on('click', function () {
                return false;
            });

            var searchState = false;
            chosen_search_box.find('.search_common_text').on('focus', function (e) {
                var hideStatus = search_common_list.is(':hidden');
                $('.chosen_search .search_common_list').hide();
                search_common_list.show();
                if (hideStatus) {
                    search_text_box.val('');
                    chosen_search_res_ul.html('');
                    search_common_list.find('.search_result_empty').hide();
                }

                search_text_box.focus();
                search_text_box.bind('input propertychange', function () {
                    var thisVal = $(this).val();
                    if (thisVal) {
                        searchState = true;
                    } else {
                        searchState = false;
                    }
                });
                e.stopImmediatePropagation();
            });

            if (setting.multi) {
                obj.prop('disabled', true).hide();
            } else {
                var reset_search_input = chosen_search_box.find('.search_input_box .reset_search_input');
                reset_search_input.on('click', function () {
                    obj.val('');
                    chosen_search_box.find('.search_common_text').val('');
                    reset_search_input.hide();
					if (setting.afterClear && $.isFunction(setting.afterClear)) {
						setting.afterClear();
					}
                })
            }

            var windowH = $(window).height(), scrollH = $(window).scrollTop();

            search_text_box.on('keyup', function (event) {
                var curIndex = search_common_list.find('ul .on').index();
                var curNum = search_common_list.find('ul li').size();
                var textVal = search_text_box.val();
                var liOn = search_common_list.find('ul li').hasClass('on');
                //可输入键，包括字母数字符和退格删除回车空格键，回车需要输入框存在内容且内容列表选项未选择才可触发ajax
                if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 65 && event.keyCode <= 90) || (event.keyCode >= 96 && event.keyCode <= 105) || (event.keyCode >= 186 && event.keyCode <= 192) || (event.keyCode >= 219 && event.keyCode <= 222) || ((event.keyCode == 13 || event.keyCode == 108 || event.keyCode == 16) && textVal && !liOn) || event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 32) {
					var postData = {'option': $(this).val()};
                    if (obj.data('formdata')) {
                        postData = $.extend(obj.data('formdata'), postData);
                    }
                    if (searchState) {
                        $.ajax({
                            type: 'post',
                            url: setting.url,
                            data: postData,
                            beforeSend: function () {
                                search_common_list.find('.search_result_empty').hide();
                            },
                            success: function (data) {
                                chosen_search_res_ul.html('');
                                if (!data) {
                                    $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                                    return false;
                                } else if (data.error == '1') {
                                    search_common_list.find('.search_result_empty').show();
                                    return false;
                                }

                                for (i in data.content) {
									if(data.content[i].type){
										if(data.content[i].type == 1){
											if ($('input[type="hidden"][name="' + search_select_name + '[1_' + data.content[i].id + ']"]').length >= 1) {
												chosen_search_res_ul.append('<li class="user_mark selected">' + data.content[i].name + '</li>');
											} else {
												chosen_search_res_ul.append('<li class="user_mark" data-type="1" data-id="' + data.content[i].id + '">' + data.content[i].name + '</li>');
											}
										}else if(data.content[i].type == 2){
											if ($('input[type="hidden"][name="' + search_select_name + '[2_' + data.content[i].id + ']"]').length >= 1) {
												chosen_search_res_ul.append('<li class="group_mark selected">' + data.content[i].name + '</li>');
											} else {
												chosen_search_res_ul.append('<li class="group_mark" data-type="2" data-id="' + data.content[i].id + '">' + data.content[i].name + '</li>');
											}
										}
									}else{
										if ($('input[type="hidden"][name="' + search_select_name + '[' + data.content[i].id + ']"]').length >= 1) {
											chosen_search_res_ul.append('<li class="selected">' + data.content[i].name + '</li>');
										} else {
											chosen_search_res_ul.append('<li data-id="' + data.content[i].id + '">' + data.content[i].name + '</li>');
										}
									}
                                }

                                var search_common_list_H = search_common_list.height(), search_common_list_offsetH = chosen_search_box.find('.search_common_text').offset().top;
                                var search_common_list_offsetB = windowH - (search_common_list_offsetH - scrollH) - chosen_search_box.find('.search_common_text').height();

                                if (!search_common_list.hasClass('up_position')) {	//当前方向朝下
                                    if (search_common_list_H > search_common_list_offsetB && (search_common_list_offsetH - scrollH) > search_common_list_H) {
                                        search_common_list.addClass('up_position');
                                        search_text_contrainer.removeClass('commen_input').addClass('up_input').before(chosen_search_res_ul);
                                        chosen_search_res_ul.before(search_common_list.find('.search_result_empty'));
                                    }
                                } else {
                                    if ((search_common_list_offsetH - scrollH) < search_common_list_H) {
                                        search_common_list.removeClass('up_position');
                                        search_text_contrainer.removeClass('up_input').addClass('commen_input').after(chosen_search_res_ul);
                                        chosen_search_res_ul.after(search_common_list.find('.search_result_empty'));
                                    }
                                }

                                chosen_search_res_ul.find('li').not('.selected').on('mouseover', function () {
                                    // $(this).addClass('on').siblings().not('.selected').removeAttr("class");
                                    $(this).addClass('on').siblings().not('.selected').removeClass('on');
                                });

                                search_text_box.on('keyup', function (e) {
                                    if (chosen_search_res_ul.find('li').not('.selected').hasClass('on')) {
                                        var _this = chosen_search_res_ul.find('.on');
                                        //enter鍵
                                        if (e.keyCode == 13 || e.keyCode == 108) {
                                            _this.click();
                                            if (setting.multi) {
                                                search_text_box.focus();
                                            }
                                        }
                                    }
                                });

                                chosen_search_res_ul.find('li').not('.selected').on('click', function () {
                                    if (setting.multi) {
										var type = $(this).data('type') ? $(this).data('type') : 0;
                                        if ((type && $('input[name="' + search_select_name + '[' + type + '_' + $(this).data('id') + ']"]').length >= 1) || (!type && $('input[name="' + search_select_name + '[' + $(this).data('id') + ']"]').length >= 1)) {
                                            return false;
                                        }
										
                                        var multi_search_select = chosen_search_box.find('.search_input_box ul');
										if(type){
											 multi_search_select.find('li').eq(-1).before('<li class="search_choice"><i title="' + $(this).text() + '">' + $(this).text() + '</i><a href="javascript:void(0)" class="btn btn_img btn_search_del"></a><input type="hidden" value="' + type + '_' + $(this).data('id') + '" name="' + search_select_name + '[' + type + '_' + $(this).data('id') + ']' + '" /></li>');
										}else{
											 multi_search_select.find('li').eq(-1).before('<li class="search_choice"><i title="' + $(this).text() + '">' + $(this).text() + '</i><a href="javascript:void(0)" class="btn btn_img btn_search_del"></a><input type="hidden" value="' + $(this).data('id') + '" name="' + search_select_name + '[' + $(this).data('id') + ']' + '" /></li>');
										}
                                       
                                        multi_search_select.find('li').eq(-2).find('.btn_search_del').on('click', function (e) {
                                            $(this).parent('li').remove();
                                            search_common_list.hide();
                                            e.stopImmediatePropagation();
                                        });
										
										var replaceClass = type ? (type == 1 ? 'user_mark' : 'group_mark') : '';
                                        $(this).replaceWith('<li class="selected ' + replaceClass + '">' + $(this).text() + '</li>');
                                    } else {
                                        chosen_search_box.find('.search_common_text').val($(this).text());
                                        search_common_list.hide();
                                        reset_search_input.show();
                                        obj.val($(this).data('id'));
                                    }

                                    if (setting.afterSelect && $.isFunction(setting.afterSelect)) {
                                        setting.afterSelect($(this).data('id'));
                                    }
                                })
                            },
                            error: function () {
                                $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                                return false;
                            },
                            dataType: 'json'
                        })
                    }
                } else if (event.keyCode == 40) {
                    //向下方向键
                    if (search_common_list.find('ul li')) {
                        if (curIndex >= 0) {
                            ++curIndex;
                            if (curIndex >= curNum - 1) {
                                curIndex = curNum - 1;
                            }
                        } else {
                            curIndex = search_common_list.find('ul li').not('.selected').first().index();
                        }

                        while (search_common_list.find('ul li').eq(curIndex).hasClass('selected')) {
                            ++curIndex;
                            if (curIndex > curNum - 1) {
                                return false;
                            }
                        }

                        search_common_list.find('ul li').removeClass('on').eq(curIndex).addClass('on');
                    }
                } else if (event.keyCode == 38) {
                    event.preventDefault();
                    event.keyCode = 0;
                    event.returnValue = false;
                    //向上方向键
                    if (search_common_list.find('ul li')) {
                        if (curIndex >= 0) {
                            --curIndex;
                            if (curIndex <= 0) {
                                curIndex = 0;
                            }
                        } else {
                            curIndex = search_common_list.find('ul li').not('.selected').last().index();
                        }

                        while (search_common_list.find('ul li').eq(curIndex).hasClass('selected')) {
                            --curIndex;
                            if (curIndex < 0) {
                                return false;
                            }
                        }

                        search_common_list.find('ul li').removeClass('on').eq(curIndex).addClass('on');
                    }
                }
            })
        },
        chosen_select: function (config) {
            var setting = {
                'multi': false,
                'advanced': false,
                'afterSelect': function () {}
            };
            setting = $.extend(setting, config);

            var obj = $(this);
            obj.on('click',function () {
                var ajaxData = {"multi": (setting.multi ? "1" : "0")};
                if (obj.data('formdata')) {
                    ajaxData = $.extend(ajaxData, obj.data('formdata'));
                }

                $.ajax({
                    type: 'post',
                    url: setting.advanced,
                    data: ajaxData,
                    success: function(data) {
                        if(!data) {
                            $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                            return false;
                        }else if(data.error){
                            $.dialogMsg.alert(data.msg);
                            return false;
                        }

                        var d=dialog({
                            title: data.title,
                            content: data.content,
                            onshow: function () {
                                var self = this;
                                $('.ui-dialog-body .advanced_search .btn_submit').on('click', function() {
                                    var advancedSearchResult = $('.ui-dialog-body .advanced_search').find('input[name="'+data.name+'"]').val();
                                    //前置处理

                                    if(advancedSearchResult.length >= 1) {
                                        var advancedSearchResObj = $.parseJSON(advancedSearchResult);
                                        // var advancedSearchResArray = $.makeArray(advancedSearchResult);
                                        if (setting.afterSelect && $.isFunction(setting.afterSelect)) {
                                            setting.afterSelect(advancedSearchResObj);
                                        }
                                        // $.each(advancedSearchResObj, function(key, name) {
                                        //     if (setting.multi) {
                                        //
                                        //     } else {
                                        //         if (setting.afterSelect && $.isFunction(setting.afterSelect)) {
                                        //             setting.afterSelect(key);
                                        //         }
                                        //     }
                                        // })
                                    }
                                    self.remove();
                                })
                            }
                        }).showModal();
                    },
                    dataType: 'json'
                });
            });
        },
        initKindeditor: function () {
            var K = KindEditor, editor_obj = $(this);
            var options = {
                cssPath: './Public/js/kindeditor/plugins/code/prettify.css',
                width: editor_obj.width() + 'px',
                minWidth: 300,
                items: [ 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|', 'emoticons', 'image', 'code', 'link', '|', 'removeformat', 'undo', 'redo', 'fullscreen', 'source'],
                filterMode: true,
                bodyClass: 'editor_html_content',
                urlType: 'relative',
                autoHeightMode: false,
                resizeType: '1',
                uploadJson: uploadImageUrl,
                allowFileManager: false,
				pasteType: 2,
                afterBlur: function () {
                    this.sync();
                    editor_obj.prev('.ke-container').removeClass('focus');
                },
                afterFocus: function () {
                    editor_obj.prev('.ke-container').addClass('focus');
                },
                afterChange: function () {
                    editor_obj.change().hide();
                },
				afterCreate: function() {
					var doc = this.edit.doc; 
					var cmd = this.edit.cmd; 
					if(!K.WEBKIT && !K.GECKO) {
						var pasted = false;
						$(doc.body).bind('paste', function(ev) {
							pasted = true;
							return true;
						});
						setTimeout(function() {
							$(doc.body).bind('keyup', function(ev) {
								if(pasted) {
									pasted = false;
									return true;
								}
								if(ev.keyCode == 86 && ev.ctrlKey) $.dialogMsg.alert('您的浏览器不支持粘贴图片，请使用编辑器的图片上传功能！');
							})
						}, 10);
					}
					/* Paste in chrome.*/
					/* Code reference from http://www.foliotek.com/devblog/copy-images-from-clipboard-in-javascript/. */
					if(K.WEBKIT) {
						$(doc.body).bind('paste', function(ev) {
							var $this    = $(this);
							var original = ev.originalEvent;
							var file     = original.clipboardData.items[0].getAsFile();
							if(file) {
								var reader = new FileReader();
								reader.onload = function(evt) {
									var result = evt.target.result; 
									var result = evt.target.result;
									var arr    = result.split(",");
									var data   = arr[1]; // raw base64
									var contentType = arr[0].split(";")[0].split(":")[1];

									html = '<img src="' + result + '" alt="" />';

									$.ajax({
										type: 'post',
										url: ajaxPasteImageUrl,
										data: {editor: html},
										success: function(data) {
											if(!data || data.error) {
												$.dialogMsg.alert('粘贴失败，请刷新后再试一次');
												return false;
											}

											cmd.inserthtml(data.content);
										},
										error: function() {
											$.dialogMsg.alert('粘贴失败，请刷新后再试一次');
										},
										dataType: 'json'
									})
								};
								reader.readAsDataURL(file);
							}

							setTimeout(function() {
								var html = K(doc.body).html();
								if(html.search(/<img src="data:.+;base64,/) > -1 /*|| html.search(/<img.*src="file:\/\//) > -1*/) {
									$.ajax({
										type: 'post',
										url: ajaxPasteImageUrl,
										data: {editor: html},
										success: function(data) {
											if(!data || data.error) {
												$.dialogMsg.alert('粘贴失败，请刷新后再试一次');
												return false;
											}

											K(doc.body).html(data.content);
										},
										error: function() {
											$.dialogMsg.alert('粘贴失败，请刷新后再试一次');
										},
										dataType: 'json'
									})
								}
							}, 80);
						});
					}
					/* Paste in firfox and other firfox.*/
					else
					{
						K(doc.body).bind('paste', function(ev) {
							setTimeout(function() {
								var html = K(doc.body).html();
								if(html.search(/<img src="data:.+;base64,/) > -1 || html.search(/<img.*src="file:\/\//) > -1) {
									$.ajax({
										type: 'post',
										url: ajaxPasteImageUrl,
										data: {editor: html},
										success: function(data) {
											if(!data || data.error) {
												$.dialogMsg.alert('粘贴失败，请刷新后再试一次');
												return false;
											}

											K(doc.body).html(data.content);
										},
										error: function() {
											$.dialogMsg.alert('粘贴失败，请刷新后再试一次');
										},
										dataType: 'json'
									})
								}
							}, 80);
						});
					}
					/* End */
				},
                newlineTag: 'br'
            };
            try {
                var keditor = K.create(editor_obj, options);
            }
            catch (e) {
            }

            prettyPrint();
            return keditor;
        },
		initOnlyImgKindeditor: function () {
            var K = KindEditor, editor_obj = $(this);
            var options = {
                cssPath: './Public/js/kindeditor/plugins/code/prettify.css',
                width: editor_obj.width() + 'px',
                minWidth: 300,
                items: ['image', 'fullscreen'],
                filterMode: true,
                bodyClass: 'editor_html_content',
                urlType: 'relative',
                autoHeightMode: false,
                resizeType: '1',
                uploadJson: uploadImageUrl,
                allowFileManager: false,
				pasteType: 1,
                afterBlur: function () {
                    this.sync();
                    editor_obj.prev('.ke-container').removeClass('focus');
                },
                afterFocus: function () {
                    editor_obj.prev('.ke-container').addClass('focus');
                },
                afterChange: function () {
                    editor_obj.change().hide();
                },
                newlineTag: 'br'
            };
            try {
                var keditor = K.create(editor_obj, options);
            }
            catch (e) {
            }

            prettyPrint();
            return keditor;
        },
        initCheckAll: function (prefix, checkall, liveBind) {
            var checkall = checkall ? checkall : 'chkall', self = $(this), liveBind = liveBind ? liveBind : 0;
            var checkAllBtnFunc = function (obj) {
                self.find("input[name='" + prefix + "[]']").prop('checked', obj.prop('checked'));
                self.find('input[type="checkbox"][name="' + checkall + '"]').prop('checked', obj.prop('checked'));
            };
            var checkIsAllCheckedFunc = function () {
                var allNum = $('input[type="checkbox"][name="' + prefix + '[]"]').length, checkNum = $('input[type="checkbox"][name="' + prefix + '[]"]:checked').length;
                if (allNum <= checkNum) {
                    self.find('input[type="checkbox"][name="' + checkall + '"]').prop('checked', true);
                } else {
                    self.find('input[type="checkbox"][name="' + checkall + '"]').prop('checked', false);
                }
            };

            if (liveBind) {
                $(document).on('click', 'input[type="checkbox"][name="' + checkall + '"]', function () {
                    checkAllBtnFunc($(this));
                });
                $(document).on('click', 'input[type="checkbox"][name="' + prefix + '[]"]', function () {
                    checkIsAllCheckedFunc();
                })
            } else {
                self.find('input[type="checkbox"][name="' + checkall + '"]').on('click', function () {
                    checkAllBtnFunc($(this));
                });
                self.find('input[type="checkbox"][name="' + prefix + '[]"]').on('click', function () {
                    checkIsAllCheckedFunc();
                })
            }
        }
    });
})(jQuery);

$(function () {
    //点击分页按钮操作
    $(document).on("click", ".basePageOperation", function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }

        var url = $(this).data('url');
        if (url.indexOf('replacelocationNum') !== -1) {
            var basePageLocationNumObj = $(this).siblings('.basePageLocationNum');
            var basePageLocationNum = basePageLocationNumObj.val();
            basePageLocationNumObj.val('');
            if (!basePageLocationNum) {
                return false;
            }

            basePageLocationNum = parseInt(basePageLocationNum);
            if (!basePageLocationNum) {
                return false;
            }

            url = url.replace('replacelocationNum', basePageLocationNum);
        }

        $.turn_page(url, $(this).data('listid'), $(this).data('formid'), function() {}, $(this).parent().parent().find('#trunpageDataType').val());
    });

	$(document).on('keyup', '.basePageLocationNum', function(e) {
		if(e.keyCode == '13' && $(this).val().length >= 1) {
			$(this).next('.basePageOperation').click();
		}
		return false;
	});

	$(document).on('change', '.pageSetPerNumSelect', function() {
		 var url = $(this).data('url');
		 url = url.replace('perPageNumValue', $(this).val());
		 // $.turn_page(url, $(this).data('listid'), $(this).data('formid'));
		 $.turn_page(url, $(this).data('listid'), $(this).data('formid'), function() {}, $('#'+$(this).data('listid')+' #trunpageDataType').val());
	});

    $(document).on('click', '.onlyBody_click', function () {
        var obj = $(this);
        $.ajax({
            title: obj.attr('title'),
            url: obj.attr('href'),
            beforeSend: function () {
                $('#loading_data_imgIcon').show();
            },
            complete: function () {
                $('#loading_data_imgIcon').hide();
            },
            success: function (data) {
                if (!data) {
                    $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                    return false;
                }else if (data.error == 4) {
                    $.dialogMsg.confirm(data.msg, function() {
                        $.ajax({
                            url: obj.attr('href'),
                            title: obj.attr('title'),
                            data:{"confirmed":1},
                            type: 'post',
                            success: function(data) {
                                if (!data) {
                                    $.dialogMsg.alert('系统错误，请刷新后再试一次~');
                                    return false;
                                } else if (data.error) {
                                    $.dialogMsg.alert(data.msg);
                                    return false;
                                }

                                var d = dialog({
                                    id: obj.data('dialogid'),
                                    title: obj.data('dialogtitle') ? obj.data('dialogtitle') : obj.attr('title'),
                                    content: data.content,
                                    onshow: function () {
                                        if (obj.data('nochosen') != '1' && $('.ui-dialog-body .select_contrainer')) {
                                            $('.ui-dialog-body .select_contrainer').chosen({
                                                allow_single_deselect: true,
                                                disable_search_threshold: 10,
                                                no_results_text: "没有匹配结果",
                                                search_contains: true
                                            });
                                        }
                                        $('.ui-dialog input[type="text"]').each(function () {
                                            if ($(this).attr('chosen_search') && $(this).attr('chosen_search') == '1') {
                                                var multi = $(this).data('multi') && $(this).data('multi') == '1' ? true : false;
                                                var advanced = $(this).data('advanced') ? $(this).data('advanced') : false;
                                                $(this).chosen_search({url: $(this).data('url'), multi: multi, advanced: advanced, initShow: $(this).data('init'), formData: $(this).data('formdata')});
                                            }
                                        });

                                        if ($("input[name='file_upload'][type='file']").length >= 1) {
                                            $("input[name='file_upload'][type='file']").each(function () {
                                                $.attachments.init($(this));
                                            })
                                        }
                                    },
									helpUrl: obj.data('helpurl')
                                }).showModal();

                                if ($('.select_contrainer')) {
                                    $(".select_contrainer").trigger("chosen:updated");
                                }

                                return false;
                            },
                            dataType: 'json'
                        });
                    }, function() {});
                    return false;
                } else if (data.error) {
                    $.dialogMsg.alert(data.msg);
                    return false;
                }

                var d = dialog({
                    id: obj.data('dialogid'),
                    title: obj.data('dialogtitle') ? obj.data('dialogtitle') : obj.attr('title'),
                    content: data.content,
                    onshow: function () {
                        if (obj.data('nochosen') != '1' && $('.ui-dialog-body .select_contrainer')) {
                            $('.ui-dialog-body .select_contrainer').chosen({
                                allow_single_deselect: true,
                                disable_search_threshold: 10,
                                no_results_text: "没有匹配结果",
                                search_contains: true
                            });
                        }
                        $('.ui-dialog input[type="text"]').each(function () {
                            if ($(this).attr('chosen_search') && $(this).attr('chosen_search') == '1') {
                                var multi = $(this).data('multi') && $(this).data('multi') == '1' ? true : false;
								var advanced = $(this).data('advanced') ? $(this).data('advanced') : false;
                                $(this).chosen_search({url: $(this).data('url'), multi: multi, advanced: advanced, initShow: $(this).data('init'), formData: $(this).data('formdata')});
                            }
                        });

                        if ($("input[name='file_upload'][type='file']").length >= 1) {
                            $("input[name='file_upload'][type='file']").each(function () {
                                $.attachments.init($(this));
                            })
                        }
                    },
					helpUrl: obj.data('helpurl')
                }).showModal();

                if ($('.select_contrainer')) {
                    $(".select_contrainer").trigger("chosen:updated");
                }

                return false;
            },
            error: function () {
                $.dialogMsg.alert('系统错误，请刷新后再试一次~');
            },
            dataType: 'json'
        });
        return false;
    });

    $('input[type="text"]').each(function () {
        if ($(this).attr('chosen_search') && $(this).attr('chosen_search') == '1') {
            var multi = $(this).data('multi') && $(this).data('multi') == '1' ? true : false;
            var advanced = $(this).data('advanced') ? $(this).data('advanced') : false;
            $(this).chosen_search({url: $(this).data('url'), multi: multi, advanced: advanced, initShow: $(this).data('init'), formData: $(this).data('formdata')});
        }
    });

    if ($("input[name='file_upload'][type='file']").length >= 1) {
        $("input[name='file_upload'][type='file']").each(function () {
            $.attachments.init($(this));
        })
    }

    input_show();
    content_change_height();
    label_box_action();
});

//三级导航动作
function label_box_action() {
    var _label = $('.content_label').find('.label_content_container');
    _label.on('mouseover',function(){
        $(this).addClass('hover');
        $(this).find('.label_content_box').stop().fadeIn(200);
        $(this).find('.third_label_box').stop().slideDown(400);
    });
    _label.on('mouseout',function(){
        $(this).removeClass('hover');
        $(this).find('.third_label_box').stop().slideUp(200);
        $(this).find('.label_content_box').stop().fadeOut(400);
    });
}

//浏览器检测placeholder是否可用
function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}

//表单背景文字遮罩控制函数
function input_show() {
    $(document).on('focus', '.input_box input', function () {
        $(this).css({"border": "1px solid #1078d7"});
    });
    $(document).on('blur', '.input_box input', function () {
        $(this).css({"border": "1px solid #dfdfdf"});
    });
}

function content_change_height() {
    var _box = $('#task_content .content_box');
    //减的120为头部60高度+内容导航条适应高度+底部padding出来的15高度+content_box边框2高度+content_box底部padding的10高度
    var _l_h = $('#task_content .content_label').height() + 32;
    var _h = $(window).height() - 87 - _l_h;
    _box.css({"min-height": _h});
    $(window).resize(function () {
        var r_h = $(window).height() - 87 - _l_h;
        _box.css({"min-height": r_h});
    });
}

//获取指定日期，以今天为基准，往前负值，往后正值
function fun_date(aa){
    var date1 = new Date();
    var date2 = new Date(date1);
    date2.setDate(date1.getDate()+aa);
    var time2 = date2.getFullYear()+"-"+(date2.getMonth()+1)+"-"+date2.getDate();
    // console.log(time2);
    return time2;
}

function checkEntrustData (formId, viewType, attrName) {
    var attrName = typeof(attrName) !== "undefined" ? attrName : 'action';
    var winUrl = $('#'+formId).attr(attrName);
    var winUrlArr = winUrl.split('.');
    var newUrlStr = winUrlArr[0];
    var filename = '.'+winUrlArr[1];

    var code = winUrl.split('uid');
    if (typeof(code[1]) !== "undefined" && viewType == 1) {
        return false;
    }

    $.ajax({
        type: 'post',
        data: {'getEntrustUser':1},
        url: winUrl,
        success: function(data) {
            $('.wrong_detail').html('');
            if(!data && data!=0) {
                $.dialogMsg.alert('操作失败，请刷新后再试一次~');
                return false;
            } else if(data.error == 1) {
                $.dialogMsg.alert(data.msg, document.referrer);
                return false;
            }

            if (typeof(data.delparam) !== "undefined" && viewType == 1) {
                newUrlStr = newUrlStr.replace(data.delparam,"");
            }

            var user_list = '';
            var uid = '';
            for (var i in data.list) {
                user_list += '<li><a class="btn entrust_click" href="javascript:;" url="'+newUrlStr+'-uid-'+i+filename+'">'+data.list[i]+'</a></li>';
                uid = i;
            }

            if (data.unum > 1) {
                dialog({
                    id: 'confirmDialog',
                    title: '请选择操作人',
                    content: '<div class="dialog_page_box common_win common_task_alert qnaire_add_select">\
                            <div class="content_box">\
                            <ul>\
                            '+user_list+'\
                        </ul>\
                        </div>\
                        </div>',
                    cancel: function() {
                        $.dialogMsg.alert('请选择操作人~');
                        return false;
                    },
                    cancelDisplay : false,
                    onshow: function () {
                        $('.entrust_click').on('click', function() {
                            if (viewType == 1) {//页面
                                window.location.href = $(this).attr('url');
                            } else {//弹框
                                $('#'+formId).attr(attrName, $(this).attr('url'));
                                dialog.get('confirmDialog').remove();
                            }
                        });

                    }
                }).showModal();

            } else {
                if (viewType == 1) {//页面
                    window.location.href = newUrlStr+'-uid-'+uid+filename;
                } else {
                    $('#'+formId).attr(attrName, newUrlStr+'-uid-'+uid+filename);
                }
            }
        },
        error: function(data) {
            $.dialogMsg.alert('操作失败，请刷新后再试一次~');
            return false;
        },
        dataType: 'json'
    })
}