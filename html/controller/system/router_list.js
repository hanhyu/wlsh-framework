new Vue({
    el: ".all",
    data: {
        //路由列表信息
        menu: [],
        //默认显示第一条数据
        count: 1,
        //当前页数
        curr_page: 1,
        //每页显示的条数
        page_size: 10,
        //防止多次点击
        dis: false
    },
    beforeCreate: function () {
        checkLogin();
    },
    created: function () {
        axiosConfig();
    },
    beforeMount: function () {
    },
    mounted: function () {
        this.axios = axios;
    },
    methods: {
        //路由列表
        getList() {
            let self = this;
            self.dis = 'disabled';
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'get',
                url: 'system/router/get_list',
                params: {
                    curr_page: self.curr_page,
                    page_size: self.page_size
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            self.menu = response.data.data.list;
                            self.count = response.data.data.count;
                            layer.msg('查询数据完成', {icon: 6, time: 1000}, function () {
                                self.dis = false;
                            });
                            break;
                        case 300:
                            top.location.href = 'login.html';
                            break;
                        default:
                            layer.msg(response.data.data, function () {
                                self.dis = false;
                            });
                    }
                    layer.close(loadIndex);
                })
                .catch(function (error) {
                    layer.close(loadIndex);
                    self.dis = false;
                    console.log(error);
                });
        },
        //删除菜单信息
        delRouter(id, url) {
            let self = this;
            layer.confirm(`确认要删除吗？【<b style="color: red;">${url}</b>】删除后不可恢复`, function (index) {
                layer.close(index);
                let loadIndex = layer.load(2, {time: 30 * 1000});
                self.axios({
                    method: 'delete',
                    url: 'system/router/del_router',
                    params: {
                        id: id
                    }
                })
                    .then(function (response) {
                        switch (response.data.code) {
                            case 200:
                                self.getList();
                                layer.msg(`【${response.data.data.id}】已删除!`, {icon: 1, time: 1000});
                                break;
                            case 300:
                                location.href = 'login.html';
                                break;
                            default:
                                layer.msg(response.data.data);
                        }
                        layer.close(loadIndex);
                    })
                    .catch(function (error) {
                        layer.close(loadIndex);
                        console.log(error);
                    });
            });
        },
        //修改路由信息
        editRouter(item) {
            let content = encodeURI(JSON.stringify(item));
            x_admin_show('编辑', "router_edit.html?content=" + content, 700, 500);
        }
    },
    watch: {
        count: function (value) {
            let self = this;
            layui.use('laypage', function () {
                let laypage = layui.laypage;
                //执行一个laypage实例
                laypage.render({
                    elem: 'list_page', //注意，这里的 list_page 是 ID，不用加 # 号
                    count: value, //数据总数，从服务端得到
                    limit: self.page_size, //每页显示的条数
                    layout: ['count', 'prev', 'page', 'next', 'refresh', 'skip'],
                    jump: function (obj, first) {
                        if (!first) {
                            self.curr_page = obj.curr;
                            self.getList();
                        }
                    }
                });
            });
        }
    },
    updated: function () {
    },
    filters: {
        authFilter: function (value) {
            let res;
            switch (value) {
                case '1':
                    res = '需认证';
                    break;
                case '0':
                    res = '无需认证';
                    break;
                default:
                    res = '非法操作';
            }
            return res;
        },
        typeFilter: function (value) {
            let res;
            switch (value) {
                case '1':
                    res = '前台';
                    break;
                case '0':
                    res = '后台';
                    break;
                default:
                    res = '非法操作';
            }
            return res;
        },
    }
});