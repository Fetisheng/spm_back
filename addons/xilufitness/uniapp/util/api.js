/**
 * API接口封装
 */
import configs from './config.js';
//选择文件
function chooseFile(count, success) {
	// #ifdef MP-WEIXIN
	uni.chooseMedia({
		count: count > 9 ? 9 : count,
		mediaType: ['image', 'video'],
		sourceType: ['album', 'camera'],
		sizeType: ['compressed'],
		maxDuration: 30,
		camera: 'back',
		success(res) {
			let files = res.tempFiles;
			typeof success === 'function' && success(files);
		}
	})
	// #endif

	// #ifdef MP-ALIPAY
	uni.chooseImage({
		count: count > 9 ? 9 : count, //默认9
		sizeType: ['compressed'], //可以指定是原图还是压缩图，默认二者都有
		sourceType: ['album', 'camera'], //从相册选择
		success: function(res) {
			let files = res.tempFilePaths;
			typeof success === 'function' && success(files);
		}
	});
	// #endif
}

//气泡提醒
function toast(msg) {
	uni.showToast({
		title: msg,
		icon: 'none',
	})
}

//弹窗提醒
function modal(title, content, success, showCancel = true, cancelText = '取消', confirmText = '确定') {
	uni.showModal({
		title: title || '温馨提示',
		content: content || '确定此操作么？',
		showCancel: showCancel,
		cancelText: cancelText,
		confirmText: confirmText,
		success: function(res) {
			typeof success === 'function' && success(res);
		}
	})
}

//设置缓存
function setCache(key, value, expireTime = 0) {
	let field = (configs.brand_key || 'xilufitness') + '_' + key;
	let timeStamp = (expireTime == 0) ? 0 : parseInt((new Date().getTime()) / 1000) + parseInt(86400 * expireTime);
	let data = {
		value: value,
		expireTime: timeStamp
	};
	return uni.setStorageSync(field, data);
}

//读取缓存
function getCache(key) {
	let field = (configs.brand_key || 'xilufitness') + '_' + key;
	let value = uni.getStorageSync(field);
	if (value.expireTime == 0) {
		return value.value;
	} else {
		let timeStamp = parseInt((new Date().getTime()) / 1000);
		if (parseInt(timeStamp) > parseInt(value.expireTime)) {
			delCache(key);
			return null;
		}
		return value.value;
	}

}

//删除缓存
function delCache(key = '') {
	let field = (configs.brand_key || 'xilufitness') + '_' + key;
	return uni.removeStorageSync(field);
}

//清楚缓存
function clearCache() {
	return uni.clearStorageSync();
}

//定位
function getLocation(success) {
	uni.getLocation({
		type: 'gcj02',
		complete(res) {
			(typeof success === 'function') && success(res);
		}
	})
}

//打开地图
function openLocation(lat, lng, success, fail) {
	uni.openLocation({
		latitude: parseFloat(lat),
		longitude: parseFloat(lng),
		success(res) {
			typeof success === 'function' && success(res);
		},

		fail(error) {
			console.log('openLocationError', error);
			toast('地图打开失败');
			typeof fail === 'function' && fail(error);
		}
	})
}

function redirect(url, success) {
	uni.redirectTo({
		url: url,
		success: function(res) {
			typeof success === 'function' && success(res);
		}
	})
}

function navigate(url, success) {
	uni.navigateTo({
		url: url,
		success: (res) => {
			typeof success === 'function' && success(res);
		}
	})
}

function switchTab(url, success) {
	uni.switchTab({
		url: url,
		success: (res) => {
			typeof success === 'function' && success(res);
		}
	})
}

function back(backTime = 0, delta = 1, success) {
	setTimeout(function() {
		uni.navigateBack({
			delta: delta,
			success: (res) => {
				typeof success === 'function' && success(res);
			}
		});
	}, backTime);
}

function reLaunch(url, success) {
	uni.reLaunch({
		url: url,
		success: (res) => {
			typeof success === 'function' && success(res);
		}
	})
}

function showBarLoading() {
	uni.showNavigationBarLoading();
}

function hideBarLoading() {
	uni.hideNavigationBarLoading();
}

function setNavigateTitle(title = '') {
	uni.setNavigationBarTitle({
		title: title
	})
}

function scanCode(success,fail) {
	uni.scanCode({
		success: function(res) {
			typeof success === 'function' && success(res);
		},
		fail:function(error){
			typeof fail === 'function' && fail(error);
		}
	})
}

function toPay(data, order_type, success, fail) {
	let provider = null;
	// #ifdef MP-WEIXIN
	provider = 'wxpay';
	// #endif
	uni.requestPayment({
		provider: provider,
		timeStamp: data.timeStamp,
		nonceStr: data.nonceStr,
		package: data.package,
		signType: data.signType,
		paySign: data.paySign,
		success(res) {
			typeof success === 'function' && success(res);
		},
		fail(error) {
			toast('支付失败');
			typeof fail === 'function' && fail(error);
		}
	})

}


export default {
	chooseFile: chooseFile,
	toast: toast,
	modal: modal,
	setCache: setCache,
	getCache: getCache,
	delCache: delCache,
	clearCache: clearCache,
	getLocation: getLocation,
	openLocation: openLocation,
	redirect: redirect,
	navigate: navigate,
	switchTab: switchTab,
	back: back,
	reLaunch: reLaunch,
	showBarLoading: showBarLoading,
	hideBarLoading: hideBarLoading,
	toPay: toPay,
	scanCode: scanCode,
	setNavigateTitle: setNavigateTitle
}