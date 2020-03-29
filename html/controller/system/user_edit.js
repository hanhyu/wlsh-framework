new Vue({
    el: ".all",
    data: {
        //用户id
        uid: '',
        //状态
        status: '',
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
        this.uid = getUrlParam('id');
        this.status = getUrlParam('status');
        this.remark = getUrlParam('remark');
    },
    mounted: function () {
        this.axios = axios;
        layui.use('form', function () {
            let form = layui.form;
            form.render();
        })
    },
    methods: {
        //修改用户信息
        editUser() {
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'put',
                url: 'system/user/editUser',
                params: {
                    id: this.uid
                },
                data: {
                    status: $("#status").val(),
                    remark: this.remark
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            layer.alert(response.data.msg, {icon: 6}, function () {
                                //获得frame索引
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
