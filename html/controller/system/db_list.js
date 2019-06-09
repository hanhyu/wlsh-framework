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
        dis: false,
        pwd: ''
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
        //列表
        backupList() {
            let self = this;
            self.dis = 'disabled';
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'get',
                url: 'system/backup/get_list'
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            self.privmessage = response.data.data;
                            self.count = response.data.data.count;
                            layer.msg('查询数据完成', {icon: 6, time: 1000}, function () {
                                self.dis = false;
                            });
                            break;
                        case 300:
                            location.href = 'login.html';
                            break;
                        default:
                            layer.msg(response.data.msg, function () {
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
        //备份数据
        backupDB() {
            let self = this;
            if (this.pwd === '') {
                layer.msg('备份密码不能为空!', {icon: 5, time: 1000});
                return;
            }
            layer.confirm('确认要备份数据吗？', function (index) {
                layer.close(index);
                let loadIndex = layer.load(2, {time: 30 * 1000});
                self.axios({
                    method: 'post',
                    url: 'system/backup/add',
                    data: {
                        pwd: self.pwd
                    }
                })
                    .then(function (response) {
                        if (response.data.code === 400 || response.data.code === 500) {
                            layer.msg(response.data.msg);
                        }
                        if (response.data.code === 200) {
                            self.pwd = '';
                            self.backupList();
                        }
                        if (response.data.code === 300) {
                            location.href = 'login.html';
                        }
                        layer.close(loadIndex);
                    })
                    .catch(function (error) {
                        layer.close(loadIndex);
                        console.log(error);
                    });
            });
        },
        //下载数据
        downDB(id) {
            let self = this;
            if (id === '') {
                layer.msg('非法操作!', {icon: 5, time: 1000});
                return;
            }
            layer.confirm('确认要下载数据吗？', function (index) {
                layer.close(index);
                let loadIndex = layer.load(2, {time: 30 * 1000});
                self.axios({
                    method: 'post',
                    url: 'system/backup/down',
                    data: {
                        id: id
                    }
                })
                    .then(function (response) {
                        if (response.data.code === 400 || response.data.code === 500) {
                            layer.msg(response.data.msg);
                        }
                        if (response.data.code === 200) {
                            let size = response.data.data.file_size;
                            let md5 = response.data.data.file_md5;
                            layer.alert(`下载文件的大小为：${size},<br /> 文件的md5为:${md5},<br />【<b style="color: red;">如须校验文件的完整性请先保存以上信息后再点击“确定”开始下载文件</b>】`,
                                {icon: 0}, function (alertIndex) {
                                    window.open(response.data.data.filename);
                                    layer.close(alertIndex);
                                });
                        }
                        if (response.data.code === 300) {
                            location.href = 'login.html';
                        }
                        layer.close(loadIndex);
                    })
                    .catch(function (error) {
                        layer.close(loadIndex);
                        console.log(error);
                    });
            });
        },
        delDB(id, filename) {
            let self = this;
            layer.confirm('删除后不可恢复，确认要删除吗？', function (index) {
                layer.close(index);
                let loadIndex = layer.load(2, {time: 30 * 1000});
                self.axios({
                    method: 'delete',
                    url: 'system/backup/del',
                    params: {
                        id: id,
                        filename: filename
                    }
                })
                    .then(function (response) {
                        console.log(response.data);
                        if (response.data.code === 400 || response.data.code === 500) {
                            layer.msg(response.data.msg);
                        }
                        if (response.data.code === 200) {
                            layer.msg('数据已删除成功!', {icon: 1, time: 2000}, function () {
                                self.backupList();
                            });
                        }
                        if (response.data.code === 300) {
                            location.href = 'login.html';
                        }
                        layer.close(loadIndex);
                    })
                    .catch(function (error) {
                        layer.close(loadIndex);
                        console.log(error);
                    });
            });
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
        statusFilter: function (value) {
            let res;
            switch (value) {
                case '0':
                    res = '禁用';
                    break;
                case '1':
                    res = '启用';
                    break;
                default:
                    res = '非法操作';
            }
            return res;
        },
    }
});