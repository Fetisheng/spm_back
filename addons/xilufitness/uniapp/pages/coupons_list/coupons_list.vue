<template>
	<view class="xilu">
		<view class="container">
			<view class="page-head bg-normal p30">
				<view class="fs30 col9 lh42">可用的代金劵</view>
			</view>
			<view class="page-body plr30">
				<template v-if="list.length > 0">

					<view class="xilu_item" v-for="(vo,index) in list">
						<image src="@/static/images/xilu_coupons.png" mode="aspectFill" class="xilu_item_bg"></image>
						<view class="xilu_item_view flex-box">
							<view class="flex-grow-1">
								<view class="fs32 fw500 col1 lh44 pl5 m-ellipsis">{{vo.title || ''}}</view>
								<view class="fs30 col89 lh50 mt10 pl5"><text
										class="fs50 col3 fwb mr10">¥{{vo.discount_amount || 0}}</text>满{{vo.meet_amount || 0}}可用
								</view>
								<view class="fs28 col89 lh40 mt10">有效期：{{vo.expire_time || ''}}(剩余{{vo.expire_day || ''}}天)</view>
							</view>
							<view @click.stop="useCoupon(vo)" class="xilu_item_btn">立即使用</view>
						</view>
					</view>

				</template>

				<template v-else>
					<empty-data :tips="'暂无优惠券'" :lineHeight="100"></empty-data>
				</template>

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
				list: []
			}
		},
		methods: {
			useCoupon(info){
				eventChannel.emit("reloadCoupon",{
					couponInfo:info
				});
				this.$api.back();
			}
		},
		onLoad() {
			let token = this.$api.getCache('token');
			let _this = this;
			if (!token) {
				this.$api.toast('请先登录')
				this.$api.back(2000);
			} else {
				eventChannel = this.getOpenerEventChannel();
				eventChannel.on("chooseCoupon",function(params){
					_this.list = params.coupons_list || [];
				});
			}
		},
		onShareAppMessage() {
		
		}
		
	}
</script>

<style lang="scss" scoped>
	.xilu {
		&_item {
			position: relative;
			width: 690rpx;
			height: 200rpx;
			margin-bottom: 30rpx;

			&_bg {
				position: relative;
				width: 690rpx;
				height: 200rpx;
				z-index: 1;
			}

			&_view {
				position: absolute;
				width: 690rpx;
				height: 200rpx;
				top: 0;
				left: 0;
				right: 0;
				z-index: 2;
				padding: 0 34rpx 0 25rpx;
			}

			&_btn {
				width: 132rpx;
				height: 60rpx;
				line-height: 60rpx;
				text-align: center;
				background: #0E0E0F;
				border-radius: 10rpx;
				font-size: 28rpx;
				font-weight: 400;
				color: #FFCF00;
			}
		}
	}

	.page-body {
		padding-top: 102rpx;
	}
</style>