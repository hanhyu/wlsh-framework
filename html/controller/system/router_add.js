new Vue({
    el: ".all",
    data: {
        name: '',
        url: '',
        auth: 1,
        method: '',
        action: '*',
        comment: '',
        show: true,
        menu: [],
        menu_id: '',
        //防止多次点击
        dis: false,
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
        layui.use('form', function () {
            let form = layui.form;
            form.on('select(search_type)', function (data) {
                if (data.value == 1) {
                    $(".rule_show").hide();
                } else {
                    $(".rule_show").show();
                }
            });
        });
        this.axios = axios;
        this.getMenu();
    },
    beforeUpdate: function () {
    },
    updated: function () {
    },
    beforeDestroy: function () {
    },
    destroyed: function () {
    },
    methods: {
        //添加路由
        setRouter() {
            let self = this;
            let name = this.name;
            let url = this.url;
            let auth = this.$refs.auth.value;
            let method = this.$refs.method.value;
            let action = this.action;
            let type = this.$refs.type.value;
            let comment = this.comment;
            let menu_id = this.$refs.menu_id.value;

            if (!name || !url || !auth || !method || !action || !type || !comment) {
                layer.msg('带*号数据段不能为空!', {icon: 5, time: 1000});
                return;
            }

            if (type === '1') {
                menu_id = 0;
            }

            self.dis = 'disabled';
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'post',
                url: 'system/router/setRouter',
                data: {
                    name: name,
                    url: url,
                    auth: auth,
                    method: method,
                    action: action,
                    type: type,
                    menu_id: menu_id,
                    comment: comment,
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            layer.alert(response.data.msg, {icon: 6}, function () {
                                // 获得frame索引
                                var index = parent.layer.getFrameIndex(window.name);
                                //关闭当前frame
                                parent.layer.close(index);
                                //刷新菜单列表信息
                                parent.$("#search").click();
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
        getMenu() {
            let self = this;
            this.axios({
                method: 'get',
                url: 'system/menu/getMenuInfo'
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            response.data.data.menu.forEach(function (value) {
                                if (value['level'] == 1) {
                                    self.menu.push(value);
                                }
                            });
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
    },
    computed: {},
    watch: {},
    updated: function () {
        layui.use('form', function () {
            let form = layui.form;
            form.render();
        })
    },
    filters: {}
});
