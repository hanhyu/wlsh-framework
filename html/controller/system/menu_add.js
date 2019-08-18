new Vue({
    el: ".all",
    data: {
        menu: [],
        //菜单名称
        name: '',
        //菜单图标
        icon: '',
        //菜单链接
        url: '',
        name_check: '',
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
        //显示一级菜单列表
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
        //添加菜单
        setMenu() {
            let self = this;
            let name = this.name;
            let icon = this.icon;
            let url = this.url;
            let up_id = this.$refs.up_id.value;
            let level;

            if (!name || !icon || !url) {
                layer.msg('带*号数据段不能为空!', {icon: 5, time: 1000});
                return;
            }

            if (up_id === '0') {
                level = 1;
            } else {
                level = 2;
            }

            self.dis = 'disabled';
            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'post',
                url: 'system/menu/set_menu',
                data: {
                    name: name,
                    icon: icon,
                    url: url,
                    up_id: up_id,
                    level: level
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
        }
    },
    computed: {},
    watch: {
        name(value) {
            if (value.length < 3) {
                this.name_check = '不能少于3个字符';
            } else {
                this.name_check = '✓';
            }
        }
    },
    updated: function () {
        layui.use('form', function () {
            let form = layui.form;
            form.render();
        })
    },
    filters: {}
});
