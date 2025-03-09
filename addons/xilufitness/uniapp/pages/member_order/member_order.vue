<template>
	<view class="xilu">
		<view class="container plr20">
			<view class="pb30">
				<template v-if="list.length > 0">
					<view class="xilu_item flex-box" v-for="(vo,keys) in list">
						<view class="flex-grow-1">
							<view class="fs32 colf lh44">会员充值</view>
							<view class="mt20 fs28 col9 lh40">{{vo.pay_time || ''}}</view>
						</view>
						<view class="col2 fs40">+{{vo.total_amount || 0}}</view>
					</view>
				</template>

				<template v-else>
					<empty-data :tips="'暂无订单数据'" :lineHeight="150"></empty-data>
				</template>

			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				page: 1,
				total_count: 0
			}
		},
		methods: {
			//订单明细
			getList() {
				let _this = this;
				_this.$http({
					url: '/addons/xilufitness/order/getOrderList',
					data: {
						page: _this.page,
						order_type: 0
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
					console.log('rechargeOrderError', error);
				})
			}
		},
		onLoad() {
			this.getList();
		},
		onReachBottom() {
			let list = this.list;
			let total_count = this.total_count;
			if (total_count > list.length) {
				this.page = this.page + 1;
				this.getList();
			}
		},
		onShareAppMessage() {

		}
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_item {
			width: 700rpx;
			height: 155rpx;
			background: #404243;
			border-radius: 20rpx;
			margin-top: 30rpx;
			padding: 0 30rpx;
		}
	}
</style>