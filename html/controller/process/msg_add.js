new Vue({
    el: ".all",
    data: {
        //内容
        content: ''
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
        //添加提示码内容
        setMsg() {
            let content = this.content;

            if (content == null || content == '') {
                layer.msg('内容不能为空!', {icon: 5, time: 1000});
                return;
            }

            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'post',
                url: 'system/process/setMsg',
                data: {
                    content: this.content
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            layer.alert(response.data.msg, {icon: 6}, function () {
                                x_admin_close();
                                //刷新用户列表信息
                                parent.$("#search").click();
                            });
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
        }
    },
    watch: {},
    updated: function () {
    },
    filters: {}
});
