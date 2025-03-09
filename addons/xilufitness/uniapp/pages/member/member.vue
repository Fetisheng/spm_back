<template>
	<view class="xilu">
		<view class="page-foot bg-normal" @click.stop="submitRecharge()">
			<view class="plr25 pb50">
				<view :class="[agreeFlag ? 'btn1' : 'btn2']">支付：¥{{rechargeInfo.recharge_amount || 0}} 立即开通</view>
			</view>
		</view>
		<view class="container">
			<view class="pt30 pb50 plr25">
				<view class="xilu_info_nav">
					<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_bg_big.png'" mode="aspectFill" class="xilu_info_nav_bg"></image>
					<view class="xilu_info_nav_view">
						<view class="flex-box">
							<image :src="userInfo.xilufitness_urls.avatar || '../../static/images/avatar.png'"
								mode="aspectFill" class="xilu_head_img">
							</image>
							<view class="flex-grow-1 pl30 pr30">
								<view class="flex-box">
									<view class="flex-grow-1 m-ellipsis pr20 fs40 colf fw500 lh56">
										{{userInfo.nickname || ''}}
									</view>
									<view @click.stop="cms_detail(6)" class="tdu fs28 cola lh40">充值须知</view>
								</view>
								<view class="col8 mt20 fs28 lh40" v-if="userInfo.is_vip == 0">未开通会员</view>
							</view>
						</view>
						<view class="mt30 xilu_text">MEMBERSHIP CARD</view>
						<view class="mt20 fs30 colf lh42">开通会员卡预计省¥{{rechargeInfo.cut_amount || 0}}/年</view>
					</view>
				</view>
				<view class="xilu_box">
					<view class="colf fs36 colf lh36 pb10">充值开通</view>
					<view>
						<template v-for="(vo,keys) in recharge_list">
							<view :class="[vo.id == recharge_id ? 'xilu_money_item active' : 'xilu_money_item']"
								@click="chooseRecharge(vo)">
								<text class="fs36 fw500">¥{{vo.recharge_amount || 0}}</text><text
									class="fs24">（到账{{vo.account_amount || vo.recharge_amount}}元）</text>
								<image src="@/static/images/xilu_choose.png" mode="aspectFill"
									class="xilu_money_item_choose" v-if="vo.id == recharge_id"></image>
							</view>
						</template>
					</view>
				</view>
				<view class="xilu_box1">
					<template v-if="member_tips">
						<view class="fs36 colf lh36 pl25">会员权益</view>
						<view class="mt30 xilu_interests colf p25">
							<mp-html :content="member_tips"></mp-html>
						</view>
					</template>

					<view class="xilu_box1_fixed flex-box">
						<image @click="argeeAccount()"
							:src="'../../static/images/xilu_radio_'+(agreeFlag ? 's' : 'u')+'c.png'" mode="aspectFill"
							class="ico30"></image>
						<view class="pl15 fs28 col9">我已同意并阅读<text class="col2" @click.stop="cms_detail(6)">《充值须知》</text>
						</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	const webConfig = require("@/util/config");
	var eventChannel = null;
	export default {
		data() {
			return {
				userInfo: null,
				rechargeInfo: null,
				recharge_id: 0,
				recharge_list: [],
				agreeFlag: false,
				member_tips: ''
			}
		},
		methods: {
			getRechargeList() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/recharge/index',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.recharge_list = res.data.list;
					}
				}).catch(error => {
					console.log('rechargeError', error);
				})
			},
			//选择套餐
			chooseRecharge(rechargeInfo) {
				this.rechargeInfo = rechargeInfo;
				this.recharge_id = rechargeInfo.id || 0;
			},
			//同意协议
			argeeAccount() {
				this.agreeFlag = !this.agreeFlag;
			},
			//立即充值
			submitRecharge() {
				let _this = this;
				if (!_this.agreeFlag) {
					_this.$api.toast('请先同意协议');
				} else if (!_this.rechargeInfo) {
					_this.$api.toast('请选择套餐');
				} else {
					_this.$http({
						url: '/addons/xilufitness/order/createOrder',
						data: {
							id: _this.recharge_id,
							is_type: 0,
							num: 1,
							pay_type: 2
						},
						method: 'POST'
					}).then(res => {
						if (res.code == 1) {
							//去支付
							_this.toPay(res.data);
						} else {
							_this.$api.toast(res.msg);
						}
					}).catch(error => {
						console.log('rechargeError', error);
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
							//预约订单
							_this.$api.navigate('../booking/booking');
						}
					} else {
						_this.$api.toast(res.msg);
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
						//预约订单
						_this.$api.navigate('../booking/booking');
					}
				}, function(error) {
					console.log('payError', error);
				})

			},
			//充值须知
			cms_detail(is_type) {
				this.$api.navigate('../about_us/about_us?is_type=' + is_type);
			},
			//会员权益
			getMemberTips() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/cms/detail',
					data: {
						is_type: 5
					}
				}).then(res => {
					if (res.code == 1) {
						_this.member_tips = (res.data.info && res.data.info.content) ? res.data.info.content : '';
					}
				}).catch(error => {
					console.log('tipsError', error);
				})
			}
		},
		onLoad(options) {
			let _this = this;
			this.web_url = webConfig.base_url || '';
			_this.recharge_id = options.recharge_id || 0;
			console.log('recharge_id', _this.recharge_id);
			eventChannel = this.getOpenerEventChannel();
			eventChannel.on('memberCardData', function(params) {
				_this.userInfo = params.userInfo || null;
				_this.rechargeInfo = params.rechargeInfo;
			});
			this.getRechargeList();
			this.getMemberTips();
		},
		onShareAppMessage() {

		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_info_nav {
			width: 700rpx;
			height: 312rpx;
			position: relative;

			&_bg {
				width: 700rpx;
				height: 312rpx;
				position: relative;
				z-index: 1;
			}

			&_view {
				width: 700rpx;
				height: 312rpx;
				position: absolute;
				z-index: 1;
				top: 0;
				left: 0;
				right: 0;
				padding-left: 20rpx;
				padding-right: 0;
				padding-top: 30rpx;
			}
		}

		&_head_img {
			width: 130rpx;
			height: 130rpx;
			border: 5rpx solid rgba(255, 255, 255, 0.3);
			border-radius: 50%;
		}

		&_text {
			font-size: 22rpx;
			font-weight: 400;
			color: #999999;
			line-height: 30rpx;
			letter-spacing: 30rpx;
		}

		&_box {
			width: 700rpx;
			padding: 30rpx 25rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 30rpx;
		}

		&_money_item {
			width: 315rpx;
			height: 98rpx;
			border-radius: 20rpx;
			border: 1px solid #999999;
			display: inline-block;
			vertical-align: top;
			margin-right: 20rpx;
			padding-left: 20rpx;
			line-height: calc(98rpx - 2px);
			color: #999999;
			position: relative;
			margin-top: 20rpx;

			&.active {
				background: rgba(246, 204, 25, 0.2);
				color: #FFCF00;
				border: 1rpx solid #FFCF00;
			}

			&_choose {
				width: 38rpx;
				height: 36rpx;
				position: absolute;
				right: -1px;
				bottom: -1px;
			}
		}

		&_money_item:nth-of-type(2n) {
			margin-right: 0;
		}

		&_box1 {
			width: 700rpx;
			height: 528rpx;
			background: #292B2C;
			border-radius: 20rpx;
			margin-top: 30rpx;
			padding-top: 30rpx;
			position: relative;

			&_fixed {
				width: 700rpx;
				height: 70rpx;
				background: #404243;
				border-radius: 15rpx;
				position: absolute;
				bottom: 0;
				left: 0;
				right: 0;
				padding-left: 20rpx;
			}
		}

		&_interests {
			width: 100%;
			height: auto;
		}

	}

	.tdu {
		text-decoration: underline;
	}
</style>