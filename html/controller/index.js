new Vue({
    el: ".all",
    data: {
        'username': '',
        'menu': [],
        'title': ''
    },
    beforeCreate: function () {
        checkLogin();
    },
    created: function () {
        axiosConfig();
    },
    beforeMount: function () {
        let name = window.location.host + '_name';
        this.username = localStorage.getItem(name);
    },
    mounted: function () {
        this.axios = axios;
        window.onbeforeunload = function (e) {
            localStorage.clear();
        };
        let self = this;
        window.onload = function () {
            self.getMenu();
        };
    },
    methods: {
        getMenu() {
            let self = this;
            axios({
                method: 'get',
                url: 'system/menu/getMenuInfo'
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            self.menu = response.data.data.menu;
                            self.title = response.data.data.title;
                            break;
                        case 300:
                            location.href = 'login.html';
                            break;
                        default:
                            layer.msg(response.data.msg);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        logout() {
            let loadIndex = layer.load(2, {time: 30 * 1000});
            axios({
                url: 'system/user/logout',
                method: 'post'
            })
                .then(function (response) {
                    localStorage.clear();
                    switch (response.data.code) {
                        case 200:
                            location.href = 'login.html';
                            break;
                        case 300:
                            location.href = 'login.html';
                            break;
                        default:
                            layer.msg(response.data.msg);
                    }
                    layer.close(loadIndex);
                })
                .catch(function (error) {
                    layer.close(loadIndex);
                    console.log(error);
                });
        },
        pwd() {
            x_admin_show('修改密码', "pwd.html", 600, 400);
        }
    },
    watch: {},
    updated: function () {

        //TODO 当改变DOM结构css参数后，需要同时更新显示。如：循环出来绑定的icon图标。
        /* //触发事件
         let tab = {
             tabAdd: function (title, url, id) {
                 alert(456);
                 //新增一个Tab项
                 element.tabAdd('xbs_tab', {
                     title: title,
                     content: '<iframe tab-id="' + id + '" frameborder="0" src="' + url + '" scrolling="yes" class="x-iframe"></iframe>',
                     id: id
                 })
             },
             tabDelete: function (othis) {
                 //删除指定Tab项
                 element.tabDelete('xbs_tab', '44');
                 othis.addClass('layui-btn-disabled');
             },
             tabChange: function (id) {
                 //切换到指定Tab项
                 element.tabChange('xbs_tab', id); //切换到：用户管理
             }
         };
         $('.left-nav #nav li').click(function (event) {
             alert(123);
             if ($(this).children('.sub-menu').length) {
                 if ($(this).hasClass('open')) {
                     $(this).removeClass('open');
                     $(this).find('.nav_right').html('&#xe697;');
                     $(this).children('.sub-menu').stop().slideUp();
                     $(this).siblings().children('.sub-menu').slideUp();
                 } else {
                     $(this).addClass('open');
                     $(this).children('a').find('.nav_right').html('&#xe6a6;');
                     $(this).children('.sub-menu').stop().slideDown();
                     $(this).siblings().children('.sub-menu').stop().slideUp();
                     $(this).siblings().find('.nav_right').html('&#xe697;');
                     $(this).siblings().removeClass('open');
                 }
             } else {
                 var url = $(this).children('a').attr('_href');
                 var title = $(this).find('cite').html();
                 var index = $('.left-nav #nav li').index($(this));

                 for (var i = 0; i < $('.x-iframe').length; i++) {
                     if ($('.x-iframe').eq(i).attr('tab-id') == index + 1) {
                         tab.tabChange(index + 1);
                         event.stopPropagation();
                         return;
                     }
                 }

                 tab.tabAdd(title, url, index + 1);
                 tab.tabChange(index + 1);
             }
             event.stopPropagation();
         });*/


        //$('.left-nav #nav').on('click', 'li', function(event) {
        $('.left-nav #nav li').click(function (event) {
            if ($(this).parent().attr('id') == 'nav') {
                xadmin.set_cate_data({key: 'f1', value: $('.left-nav #nav li').index($(this))})
                xadmin.set_cate_data({key: 'f2', value: null})
                xadmin.set_cate_data({key: 'f3', value: null})
            }

            if ($(this).parent().parent().parent().attr('id') == 'nav') {
                xadmin.set_cate_data({key: 'f2', value: $('.left-nav #nav li').index($(this))})
                xadmin.set_cate_data({key: 'f3', value: null})
            }

            if ($(this).parent().parent().parent().parent().parent().attr('id') == 'nav') {
                xadmin.set_cate_data({key: 'f3', value: $('.left-nav #nav li').index($(this))})
            }


            if ($('.left-nav').css('width') == '60px') {
                $('.left-nav').animate({width: '220px'}, 100);
                $('.page-content').animate({left: '220px'}, 100);
                $('.left-nav i').css('font-size', '14px');
                $('.left-nav cite,.left-nav .nav_right').show();
            }

            if ($(window).width() < 768) {
                $('.page-content-bg').show();
            }


            //$('.left-nav').find('a').removeClass('active');
            //$(this).children('a').addClass('active');
            if ($(this).children('.sub-menu').length) {
                if ($(this).hasClass('open')) {
                    $(this).removeClass('open');
                    $(this).find('.nav_right').html('&#xe697;');
                    $(this).children('.sub-menu').stop(true, true).slideUp();
                    $(this).siblings().children('.sub-menu').slideUp();
                } else {
                    $(this).addClass('open');
                    $(this).children('a').find('.nav_right').html('&#xe6a6;');
                    $(this).children('.sub-menu').stop(true, true).slideDown();
                    $(this).siblings().children('.sub-menu').stop(true, true).slideUp();
                    $(this).siblings().find('.nav_right').html('&#xe697;');
                    $(this).siblings().removeClass('open');
                }
            } else {
                var url = $(this).children('a').attr('_href');
                var title = $(this).find('cite').html();

                xadmin.add_tab(title, url);
            }
            event.stopPropagation();
        });


    },
    filters: {}
});
