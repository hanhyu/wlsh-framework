new Vue({
    el: ".all",
    data: {
        info: []
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
        this.getRouterInfo();
    },
    methods: {
        getRouterInfo() {
            let self = this;
            this.axios({
                method: 'get',
                url: 'system/logRouter/getInfo',
                params: {
                    trace_id: getUrlParam('trace_id')
                }
            })
                .then(function (response) {
                    switch (response.data.code) {
                        case 200:
                            self.info = response.data.data;
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
    watch: {},
    updated: function () {
    },
    filters: {}
});
