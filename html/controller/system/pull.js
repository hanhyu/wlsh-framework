new Vue({
    el: ".all",
    data: {
        //列表信息
        content: '',
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
        //更新服务器Pull
        getPull() {
            let self = this;
            if (this.pwd === '') {
                layer.msg('查询密码不能为空!', {icon: 5, time: 1000});
                return;
            }

            let hostName = window.location.hostname;
            layer.confirm(`确认要更新吗？`, function (index) {
                layer.close(index);
                let loadIndex = layer.load(2, {time: 30 * 1000});
                self.axios({
                    method: 'post',
                    url: 'system/user/pull',
                    data: {
                        host: hostName,
                        pwd: self.pwd
                    }
                })
                    .then(function (response) {
                        switch (response.data.code) {
                            case 200:
                                self.pwd = '';
                                self.content = response.data.data.content;
                                layer.msg('查询数据完成', {icon: 6, time: 1000});
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
        }
    },
    watch: {},
    updated: function () {
    },
    filters: {}
});