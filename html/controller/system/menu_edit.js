new Vue({
    el: ".all",
    data: {
        //菜单列表信息
        menu: [],
        id: '',
        //菜单名称
        name: '',
        //菜单图标
        icon: '',
        //菜单链接
        url: '',
        //上级菜单ID
        up_id: '',
        name_check: ''
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
        this.getMenuLevel();
        this.getMenu();
    },
    methods: {
        //显示一级菜单列表
        getMenuLevel() {
            let self = this;
            this.axios({
                method: 'get',
                url: 'system/menu/getMenuInfo'
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            response.data.data.menu.forEach(function (value) {
                                if (value['level'] == '1') {
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
        //获取修改菜单的信息
        getMenu() {
            let self = this;
            this.axios({
                method: 'get',
                url: 'system/menu/getMenu',
                params: {
                    id: getUrlParam('id')
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            self.id = response.data.data.id;
                            self.name = response.data.data.name;
                            self.icon = response.data.data.icon;
                            self.url = response.data.data.url;
                            self.up_id = response.data.data.up_id;
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
        //修改菜单
        editMenu() {
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

            let loadIndex = layer.load(2, {time: 30 * 1000});
            this.axios({
                method: 'put',
                url: 'system/menu/editMenu',
                params: {
                    id: this.id
                },
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
                            layer.msg(response.data.msg);
                    }
                    layer.close(loadIndex)
                })
                .catch(function (error) {
                    layer.close(loadIndex);
                    console.log(error);
                });
        }
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
        layui.use('form', function () {
            let form = layui.form;
            form.render();
        })
    },
    filters: {}
});
