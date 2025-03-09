<template>
	<view class="xilu">
		<view class="container plr25 pt30">
			<view class="xilu_vip_nav">
				<image v-if="web_url" :src="web_url+'/uniapp_image/xilu_profile_card.png'" mode="aspectFill" class="xilu_vip_nav_bg">
				</image>
				<view class="xilu_vip_nav_view plr30 flex-box">
					<view class="flex-grow-1">
						<view class="fs28 colf lh28">可提现金额（元）</view>
						<view class="fs30 mt20 colf lh50">¥<text class="fs50">{{coachInfo.accountInfo.account || 0.00}}</text>
						</view>
						<view class="fs28 colf lh28 mt40">全部金额（元）</view>
						<view class="fs30 colf lh34 mt15">¥<text class="fs34">{{coachInfo.accountInfo.account || 0.00}}</text>
						</view>
					</view>
					<view @tap="withdraw()" class="xilu_go">提现</view>
				</view>
			</view>
			<view class="ptb40 fs36 colf lh36">收入明细</view>
			<view class="pt10 pb30">
				<template v-if="list.length > 0">
					<view class="xilu_income_item flex-box" v-for="(vo,index) in list">
						<view class="flex-grow-1">
							<view class="fs32 colf lh46">{{vo.title || ''}}</view>
							<view class="mt20 fs28 col9 lh40">{{vo.createtime_txt || ''}}</view>
						</view>
						<view v-if="vo.cash_type == 1" class="col2 fs40 lh56">+{{vo.cash_price || 0}}</view>
						<view v-else class="colf fs40 lh56">-{{vo.cash_price || 0}}</view>
					</view>
				</template>
				<template v-else>
					<empty-data :tips="'暂无收入明细'" :lineHeight="300"></empty-data>
				</template>

			</view>
		</view>
	</view>
</template>

<script>
	const app = getApp();
	const webConfig = require("@/util/config");
	export default {
		data() {
			return {
				list: [],
				page: 1,
				total_count: 0,
				coachInfo: null,
				web_url:''
			}
		},
		methods: {
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
			},
			//获取数据
			getLists() {
				let _this = this;
				this.$http({
					url: '/addons/xilufitness/coach/getCashList',
					data: {
						page: _this.page,
						id: _this.coachInfo.id || 0
					},
					method: 'get'
				}).then(res => {
					if (res.code == 1) {
						if (_this.page > 1) {
							_this.list.push(...res.data.list);
						} else {
							_this.list = res.data.list;
						}
						_this.total_count = res.data.total_count;
					}
				}).catch(error => {
					console.log('cashLitsError', error);
				})
			},
			//清除数据
			clearData() {
				this.page = 1;
				this.list = [];
				this.total_count = 0;
				this.getLists();
			},
			//提现
			withdraw() {
				let _this = this;
				this.$api.navigate('../payouts/payouts', function(res) {
					res.eventChannel.emit('coachData', {
						coachInfo: _this.coachInfo
					});
					res.eventChannel.on('coachWithdraw', function() {
						_this.getInfos();
						_this.clearData();
					});
				})
			}
		},
		onLoad() {
			this.web_url = webConfig.base_url || '';
			let eventChannel = this.getOpenerEventChannel();
			let _this = this;
			eventChannel.on('userData', function(params) {
				console.log('params', params);
				_this.coachInfo = params.coachInfo || null;
				_this.getLists();
			});
		},
		onReachBottom() {
			if (this.total_count > this.list.length) {
				this.page = this.page + 1;
				this.getLists();
			}
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_vip_nav {
			width: 700rpx;
			height: 295rpx;
			position: relative;

			&_bg {
				width: 700rpx;
				height: 295rpx;
				position: relative;
			}

			&_view {
				width: 700rpx;
				height: 295rpx;
				position: absolute;
				top: 0;
				left: 0;
			}
		}

		&_go {
			width: 150rpx;
			height: 70rpx;
			line-height: 70rpx;
			text-align: center;
			font-size: 30rpx;
			font-weight: 400;
			color: #FFFFFF;
			background: #D7A35F;
			border-radius: 40rpx;
		}

		&_income_item {
			width: 700rpx;
			height: 155rpx;
			background: #404243;
			border-radius: 20rpx;
			margin-top: 30rpx;
			padding-left: 30rpx;
			padding-right: 30rpx;
		}
	}
</style>