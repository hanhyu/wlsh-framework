new Vue({
    el: ".all",
    data: {
        //列表信息
        privmessage: [],
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
        layui.use('laydate', function () {
            let laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#log_time', //指定元素
            });
        });

        this.axios = axios;
    },
    methods: {
        //列表
        mongoList() {
            let self = this;
            self.dis = 'disabled';
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'get',
                url: 'system/log_mongo/get_mongo_list',
                params: {
                    curr_page: self.curr_page,
                    page_size: self.page_size,
                    log_time: $("#log_time").val()
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            self.privmessage = response.data.data.list;
                            self.count = response.data.data.count;
                            layer.msg('查询数据完成', {icon: 6, time: 1000}, function () {
                                self.dis = false;
                            });
                            break;
                        case 300:
                            location.href = 'login.html';
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
        //显示详细信息
        getMongo(item) {
            let info = encodeURI(JSON.stringify(item));
            x_admin_show('详细信息', "mongo_info.html?info=" + info, 800, 700);
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
                            self.mongoList();
                        }
                    }
                });
            });
        }
    },
    updated: function () {
    },
    filters: {
        timestampFilter: function (value) {
            return formatDate(value);
        }
    }
});