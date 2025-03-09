<template>
	<view class="xilu">
		<view class="container pt140">
			<image src="@/static/images/xilu_success.png" mode="aspectFill" class="xilu_success"></image>
			<view class="mt40 fs34 colf lh48 fw500 tc">支付成功！</view>
			<view class="mt300 flex-box flex-center">
				<view @click="back_home()" class="xilu_btn1">返回首页</view>
				<view @click="check_order()" class="xilu_btn2 ml30">查看预约</view>
			</view>



		</view>
	</view>
</template>

<script>
	const app = getApp();
	export default {
		data() {
			return {

			}
		},
		methods: {
			back_home() {
				this.$api.reLaunch('../index/index');
			},
			check_order() {
				this.$api.reLaunch('../booking/booking');
			}
		}
	}
</script>

<style lang="scss" scoped>
	.pt140 {
		padding-top: 140rpx;
	}

	.xilu {
		&_success {
			width: 186rpx;
			height: 180rpx;
			display: block;
			margin-left: auto;
			margin-right: auto;
		}

		&_btn1 {
			width: 300rpx;
			height: 110rpx;
			line-height: calc(110rpx - 2px);
			text-align: center;
			font-size: 34rpx;
			font-weight: 400;
			color: #FFCF00;
			border-radius: 55rpx;
			border: 1px solid #FFCF00;
		}

		&_btn2 {
			width: 300rpx;
			height: 110rpx;
			line-height: 110rpx;
			text-align: center;
			font-size: 34rpx;
			font-weight: 400;
			color: #FFFFFF;
			background: #404243;
			border-radius: 55rpx;
		}
	}

	.mt300 {
		margin-top: 300rpx;
	}
</style>