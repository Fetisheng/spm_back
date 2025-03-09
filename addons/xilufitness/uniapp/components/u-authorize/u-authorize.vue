<template>
	<view>
		<u-popup :show="popupStatus" mode="center" round="10" main-class="u-authorize" customStyle="auth-class-1"
			custom-style="auth-class-1" :safeAreaInsetBottom="false">
			<view class="main">
				<view class="u-authorize-title">{{isAuth == 1 ? '微信授权' : '获取手机号'}}</view>
				<view class="u-authorize-body">
					<image class="u-authorize-body__icon" src="@/static/icon/phone.png" mode="aspectFit"></image>
					<view class="u-authorize-body__title">授权绑定您的手机号码</view>
				</view>
				<view class="u-authorize-tool">
					<button class="u-authorize-tool__cancel" @tap="onCancel">
						<view style="line-height: 100rpx;">
							拒绝
						</view>
					</button>
					<button class="u-authorize-tool__define" open-type="getPhoneNumber"
						@getphonenumber="authorizationPhone" @tap="onConfirm">
						<view style="line-height: 100rpx;color: #FFCF00;">
							同意
						</view>
					</button>

				</view>

			</view>
		</u-popup>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		name: "u-authorize",
		data() {
			return {
				openidInfo: null
			};
		},
		props: {
			popupStatus: [Boolean],
			isAuth: [Number]
		},
		created() {
			let openidInfo = this.$api.getCache('openidInfo');
			let _this = this;
			if (!openidInfo) {
				_this.miniLogin();
			}
		},
		methods: {
			onCancel(event) {
				this.$emit('onAuthCancel');
			},
			onConfirm(event) {

			},
			//小程序静默登录
			miniLogin() {
				let _this = this;
				uni.login({
					provider: 'weixin',
					success: function(res) {
						_this.getOpenidByCode(res.code)
					},
					fail: function(error) {
						console.log('authError', error);
					}
				})
			},
			//根据code获取openid
			getOpenidByCode(code) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/login/getOpenid',
					data: {
						code: code
					},
					method: 'get'
				}).then(res => {
					if(res.code == 1){
						_this.openidInfo = res.data;
						_this.$api.setCache('openidInfo', res.data);
					}
				}).catch(error => {
					console.log('获取openid失败：', error);
				})
			},
			// 获取手机号
			authorizationPhone(e) {
				var _this = this;
				if (e.detail.errMsg !== "getPhoneNumber:ok") {
					this.$api.toast('授权失败，请重新点击授权');
					return false;
				}
				var openidInfo = this.$api.getCache('openidInfo');
				if (!openidInfo) {
					_this.miniLogin();
					this.$api.toast('未获取到信息，请重新点击授权');
					return false;
				}
				this.decodeMobile({
					encryptedData: e.detail.encryptedData,
					iv: e.detail.iv,
					sessionKey: openidInfo.session_key,
				}, openidInfo);
			},
			//解析手机号
			decodeMobile(data, openidInfo) {
				var _this = this;
				this.$api.showBarLoading();
				this.$http({
					url: '/addons/xilufitness/login/decodeData',
					data: data,
					method: 'post'
				}).then(res => {
					if(res.code == 1){
						_this.userRegister(res.data.phoneNumber, openidInfo.openid);
					} else {
						_this.$api.toast(res.msg || '解析手机号失败')
					}
					
				}).catch(error => {

				});
				this.$api.hideBarLoading();
			},
			//用户注册
			userRegister(mobile, openid) {
				var _this = this;
				this.$http({
					url: '/addons/xilufitness/login/registerLogin',
					data: {
						mobile: mobile,
						openid: openid,
						rec_user_id: app.globalData.share_user_id || 0
					},
					method: 'post'
				}).then(res => {
					if (res.code == 1) {
						_this.$api.setCache('token', res.data.token);
						_this.$emit('onAuthConfirm');
					} else {
						_this.$api.toast(res.msg);
					}
				}).catch(error => {
					console.log('error', error);
					_this.$api.modal('温馨提示', error.data.msg, function() {

					}, false);
				});

			},
			//版本比较
			compareVersion(v1, v2) {
				v1 = v1.split('.')
				v2 = v2.split('.')
				const len = Math.max(v1.length, v2.length)
				while (v1.length < len) {
					v1.push('0')
				}
				while (v2.length < len) {
					v2.push('0')
				}
				for (let i = 0; i < len; i++) {
					const num1 = parseInt(v1[i])
					const num2 = parseInt(v2[i])
					if (num1 > num2) {
						return 1
					} else if (num1 < num2) {
						return -1
					}
				}
				return 0
			},

		}
	}
