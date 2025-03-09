<template>
	<view class="xilu">
		<view class="page-foot bg-normal plr25" @click.stop="createOrder()">
			<view class="xilu_foot_nav flex-box pl30">
				<view class="flex-grow-1">
					<view class="fs28 col9 lh40">合计</view>
					<view class="flex flex-align-end">
						<view class="fs34 col2 fw500">¥{{pay_price || 0}}</view>
						<view class="tdl fs20 col9 pl10">¥{{total_price || 0}}</view>
					</view>
				</view>
				<view class="xilu_foot_nav_btn">立即支付</view>
			</view>
		</view>
		<view class="container">
			<view class="pr">
				<swiper class="xilu_swiper_info" @change="swiperTab" :current="current" :circular="true"
					:autoplay="true" :interval="3000" :duration="1000">
					<swiper-item v-for="(vo,index) in thumb_images">
						<image :src="vo" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
				<view class="xilu_swiper_dot">{{ current + 1 }}/{{ thumb_images.length }}</view>
				<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_mask.png'" mode="aspectFill" class="xilu_mask"></image>
			</view>
			<view class="pr z5 plr25 pb60">
				<view class="xilu_introduce_box">
					<view class="fs44 colf lh62" v-if="is_type == 3">{{info.camp.title || ''}}</view>
					<view class="fs44 colf lh62" v-else>{{info.course.title || ''}}</view>
					<view class="mt40 flex-box">
						<image src="@/static/images/xilu_icon10.png" mode="aspectFill" class="ico28"></image>
						<view class="pl5 fs28 colf flex-grow-1">教练：{{info.coach.coach_name || ''}}</view>
					</view>
					<view class="mt40 flex-box">
						<image src="@/static/images/xilu_icon11.png" mode="aspectFill" class="ico28"></image>
						<view class="pl5 fs28 colf flex-grow-1" v-if="is_type == 3">时间：
							<template v-for="(vo,index) in info.plans">
								{{vo.week || ''}} {{vo.day_start_at || ''}}～{{vo.day_end_at || ''}}
							</template>
						</view>
						<view class="pl5 fs28 colf flex-grow-1" v-else>时间：{{info.class_time_txt || ''}}
							{{info.start_at || ''}}–{{info.end_at || ''}}
						</view>
					</view>
					<view class="mt40 flex">
						<image src="@/static/images/xilu_icon12.png" mode="aspectFill" class="ico28 mt6"></image>
						<view class="pl5 fs28 colf flex-grow-1 lh40 m-ellipsis-l2 pr40">地址：{{info.shop.address || ''}}
						</view>
					</view>
				</view>
				<view class="xilu_box">
					<!--  -->
					<view class="bb flex" v-if="is_type == 3 && info.sign_count > 1">
						<view class="flex-grow-1 col9 fs28">人数</view>
						<view class="pl20" style="max-width: 85%;">
							<!--  -->
							<template v-for="(vo,index) in info.sign_count">
								<view :class="(num == (index + 1)) ? 'xilu_choose_link ac' : 'xilu_choose_link'"
									@tap="chooseNum((index+1))">{{index + 1}}人</view>
							</template>

						</view>
					</view>
					<view class="ptb30 bb flex-box">
						<view class="flex-grow-1 col9 fs28">总价</view>
						<view class="fs30 colf">{{total_price || 0}}元</view>
					</view>
					<!-- <picker mode="selector" :value="qindex" :range="coupons" @change="changeCoupons" -->
					<view v-if="coupons.length > 0" @click.stop="chooseCoupon(coupons)">
						<view class="pt30 flex-box">
							<view class="flex-grow-1 col9 fs28">代金劵</view>
							<view class="fs30 col2">{{coupon_price > 0 ? ('-' + coupon_price + '元') : '请选择'}}</view>
							<image src="@/static/images/arrow_white.png" mode="aspectFill" class="ico30 ml10"></image>
						</view>
					</view>
					<!-- </picker> -->

				</view>

				<view class="xilu_box">
					<view v-if="userInfo && userInfo.account > 0"
						:class="pay_type == 1 ? 'xilu_pay_item ac flex-box plr30' : 'xilu_pay_item flex-box plr30'"
						@tap="choosePayType(1)">
						<image src="@/static/images/xilu_icon13.png" mode="aspectFill" class="ico40"></image>
						<view class="flex-grow-1 plr20 fs32 colf">会员卡支付</view>
						<view class="fs30 col2">余额：{{userInfo.account || 0}}元</view>
					</view>
					<view :class="pay_type == 2 ? 'xilu_pay_item flex-box ac plr30' : 'xilu_pay_item flex-box plr30'"
						@tap="choosePayType(2)">
						<image src="@/static/images/xilu_icon14.png" mode="aspectFill" class="ico40"></image>
						<view class="flex-grow-1 plr20 fs32 colf">微信支付</view>
					</view>
				</view>
				<view class="xilu_box" v-if="tips_content">
					<view class="pb30 mb30 fs36 fw500 colf lh50 bb">退课须知：</view>
					<view class="fs28 col9 lh40">
						<mp-html :content="tips_content"></mp-html>
					</view>
				</view>
			</view>
		</view>
		<u-authorize @onAuthCancel="onAuthCancel($event)" @onAuthConfirm="onAuthConfirm($event)"
			:popupStatus="auth_status" :isAuth="2"></u-authorize>
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				current: 0,
				thumb_images: [],
				coupons: [],
				user_coupon_id: 0,
				userInfo: null,
				info: null,
				is_type: 0,
				num: 1,
				price: 0.00,
				total_price: 0.00,
				pay_price: 0.00,
				coupon_price: 0.00,
				pay_type: 2,
				auth_status: false,
				tips_content: '',
				web_url:''
			}
		},
		methods: {
			swiperTab(e) {
				this.current = Number(e.target.current)
				console.log(this.current);
			},
			//选择优惠券
			chooseCoupon(coupons) {
				let _this = this;
				this.$api.navigate('../coupons_list/coupons_list', function(res) {
					res.eventChannel.emit('chooseCoupon', {
						coupons_list: coupons
					});
					res.eventChannel.on("reloadCoupon", function(params) {
						_this.coupon_price = params.couponInfo.discount_amount || 0.00;
						_this.user_coupon_id = params.couponInfo.id || 0;
						console.log('user_coupon_id', _this.user_coupon_id);
						console.log('couponInfo', params.couponInfo);
						let total_price = _this.total_price;
						let pay_price = parseFloat(total_price) > parseFloat(_this.coupon_price) ? (
							parseFloat(total_price) - parseFloat(_this.coupon_price)) : 0;
						_this.pay_price = pay_price.toFixed(2);
					});
				});
			},
			//授权取消
			onAuthCancel(e) {
				this.auth_status = false;
				this.$api.toast('取消授权，可能会使部分服务不能用，或页面信息不完整')
			},
			//授权成功
			onAuthConfirm(e) {
				this.auth_status = false;
				this.createOrder();
			},
			//选择报名人数
			chooseNum(num) {
				this.num = num;
				this.getTotalPrice();
			},
			//选择支付类型
			choosePayType(pay_type) {
				let pay_price =parseFloat(this.pay_price);
				let user_account = parseFloat(this.userInfo.account);
				if (pay_type == 1 && pay_price > user_account) {
					this.$api.toast('余额不足');
				} else {
					this.pay_type = pay_type || 2;
				}
			},
			//获取详情
			getDetail(id, is_type) {
				let url = '';
				let _this = this;
				if (is_type == 3) {
					url = '/addons/xilufitness/course/getCampDetail';
				} else {
					url = '/addons/xilufitness/course/detail';
				}
				this.$http({
					url: url,
					data: {
						id: id
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.info = res.data.info;
						_this.is_type = is_type;
						if (is_type == 3) {
							_this.price = res.data.info.camp_price || 0.00;
							_this.thumb_images = res.data.info.camp.xilufitness_urls.thumb_images || [];
						} else {
							_this.price = res.data.info.course_price || 0.00;
							_this.thumb_images = res.data.info.course.xilufitness_urls.thumb_images || [];
						}
						_this.getTotalPrice();
					}
				}).catch(error => {
					console.log('orderDedetail', error);
				})
			},
			//获取用户信息
			getUserDetail() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/user/index',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.userInfo = res.data.info;
					}
				}).catch(error => {
					console.log('userError', error);
				})
			},
			//优惠券列表
			getCouponList() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coupon/getOrderCoupon',
					data: {
						total_price: _this.total_price
					},
					method: 'post'
				}).then(res => {
					if (res.code == 1) {
						_this.coupons = res.data.list || []
					}
				}).catch(error => {
					console.log('couponError', error);
				})
			},

			//获取总价
			getTotalPrice() {
				let _this = this;
				let num = this.num || 1;
				let price = this.price || 0.00;
				let total_price = parseFloat(price) * parseInt(num);
				_this.total_price = total_price.toFixed(2);
				_this.pay_price = total_price.toFixed(2);
				_this.getCouponList();
			},
			//创建订单
			createOrder() {
				let _this = this;
				let token = this.$api.getCache('token');
				if (!token) {
					this.auth_status = true;
				} else {
					this.$http({
						url: '/addons/xilufitness/order/createOrder',
						data: {
							id: _this.info.id || 0,
							num: _this.num,
							is_type: _this.is_type || 0,
							user_coupon_id: _this.user_coupon_id || 0,
							pay_type: _this.pay_type || 2
						},
						method: 'post'
					}).then(res => {
						if (res.code == 1) {
							_this.toPay(res.data);
						} else {
							_this.$api.modal('温馨提示', (res.msg || '下单失败'), function(ret) {

							}, false)
						}
					}).catch(error => {
						console.log('orderError', error);
					});
				}
			},
			//去支付
			toPay(data) {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/pay/index',
					data: data,
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (data.pay_type == 2) {
							//发起微信支付
							_this.wechatPay(res.data, data.order_type);
						} else {
							//到支付成功界面
							_this.$api.navigate('../pay_success/pay_success');
						}
					} else {
						_this.$api.modal('温馨提示', res.msg || '支付失败', function() {}, false);
					}
				}).catch(error => {
					console.log('payError', error);
				})
			},
			//发起微信支付
			wechatPay(data, order_type) {
				let _this = this;
				this.$api.toPay(data, order_type, function(res) {
					if (order_type == 0) {
						//会员充值
						_this.$api.navigate('../member_order/member_order');
					} else {
						//到支付成功界面
						_this.$api.navigate('../pay_success/pay_success');
					}
				}, function(error) {
					_this.$api.modal('温馨提示', '支付失败', function() {}, false);
					console.log('payError', error);
				})

			},
			//退课须知
			getTips() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/cms/detail',
					data: {
						is_type: 3
					},
					method: 'GET'
				}).then(res => {
					if (res.code == 1) {
						_this.tips_content = (res.data.info && res.data.info.content) ? res.data.info.content : '';
					}
				}).catch(error => {
					console.log('tipsError', error);
				});
			},
		},
		onLoad(options) {
			this.web_url = webConfig.base_url || '';
			this.getDetail((options.id), (options.is_type || 0));
			this.getUserDetail();
			this.getTips();
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_swiper_info {
			width: 100%;
			height: 750rpx;
			position: relative;

			image {
				width: 100%;
				height: 100%;
				display: block;
			}
		}

		&_mask {
			width: 100%;
			height: 380rpx;
			position: absolute;
			bottom: 0;
			z-index: 3;
			left: 0;
		}

		&_swiper_dot {
			display: inline-block;
			height: 39rpx;
			line-height: 39rpx;
			background: rgba(0, 0, 0, 0.25);
			border-radius: 20rpx;
			position: absolute;
			font-size: 24rpx;
			font-weight: 400;
			color: #FFFFFF;
			right: 25rpx;
			bottom: 410rpx;
			padding-left: 15rpx;
			padding-right: 15rpx;
			z-index: 4;
		}

		&_introduce_box {
			width: 700rpx;
			height: 412rpx;
			margin-top: -380rpx;
			background: #292B2C;
			border-radius: 20rpx;
			padding: 40rpx 30rpx 30rpx;
		}

		&_box {
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 30rpx;
			padding: 30rpx;
		}

		&_choose_link {
			min-width: 118rpx;
			height: 57rpx;
			line-height: 57rpx;
			text-align: center;
			font-size: 28rpx;
			font-weight: 400;
			color: #FFFFFF;
			background: #404243;
			border-radius: 10rpx;
			display: inline-block;
			vertical-align: top;
			padding: 0 15rpx;
			margin-left: 25rpx;
			margin-bottom: 20rpx;

			&.ac {
				color: #0E0E0F;
				background: #FFCF00;
			}
		}

		// &_choose_link+&_choose_link {
		// 	margin-left: 25rpx;
		// }

		&_pay_item+&_pay_item {
			margin-top: 30rpx;
		}

		&_pay_item {
			width: 640rpx;
			height: 110rpx;
			background: #404243;
			border-radius: 15rpx;

			&.ac {
				border: 2rpx solid #FFCF00;
			}
		}

		&_foot_nav {
			width: 700rpx;
			height: 125rpx;
			background: #2C2D2E;
			box-shadow: 0rpx 2rpx 4rpx 0rpx rgba(0, 0, 0, 0.96);
			border-radius: 20rpx;
			margin-bottom: 40rpx;

			&_btn {
				width: 245rpx;
				height: 125rpx;
				line-height: 125rpx;
				text-align: center;
				font-size: 30rpx;
				font-weight: 400;
				color: #0E0E0F;
				background: #FFCF00;
				border-radius: 0rpx 20rpx 20rpx 0rpx;
			}
		}
	}

	.bb {
		border-bottom: 1px solid #434343;
	}
</style>