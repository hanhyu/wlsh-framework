new Vue({
    el: ".all",
    data: {
        //菜单名称
        info: []
    },
    mounted: function () {
        checkLogin();
        this.info = JSON.parse(getUrlParam('info'));
    },
    methods: {},
    watch: {},
    updated: function () {
    },
    filters: {
        timestampFilter: function (value) {
            return formatDate(value);
        }
    }
});