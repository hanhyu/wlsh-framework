new Vue({
    el: ".all",
    data: {
        //列表信息
        info: [],
        dirName: ''
    },
    beforeCreate: function () {
        checkLogin();
    },
    created: function () {
        axiosConfig();
    },
    beforeMount: function () {
        layui.use('laydate', function () {
            let laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#startTime', //指定元素
            });
        });
    },
    mounted: function () {
        this.axios = axios;
    },
    methods: {
        //swoole日志
        logList(dirName) {
            let self = this;
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'get',
                url: 'system/log_swoole/get_info',
                params: {
                    name: dirName
                }
            })
                .then(function (response) {
                    if (response.data.code === 400 || response.data.code === 500) {
                        layer.msg(response.data.data);
                    }
                    if (response.data.code === 200) {
                        self.dirName = dirName;
                        self.info = response.data.data.content;
                        layer.msg('查询数据完成', {icon: 6, time: 1000});
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
        },
        cleanLog(dirName) {
            let self = this;
            if (!dirName) {
                layer.msg('不能清空空文件', {icon: 5, time: 1000});
                return;
            }
            layer.confirm(`确认要删除吗？清空数据后不可恢复`, function (index) {
                layer.close(index);
                let loadIndex = layer.load(2, {time: 10 * 1000});
                self.axios({
                    method: 'post',
                    url: 'system/log_swoole/clean_log',
                    data: {
                        name: dirName
                    }
                })
                    .then(function (response) {
                        console.log(response.data);
                        if (response.data.code === 400 || response.data.code === 500) {
                            layer.msg(response.data.data);
                        }
                        if (response.data.code === 200) {
                            self.logList('slow.log');
                            layer.msg(`数据已清空!`, {icon: 1, time: 1000});
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
        //查询monolog日志
        sreachMonolog() {
            let self = this;
            let startTime = $("#startTime").val();

            if (startTime === '') {
                layer.msg('查询日期不能为空!', {icon: 5, time: 1000});
                return;
            }
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'get',
                url: 'system/log_swoole/get_monolog',
                params: {
                    name: startTime + '.log'
                }
            })
                .then(function (response) {
                    if (response.data.code === 400 || response.data.code === 500) {
                        layer.msg(response.data.data);
                    }
                    if (response.data.code === 200) {
                        self.dirName = startTime + '.log';
                        self.info = response.data.data.content;
                        layer.msg('查询数据完成', {icon: 6, time: 1000});
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
        }
    },
    watch: {},
    updated: function () {
    },
    filters: {}
});