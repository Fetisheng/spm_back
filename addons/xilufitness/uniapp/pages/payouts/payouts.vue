<template>
	<view class="xilu">
		<view class="page-foot bg-normal">
			<view class="xilu_foot_nav" @click="addWithdraw()">
				<view class="btn1">确定</view>
			</view>
		</view>
		<view class="container">
			<view class="xilu_payouts">
				<view class="xilu_payouts_inpnav">
					<view class="fs30 colc lh50 tc h65" v-if="!show" @click="clickInp">¥<text
							class="fs60">{{coachInfo.accountInfo.account || 0.00}}</text>
					</view>
					<input confirm-type="done" @confirm="addWithdraw()" type="digit" class="fs60 colf lh60 tc"
						v-model="withdraw_price" :focus="show" v-else />
				</view>
			</view>
			<view class="xilu_content" v-if="withdraw_content">
				<mp-html :content="withdraw_content"></mp-html>
			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	var eventChannel = null;
	export default {
		data() {
			return {
				show: false,
				value: null,
				coachInfo: null,
				withdraw_price: '',
				withdraw_content: ''
			}
		},
		methods: {
			clickInp() {
				this.show = true
			},
			//提现申请提交
			addWithdraw() {
				let _this = this;
				if (_this.withdraw_price <= 0) {
					this.$api.toast('提现金额需要大于0');
				} else if (this.coachInfo && parseFloat(_this.coachInfo.accountInfo.account) < parseFloat(_this
						.withdraw_price)) {
					this.$api.toast('余额不足');
				} else {
					this.$http({
						url: '/addons/xilufitness/coach/addWithdraw',
						data: {
							id: _this.coachInfo.id || 0,
							withdraw_price: _this.withdraw_price
						},
						method: 'post'
					}).then(res => {
						if (res.code == 1) {
							_this.$api.toast('提交成功，请耐心等待管理员审核通过');
							eventChannel.emit('coachWithdraw', {});
							_this.$api.back(2000);
						}
					}).catch(error => {
						console.log('addWithdrawError', error);
					})
				}
			},
			//获取提现规则
			getTips() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/cms/detail',
					data: {
						is_type: 8
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.withdraw_content = res.data.info.content || '';
					}
				}).catch(error => {
					console.log('withdrawTipsError', error);
				})
			},
			//获取详情
			getInfos() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getCoachInfo',
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						_this.coachInfo = res.data.info;
					}
				}).catch(error => {
					console.log('coachInfoError', error);
				})
			}

		},
		onLoad() {
			let _this = this;
			eventChannel = this.getOpenerEventChannel();
			eventChannel.on('coachData', function(params) {
				_this.coachInfo = params.coachInfo || null;
			});
			this.getTips();
			this.getInfos();
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_foot_nav {
			padding: 10rpx 75rpx 30rpx;
		}

		&_payouts {
			padding-top: 150rpx;
			padding-left: 30rpx;
			padding-right: 30rpx;

			&_inpnav {
				padding: 30rpx 0;
				width: 520rpx;

				border-bottom: 1px solid #CCCCCC;
				text-align: center;
				margin-left: auto;
				margin-right: auto;
			}

			input {
				height: 65rpx;
				width: 520rpx;
			}
		}

		&_content {
			margin-top: 193rpx;
			white-space: pre-line;
			font-size: 30rpx;
			font-weight: 400;
			color: #999999;
			line-height: 42rpx;
			padding-left: 30rpx;
			padding-right: 30rpx;
		}
	}

	.fs60 {
		font-size: 60rpx;
	}

	.h65 {
		height: 65rpx;
	}
</style>