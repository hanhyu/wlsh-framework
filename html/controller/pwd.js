new Vue({
    el: ".all",
    data: {
        old_pwd: '',
        new_pwd: ''
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
        //修改用户密码
        edit() {
            let self = this;
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'post',
                url: 'system/user/edit_pwd',
                data: {
                    old_pwd: self.old_pwd,
                    new_pwd: self.new_pwd,
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            layer.msg(response.data.msg, {icon: 6, time: 2 * 1000}, function () {
                                x_admin_close();
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