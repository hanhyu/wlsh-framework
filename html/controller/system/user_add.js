new Vue({
    el: ".all",
    data: {
        //用户列表信息
        privmessage: [],
        //用户名
        name: '',
        //密码
        pwd: '',
        //重复的密码
        repwd: '',
        //备注
        remark: ''
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
        //添加用户
        setUser() {
            let name = this.name;
            let pwd = this.pwd;
            let repwd = this.repwd;

            if (name.length < 6) {
                layer.msg('用户名长度不够!', {icon: 5, time: 1000});
                return;
            }
            if (pwd == null || pwd == '') {
                layer.msg('密码不能为空!', {icon: 5, time: 1000});
                return;
            }
            if (pwd !== repwd) {
                layer.msg('两次密码不一样!', {icon: 5, time: 1000});
                return;
            }

            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'post',
                url: 'system/user/set_user',
                data: {
                    name: this.name,
                    pwd: this.pwd,
                    remark: this.remark
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