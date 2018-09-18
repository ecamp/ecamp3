import Vue from 'vue'

export function setBaseUrl(baseUrl) {
    if (process.env.NODE_ENV == 'production') {
        __webpack_public_path__ = baseUrl;
    } else {
        __webpack_public_path__ = 'http://localhost:8080/';
    }
}
