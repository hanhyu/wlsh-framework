new Vue({
    el: ".all",
    data: {
        'swoole': [],
        'pool': [],
        'content': [],
        'title': ''
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
        let self = this;
        window.onload = function () {
            self.getStatus();
        };
    },
    methods: {
        getStatus() {
            let self = this;
            this.axios({
                method: 'get',
                url: 'system/server_status/get_status'
            })
                .then(function (response) {
                    if (response.data.code === 201) {
                        layer.msg(response.data.info);
                    }
                    if (response.data.code === 200) {
                        self.swoole = response.data.data.swoole;
                        self.pool = response.data.data.pool;
                        self.content = response.data.data.content;
                        self.swoole.start_time = formatDate(response.data.data.swoole.start_time);
                    }
                    if (response.data.code === 503 || response.data.code === 504 || response.data.code === 505) {
                        location.href = 'login.html';
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    watch: {},
    updated: function () {
    },
    filters: {}
});