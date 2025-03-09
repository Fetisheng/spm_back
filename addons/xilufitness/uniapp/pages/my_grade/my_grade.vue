<template>
	<view class="xilu">
		<view class="container">
			<view class="pt70 plr25">
				
				<image :src="'../../static/images/level'+(coachInfo.coach_group_id || 1)+'.png'" mode="aspectFill" class="xilu_level_img"></image>
				<!-- <image src="@/static/images/level3.png" mode="aspectFill" class="xilu_level_img"></image> -->
				<view class="mt20 fs30 colf fw500 tc">{{coachInfo.group_name || '初级教练'}}</view>
				<view class="mt70 xilu_level_item flex-box">
					<view class="tc">
						<image src="@/static/images/level1_icon.png" mode="widthFix" class="ico34"></image>
						<view class="fs28 lh30 mt10 cold2">初级教练</view>
					</view>
					<view class="flex-grow-1 plr30">
						<!-- <view class="fs30 lh30 colf">升到中级教练还需150积分</view> -->
						<view class="xilu_progress_nav mt30">
							<view class="xilu_progress_nav_active" :style="{'width':width1 +'%'}"></view>
						</view>
					</view>
					<view class="tc">
						<image src="@/static/images/level2_icon.png" mode="widthFix" class="ico34"></image>
						<view class="fs28 lh30 mt10 colff">中级会员</view>
					</view>
				</view>
				<view class="mt30 xilu_level_item flex-box">
					<view class="tc">
						<image src="@/static/images/level2_icon.png" mode="widthFix" class="ico34"></image>
						<view class="fs28 lh30 mt10 colff">中级会员</view>
					</view>
					<view class="flex-grow-1 plr30">
						<!-- <view class="fs30 lh30 colf">升到高级教练还需1500积分</view> -->
						<view class="xilu_progress_nav mt30">
							<view class="xilu_progress_nav_active" :style="{'width':width1 +'%'}"></view>
						</view>
					</view>
					<view class="tc">
						<image src="@/static/images/level3_icon.png" mode="widthFix" class="ico34"></image>
						<view class="fs28 lh30 mt10 cold7">高级教练</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	var eventChannel = null;
	export default {
		data() {
			return {
				width1: 0,
				coachInfo: null,
				list: []
			}
		},
		methods: {
			
		},
		onLoad() {
			let _this = this;
			eventChannel = this.getOpenerEventChannel();
			eventChannel.on("coachData", function(params) {
				_this.coachInfo = params.coachInfo || '';
			});
		},
		onShareAppMessage() {
		
		}
	}
</script>

<style lang="scss" scoped>
	.pt70 {
		padding-top: 70rpx;
	}

	.xilu {
		&_level {
			&_img {
				width: 120rpx;
				height: 120rpx;
				display: block;
				margin-left: auto;
				margin-right: auto;
			}

			&_item {
				width: 700rpx;
				height: 202rpx;
				background: #292B2C;
				border-radius: 20rpx;
				padding-left: 20rpx;
				padding-right: 20rpx;
			}
		}

		.cold2 {
			color: #D2A36F;
		}

		.colff {
			color: #FFD160;
		}

		.cold7 {
			color: #D74B07;
		}

		&_progress_nav {
			width: 370rpx;
			height: 12rpx;
			background: rgba(255, 255, 255, 0.43);
			border-radius: 6rpx;
			position: relative;

			&_active {
				height: 12rpx;
				background: #FFFFFF;
				border-radius: 6rpx;
				position: absolute;
				left: 0;
				top: 0;
				bottom: 0;
				z-index: 2;
			}
		}
	}
</style>