</script>

<style lang="scss" scoped>
	.main {
		width: 600rpx;
	}

	.auth-class-1 {
		width: 65vw !important;
		border-radius: 20rpx !important;
		overflow: hidden;
	}

	.u-authorize {
		min-height: initial !important;
		max-height: inherit !important;
	}

	.u-authorize-title {
		position: relative;
		text-align: center;
		line-height: 90rpx;
		font-size: 34rpx;
		color: #000;
	}

	.u-authorize-title:after {
		content: " ";
		position: absolute;
		top: 0;
		left: 0;
		width: 200%;
		height: 200%;
		border: 0 #dddddd solid;
		border-bottom-width: 1px;
		-webkit-transform: scale(.5);
		transform: scale(.5);
		-webkit-transform-origin: 0 0;
		transform-origin: 0 0;
		pointer-events: none;
		box-sizing: border-box;
	}

	.u-authorize-body {
		padding: 40rpx 80rpx;
		text-align: center;
	}

	.u-authorize-body__icon {
		display: block;
		width: 100rpx;
		height: 100rpx;
		margin: 0 auto;
	}

	.u-authorize-body__title {
		padding: 30rpx 0;
		font-size: 32rpx;
		color: #101010;
	}

	.u-authorize-body__info {
		position: relative;
		padding: 20rpx 0;
		font-size: 24rpx;
		color: #898989;
	}

	.u-authorize-body__info:after {
		content: " ";
		position: absolute;
		top: 0;
		left: 0;
		width: 200%;
		height: 200%;
		border: 0 #dddddd solid;
		border-top-width: 1px;
		-webkit-transform: scale(.5);
		transform: scale(.5);
		-webkit-transform-origin: 0 0;
		transform-origin: 0 0;
		pointer-events: none;
		box-sizing: border-box;
	}

	.u-authorize-tool {
		position: relative;
		display: flex;
	}

	.u-authorize-tool:after {
		content: " ";
		position: absolute;
		top: 0;
		left: 0;
		width: 200%;
		height: 200%;
		border: 0 #dddddd solid;
		border-top-width: 1px;
		-webkit-transform: scale(.5);
		transform: scale(.5);
		-webkit-transform-origin: 0 0;
		transform-origin: 0 0;
		pointer-events: none;
		box-sizing: border-box;
	}

	.u-authorize-tool button {
		position: relative;
		flex: 1;
		margin: 0;
		padding: 0;
		border: 0;
		width: 100%;
		height: 100rpx;
		line-height: 100rpx;
		font-size: 32rpx;
		text-align: center;
		border-radius: 0;
		-webkit-appearance: none;
		-webkit-text-size-adjust: 100%;
		box-sizing: border-box;
		background: transparent;
	}

	.u-authorize-tool button:after {
		content: " ";
		position: absolute;
		top: 0;
		left: 0;
		width: 200%;
		height: 200%;
		border: 0 #dddddd solid;
		border-right-width: 1px;
		-webkit-transform: scale(.5);
		transform: scale(.5);
		-webkit-transform-origin: 0 0;
		transform-origin: 0 0;
		pointer-events: none;
		box-sizing: border-box;
		border-radius: 0;
	}

	.u-authorize-tool button:last-child:after {
		display: none;
	}

	.u-authorize-tool__cancel {
		color: #666;
	}

	.u-authorize-tool__define {
		color: #09bb07;
	}

	.u-safe-bottom {
		display: none !important;
	}
</style>