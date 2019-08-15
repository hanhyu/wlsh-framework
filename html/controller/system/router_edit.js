new Vue({
    el: ".all",
    data: {
        id: '',
        name: '',
        url: '',
        auth: '',
        method: '',
        action: '*',
        type: 0,
        comment: '',
        menu: [],
        menu_id: '',
        show: false,
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
        this.axios = axios;
        let list = JSON.parse(getUrlParam('content'));
        this.id = list.id;
        this.name = list.name;
        this.url = list.url;
        this.auth = list.auth;
        this.method = list.method;
        this.action = list.action;
        this.type = list.type;
        this.menu_id = list.menu_id;
        this.comment = list.comment;
        if (this.type == 0) this.show = true;
        let self = this;
        $(function () {
            self.getMenu();
        });
    },
    methods: {
        //修改路由
        editRouter() {
            let name = this.name;
            let url = this.url;
            let auth = this.$refs.auth.value;
            let method = this.$refs.method.value;
            let action = this.action;
            let type = this.$refs.type.value;
            let menu_id = this.$refs.menu_id.value;
            let comment = this.comment;

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
                method: 'put',
                url: 'system/router/edit_router',
                params: {
                    id: this.id
                },
                data: {
                    name: name,
                    url: url,
                    auth: auth,
                    method: method,
                    action: action,
                    type: type,
                    menu_id: menu_id,
                    comment: comment
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
                    layer.close(loadIndex)
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
                url: 'system/menu/get_menu_info'
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
    watch: {
        name: function (value) {
            if (value.length < 3) {
                this.name_check = '不能少于3个字符';
            } else {
                this.name_check = '✓';
            }
        }
    },
    updated: function () {
    },
    filters: {}
});