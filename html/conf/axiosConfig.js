function axiosConfig() {
    axios.defaults.timeout = 7000;

    axios.defaults.headers.common['Accept'] = '*/*';
    axios.defaults.headers.common['Authorization'] = localStorage.getItem(window.location.host + '_token');
    //axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
    //axios.defaults.headers.post['Content-Type'] = 'application/json;charset=UTF-8';
    axios.defaults.withCredentials = true;   // axios 默认不发送cookie，需要全局设置true发送cookie
    axios.defaults.baseURL = `https://${window.location.hostname}:9770`;
}