<template>
	<view class="m-footer" :style="'--backgroundColor:'+(backgroundColor?backgroundColor:'#fff')">
		<!-- <navigator v-for="(item,index) in (list)" :key="item.index" hover-class="none" class="m-footer-item" open-type="redirect" :url="item.pagePath" :data-index="index"></navigator> -->
		<view v-for="(item,index) in (list)" :key="index" class="m-footer-item"
			@click="jumpFooter(item.pagePath,item.needLogin)">
			<image class="foot-icon" :src="footState === index ? item.selectedIconPath : item.iconPath"></image>
			<view class="foot-text" :style="'color: '+(footState === index ? selectedColor : color)">
				{{item.text}}
			</view>
			<text class="foot-badge" v-if="index == 2 && num > 0">{{num}}</text>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		name: "Footer",
		props: {
			identity: {
				default: 1,
				type: Number
			},
			footState: {
				default: 0,
				type: Number
			},
			num: {
				default: 0,
				type: Number
			},
		},
		data() {
			return {
				backgroundColor: "#1E1F20",
				color: "#999999",
				selectedColor: "#FFCF00",
				list: [],
				list1: [{
						"pagePath": "/pages/index/index",
						"text": "健身",
						"iconPath": "/static/images/xilu_footer1_uc.png",
						"selectedIconPath": "/static/images/xilu_footer1_sc.png",
					},
					{
						"pagePath": "/pages/course_classification/course_classification",
						"text": "课程",
						"iconPath": "/static/images/xilu_footer2_uc.png",
						"selectedIconPath": "/static/images/xilu_footer2_sc.png",
					},
					{
						"pagePath": "/pages/trainer/trainer",
						"text": "教练",
						"iconPath": "/static/images/xilu_footer3_uc.png",
						"selectedIconPath": "/static/images/xilu_footer3_sc.png",
					},
					{
						"pagePath": "/pages/booking/booking",
						"text": "预约",
						"iconPath": "/static/images/xilu_footer4_uc.png",
						"selectedIconPath": "/static/images/xilu_footer4_sc.png",
					},
					{
						"pagePath": "/pages/mine/mine",
						"text": "我的",
						"iconPath": "/static/images/xilu_footer5_uc.png",
						"selectedIconPath": "/static/images/xilu_footer5_sc.png",
					}
				],
				list2: [{
						"pagePath": "/pages/scheduling/scheduling",
						"text": "课程",
						"iconPath": "/static/images/xilu_footer2_uc.png",
						"selectedIconPath": "/static/images/xilu_footer2_sc.png",
					},
					{
						"pagePath": "/pages/scan_code/scan_code",
						"text": "扫一扫",
						"iconPath": "/static/images/xilu_footer6_uc.png",
						"selectedIconPath": "/static/images/xilu_footer6_sc.png",
					},
					{
						"pagePath": "/pages/profile/profile",
						"text": "我的",
						"iconPath": "/static/images/xilu_footer5_uc.png",
						"selectedIconPath": "/static/images/xilu_footer5_sc.png",
					}
				],

			};
		},
		watch: {
			identity(newVal, old) {
				if (newVal == 1) {
					this.list = this.list1;
				} else if (newVal == 2) {
					this.list = this.list2;
				} else {
					this.list = this.list3;
				}
			}
		},
		created() {
			if (this.identity == 1) {
				this.list = this.list1;
			} else if (this.identity == 2) {
				this.list = this.list2;
			} else {
				this.list = this.list3;
			}
		},
		methods: {
			jumpFooter(url, needLogin) {
				let _this = this;
				let list = this.list || [];
				let num = this.footState;
				if (url == '/pages/scan_code/scan_code') {
					//扫码核销
					this.userScan();
				} else {
					for (let i in list) {
						if (list[i]['pagePath'] == url && num == i) {
							return false;
						}
					}
					uni.redirectTo({
						url: url
					})
				}

			},
			//扫一扫
			userScan() {
				let _this = this;
				
				this.$api.scanCode(function(res) {
					let code_url = res.result || '';
					if (!code_url) {
						_this.$api.toast('未获取到二维码内容')
					} else {
						_this.$http({
							url: '/addons/xilufitness/order/confirmOrder',
							data: {
								urls:encodeURIComponent(code_url)
							},
							method: 'post'
						}).then(res => {
							if (res.code == 1) {
								_this.$api.toast('核销成功过');
								uni.$emit('coachCheckOrder',{});
							} else {
								_this.$api.modal('温馨提示', res.msg || '核销失败', function() {}, false)
							}
						}).catch(error => {
							console.log('scanCodeError', error);
						})
					}
				}, function(error) {
					_this.$api.toast('扫码失败')
				});
			}
		}
	}
</script>
<style scoped>
	/* components/u-foot/index.css */
	.m-footer {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 99;
		display: -webkit-flex;
		display: flex;
		-webkit-align-items: center;
		align-items: center;
		width: 100%;
		background-color: var(--backgroundColor);
		box-sizing: border-box;
		/* border-top: 1rpx solid #EEEEEE; */
	}

	.m-footer-border {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 1px;
		background-color: var(--borderStyle);
		-webkit-transform-origin: 0 0;
		transform-origin: 0 0;
		-webkit-transform: scaleY(0.5);
		transform: scaleY(0.5);
		pointer-events: none;
		box-sizing: border-box;
	}

	.m-footer-item {
		-webkit-flex: 1;
		flex: 1;
		position: relative;
		padding: 12rpx 10rpx 12rpx;
		height: 98rpx;
	}

	.m-footer-item .foot-icon {
		display: block;
		margin: 0 auto 7rpx;
		width: 45rpx;
		height: 45rpx;
	}

	.m-footer-item .foot-text {
		text-align: center;
		line-height: 22rpx;
		font-size: 22rpx;
	}

	.m-footer-item .foot-badge {
		position: absolute;
		right: 50%;
		top: 10rpx;
		margin-right: -45rpx;
		box-sizing: border-box;
		padding: 0 3px;
		min-width: 16px;
		height: 14px;
		line-height: 12px;
		border: 1px solid #fff;
		border-radius: 99px;
		color: #fff;
		font-size: 11px;
		font-weight: 500;
		text-align: center;
		white-space: nowrap;
		background-color: #f44;
	}

	.m-footer-bg {
		display: block;
		width: 100vw;
		height: 130rpx;
		position: absolute;
		left: 0;
		right: 0;
		bottom: 0;
	}

	.m-footer-bg image {
		display: block;
		width: 100%;
		height: 100%;
	}

	.m-footer-bg::after {
		content: '';
		display: block;
		width: 100%;
		height: 60rpx;
		background-color: #fff;
		position: absolute;
		left: 0;
		bottom: -17px;
	}


	@supports (bottom: constant(safe-area-inset-bottom)) or (bottom: env(safe-area-inset-bottom)) {
		.m-footer {
			padding-bottom: calc(34px/2);
			padding-bottom: calc(constant(safe-area-inset-bottom)/2);
			padding-bottom: calc(env(safe-area-inset-bottom)/2);
		}

		.m-footer-bg {
			bottom: calc(34px/2);
			bottom: calc(constant(safe-area-inset-bottom)/2);
			bottom: calc(env(safe-area-inset-bottom)/2);
		}
	}
</style>