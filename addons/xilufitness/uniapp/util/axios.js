/**
 * 请求封装库
 */
import Vue from 'vue';

import axios from '../uni_modules/axios/index.js';

import configs from './config.js';

import api from './api.js';

axios.defaults.headers['Content-Type'] = 'application/x-www-form-urlencoded'; //application/json;charset=utf-8';
const service = axios.create({
	baseURL: configs.base_url,
	timeout: configs.timeout,
})

// 请求拦截
service.interceptors.request.use(config => {
		config.headers['brand-key'] = configs.brand_key || '';
		// 非登录接口添加token
		if (config["url"].indexOf("login") < 0) {
			config.headers['token'] = api.getCache('token') || '';
		}
		return config;
	},
	error => {
		return Promise.reject(error);
	});

// 响应拦截
service.interceptors.response.use(res => {
	if (res.status == 200) {
		// 请求成功后设置token
		if (res.data.code == 1 && res.config.url.indexOf("login/registerLogin") > -1 && res.data.token) {
			api.setCache('token', res.data.token)
		}
		return res.data;
	} else {
		return Promise.reject(res.data.msg || '');
	}
});


// 自定义适配器 ， 适配uniapp语法
axios.defaults.adapter = function(config) {
	return new Promise((resolve, reject) => {
		let settle = require('../uni_modules/axios/lib/core/settle');
		let buildURL = require('../uni_modules/axios/lib/helpers/buildURL');
		let buildFullPath = require('../uni_modules/axios/lib/core/buildFullPath');
		let fullurl = buildFullPath(config.baseURL, config.url);
		api.showBarLoading();
		uni.request({
			method: config.method.toUpperCase(),
			url: buildURL(fullurl, config.params, config.paramsSerializer),
			timeout: config.timeout || 0,
			header: config.headers,
			data: typeof config.data === 'object' ? JSON.stringify(config.data) : (config.data ?
				JSON.parse(config.data) : ''),
			dataType: config.dataType || 'JSON',
			responseType: config.responseType || 'text',
			sslVerify: config.sslVerify || true,
			complete: function complete(response) {
				response = {
					data: response.data,
					status: response.statusCode,
					errMsg: response.errMsg,
					header: response.header,
					config: config
				};
				api.hideBarLoading();
				if (response.status == 401) {
					api.modal('温馨提示', '请登录后再操作', function() {
						api.clearCache();
						api.reLaunch("/pages/mine/mine");
					}, false)
					return false;
				}
				if (response.status == 500) {
					console.log('requestError','网络错误');
				}
				settle(resolve, reject, response);
			},
		});
	})
}

export default service