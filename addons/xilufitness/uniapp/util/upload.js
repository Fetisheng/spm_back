/**
 * 封装上传接口
 */
import configs from './config.js';
import apis from './api.js';

function uploadFile(filePath = '', success) {
	let full_url = configs.base_url + '/api/common/upload';
	let uploadTask = null;
	uploadTask = uni.uploadFile({
		url: full_url,
		filePath: filePath,
		name: 'file',
		header: {
			token: apis.getCache('token') || '',
			"brand-key": configs.brand_key || '',
		},
		complete: (res) => {
			typeof success === 'function' && success(JSON.parse(res.data), uploadTask);
		}
	});
}

export default {
	uploadFile: uploadFile
}